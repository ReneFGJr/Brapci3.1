<?php
// Executar: php tests/regression/CitedPensadorTest.php
// Usa somente tabelas TEMPORARY, isoladas da base persistente.
define('FCPATH', __DIR__ . '/../../public/');
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
class CitedPensadorTestBoot extends CodeIgniter\Boot
{
    public static function start($paths): void
    {
        static::definePathConstants($paths);
        static::loadDotEnv($paths);
        static::defineEnvironment();
        static::bootTest($paths);
    }
}
CitedPensadorTestBoot::start($paths);
$db = Config\Database::connect('brapci_cited', false);
function verifyLink(bool $condition, string $message): void
{
    if (!$condition) { throw new RuntimeException($message); }
}
try {
    $db->query('CREATE TEMPORARY TABLE cited_normalize (id_ca BIGINT UNSIGNED PRIMARY KEY, ca_text TEXT, ca_authors TEXT, ca_year INT, ca_doi VARCHAR(120), ca_cited INT UNSIGNED NOT NULL DEFAULT 0)');
    $db->query('CREATE TEMPORARY TABLE cited_pensador (pensador_id INT UNSIGNED, cited_normalize_id BIGINT UNSIGNED, PRIMARY KEY (pensador_id, cited_normalize_id)) ENGINE=InnoDB');
    $db->query('CREATE TEMPORARY TABLE cited_article (ca_normalized BIGINT, ca_rdf INT, ca_year_origem INT)');
    $db->table('cited_article')->insertBatch([
        ['ca_normalized' => 4, 'ca_rdf' => 100, 'ca_year_origem' => 2020],
        ['ca_normalized' => 4, 'ca_rdf' => 100, 'ca_year_origem' => 2020],
        ['ca_normalized' => 4, 'ca_rdf' => 200, 'ca_year_origem' => 2010],
        ['ca_normalized' => 4, 'ca_rdf' => 300, 'ca_year_origem' => 0],
        ['ca_normalized' => 4, 'ca_rdf' => 0, 'ca_year_origem' => 1990],
        ['ca_normalized' => 3, 'ca_rdf' => 400, 'ca_year_origem' => 2005],
    ]);
    $db->table('cited_normalize')->insertBatch([
        ['id_ca' => 1, 'ca_text' => 'SILVA. Obra A', 'ca_authors' => '', 'ca_year' => 2000],
        ['id_ca' => 2, 'ca_text' => 'Obra B', 'ca_authors' => 'Silva, João', 'ca_year' => 2001],
        ['id_ca' => 3, 'ca_text' => 'Outro autor', 'ca_authors' => 'Souza', 'ca_year' => 2002],
        ['id_ca' => 4, 'ca_text' => 'Silva. Já vinculada', 'ca_authors' => '', 'ca_year' => 2003],
    ]);
    $db->table('cited_pensador')->insertBatch([
        ['pensador_id' => 10, 'cited_normalize_id' => 4],
        ['pensador_id' => 20, 'cited_normalize_id' => 1],
    ]);
    $db->query('CREATE TEMPORARY TABLE test_citing_dataset (ID INT, TITLE TEXT, AUTHORS TEXT, YEAR INT)');
    $db->table('test_citing_dataset')->insertBatch([
        ['ID' => 100, 'TITLE' => 'Título do dataset', 'AUTHORS' => 'Autor dataset', 'YEAR' => 2000],
        ['ID' => 100, 'TITLE' => 'Título do dataset', 'AUTHORS' => 'Autor dataset', 'YEAR' => 2000],
        ['ID' => 200, 'TITLE' => 'Segundo trabalho', 'AUTHORS' => 'Outro autor', 'YEAR' => 2010],
    ]);
    $model = new class($db) extends App\Models\Pensador\CitedPensador {
        protected string $datasetTable = 'test_citing_dataset';
    };
    verifyLink(array_column($model->linkedWorks(10), 'id_ca') == [4], 'Obras apenas do pensador selecionado');
    verifyLink($model->linkedWorks(999) === [], 'Pensador sem obras');
    verifyLink((int) $model->linkedWorks(10)[0]['cited_by'] === 0, 'Leitura não recalcula');
    $model->citingWorks(4);
    verifyLink((int) $model->linkedWorks(10)[0]['cited_by'] === 0, 'Citantes não recalculam');
    $model->recalculateForThinker(10);
    verifyLink((int) $model->linkedWorks(10)[0]['cited_by'] === 3, 'Contagem de trabalhos distintos');
    verifyLink(array_column($model->citingWorks(4), 'ca_rdf') == [100, 200, 300], 'Ordem cronológica, sem duplicação, sem ano ao final');
    verifyLink($model->citingWorks(2) === [], 'Obra sem citações');
    verifyLink((int) $db->table('cited_normalize')->where('id_ca', 4)->get()->getRowArray()['ca_cited'] === 3, 'Contagem persistida');
    verifyLink($model->citingWorks(4)[0]['title'] === 'Título do dataset', 'Metadados pelo ID do dataset');
    $db->table('cited_article')->where('ca_rdf', 100)->delete();
    $model->refreshCitationCounts([4]);
    verifyLink((int) $db->table('cited_normalize')->where('id_ca', 4)->get()->getRowArray()['ca_cited'] === 2, 'Recontagem após exclusão');
    $person = ['id' => 10, 'nome' => 'João da Silva', 'nome_citacao' => 'SILVA, João'];
    verifyLink($model::surname($person) === 'SILVA', 'Sobrenome de citação');
    verifyLink($model::surname(['nome' => 'João da Silva']) === 'Silva', 'Sobrenome pelo nome');
    verifyLink(count($model->candidates($person)) === 2, 'Texto/autores e exclusão apenas para este pensador');
    verifyLink($model->linkSelected($person, ['1', '1', '3', '999']) === 1, 'Revalidar seleção e deduplicar');
    verifyLink($model->linkSelected($person, ['1']) === 0, 'Reenvio idempotente');
    verifyLink(count($model->candidates($person)) === 1, 'Remover vinculadas da seleção');
    verifyLink($model->linkSelected($person, ['2']) === 1, 'Vincular correspondência por autores');
    verifyLink($model->candidates($person) === [], 'Estado vazio');
    verifyLink(array_column($model->linkedWorks(10), 'id_ca') == [4, 2, 1], 'Obras vinculadas ordenadas por ano');
    $db->query('ALTER TABLE cited_normalize DROP COLUMN ca_cited');
    unset($db->dataCache['field_names']['cited_normalize']);
    $model->refreshCitationCounts([4]);
    verifyLink($model->linkedWorks(10)[0]['cited_by'] === null, 'Sem coluna não calcula no carregamento');
    verifyLink(count($model->citingWorks(4)) === 2, 'Página de citações sem coluna ca_cited');
    try {
        $model->linkSelected($person, [['1']]);
        throw new LogicException('Seleção inválida aceita');
    } catch (InvalidArgumentException $expected) {}
    echo "OK: sobrenome, candidatos, isolamento por pensador, vínculos, duplicação e seleção inválida.\n";
} finally {
    $db->query('DROP TEMPORARY TABLE IF EXISTS test_citing_dataset');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_article');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_pensador');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_normalize');
    $db->close();
}
