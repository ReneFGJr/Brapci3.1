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
    $db->query('CREATE TEMPORARY TABLE cited_article (id_ca BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ca_normalized BIGINT, ca_rdf INT, ca_year INT, ca_year_origem INT, ca_doi VARCHAR(120), ca_text TEXT)');
    $db->query('CREATE TEMPORARY TABLE cached (pensador_id INT UNSIGNED, cited_article_id BIGINT UNSIGNED, cache_date DATE, search_term VARCHAR(255), created_at DATETIME, PRIMARY KEY (pensador_id, cited_article_id, cache_date)) ENGINE=InnoDB');
    $db->table('cited_article')->insertBatch([
        ['ca_normalized' => 4, 'ca_rdf' => 100, 'ca_year_origem' => 2020, 'ca_text' => null],
        ['ca_normalized' => 4, 'ca_rdf' => 100, 'ca_year_origem' => 2020, 'ca_text' => null],
        ['ca_normalized' => 4, 'ca_rdf' => 200, 'ca_year_origem' => 2010, 'ca_text' => null],
        ['ca_normalized' => 4, 'ca_rdf' => 300, 'ca_year_origem' => 0, 'ca_text' => null],
        ['ca_normalized' => 4, 'ca_rdf' => 0, 'ca_year_origem' => 1990, 'ca_text' => null],
        ['ca_normalized' => 3, 'ca_rdf' => 400, 'ca_year_origem' => 2005, 'ca_text' => null],
        ['ca_normalized' => 1, 'ca_rdf' => 0, 'ca_year_origem' => 0, 'ca_text' => 'SILVA. Obra A'],
        ['ca_normalized' => 2, 'ca_rdf' => 0, 'ca_year_origem' => 0, 'ca_text' => 'Obra de Silva, João'],
        ['ca_normalized' => 4, 'ca_rdf' => 0, 'ca_year_origem' => 0, 'ca_text' => 'SILVA. Já vinculada'],
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
    $db->table('cited_normalize')->insertBatch([
        ['id_ca' => 9001, 'ca_text' => 'Outro título', 'ca_doi' => 'https://doi.org/10.1234/PRIORITY'],
        ['id_ca' => 9002, 'ca_text' => 'Título procurado', 'ca_doi' => '10.1234/other'],
    ]);
    $normalizedModel = new App\Models\DOI\Cited_Normalize($db);
    $candidates = $normalizedModel->findCandidates([
        ['ca_doi' => ''], ['ca_doi' => 'doi:10.1234/priority'],
    ], 'Título procurado');
    verifyLink(array_column($candidates, 'id_ca') == [9001], 'DOI de qualquer referência tem prioridade sobre correspondência por texto');
    verifyLink($candidates[0]['match_reason'] === 'Mesmo DOI', 'Motivo da correspondência por DOI');
    verifyLink(array_column($normalizedModel->findCandidates([['ca_doi' => '10.1234/missing']], 'Título procurado'), 'id_ca') == [9002], 'Sem DOI correspondente busca pelo texto');
    verifyLink(array_column($normalizedModel->findCandidates([['ca_doi' => '']], 'Título procurado'), 'id_ca') == [9002], 'Referência sem DOI busca pelo texto');
    $db->table('cited_normalize')->whereIn('id_ca', [9001, 9002])->delete();
    $db->query('CREATE TEMPORARY TABLE test_citing_dataset (ID INT, TITLE TEXT, AUTHORS TEXT, YEAR INT)');
    $db->table('test_citing_dataset')->insertBatch([
        ['ID' => 100, 'TITLE' => 'Título do dataset', 'AUTHORS' => 'Autor dataset', 'YEAR' => 2000],
        ['ID' => 100, 'TITLE' => 'Título do dataset', 'AUTHORS' => 'Autor dataset', 'YEAR' => 2000],
        ['ID' => 200, 'TITLE' => 'Segundo trabalho', 'AUTHORS' => 'Outro autor', 'YEAR' => 2010],
    ]);
    $model = new class($db) extends App\Models\Pensador\CitedPensador {
        protected string $datasetTable = 'test_citing_dataset';
        protected string $authorDataTable = 'test_author_data';
        protected string $authorClassTable = 'test_author_class';
        protected string $thinkerTable = 'test_thinkers';
    };
    $db->query('CREATE TEMPORARY TABLE test_author_data (d_r1 INT, d_r2 INT, d_p INT)');
    $db->query('CREATE TEMPORARY TABLE test_author_class (id_c INT, c_class VARCHAR(100))');
    $db->query('CREATE TEMPORARY TABLE test_thinkers (id INT, nome VARCHAR(100))');
    $db->table('test_author_class')->insertBatch([
        ['id_c' => 1, 'c_class' => 'hasAuthor'], ['id_c' => 2, 'c_class' => 'hasSubject'],
    ]);
    $db->table('test_author_data')->insertBatch([
        ['d_r1' => 100, 'd_r2' => 7, 'd_p' => 1],
        ['d_r1' => 100, 'd_r2' => 7, 'd_p' => 1],
        ['d_r1' => 200, 'd_r2' => 7, 'd_p' => 1],
        ['d_r1' => 300, 'd_r2' => 7, 'd_p' => 2],
        ['d_r1' => 400, 'd_r2' => 8, 'd_p' => 1],
    ]);
    $db->table('test_thinkers')->insertBatch([
        ['id' => 10, 'nome' => 'Silva'], ['id' => 20, 'nome' => 'Souza'],
    ]);
    $db->table('cited_pensador')->insert(['pensador_id' => 20, 'cited_normalize_id' => 3]);
    $db->table('cited_article')->insert(['ca_normalized' => 3, 'ca_rdf' => 200]);
    verifyLink($model->thinkersCitedByAuthor(7) === [
        ['id' => 10, 'nome' => 'Silva', 'total' => 3],
        ['id' => 20, 'nome' => 'Souza', 'total' => 1],
    ], 'Ranking por referências, sem multiplicar vínculos RDF e sem incluir outros autores/propriedades');
    verifyLink($model->thinkersCitedByAuthor(999) === [], 'Autor sem citações retorna lista vazia');
    $db->table('cited_article')->where('ca_normalized', 3)->where('ca_rdf', 200)->delete();
    verifyLink(array_column($model->linkedWorks(10), 'id_ca') == [4], 'Obras apenas do pensador selecionado');
    verifyLink($model->linkedWorks(999) === [], 'Pensador sem obras');
    verifyLink((int) $model->linkedWorks(10)[0]['cited_by'] === 0, 'Leitura não recalcula');
    $model->citingWorks(4);
    verifyLink((int) $model->linkedWorks(10)[0]['cited_by'] === 0, 'Citantes não recalculam');
    $model->recalculateForThinker(10);
    verifyLink((int) $model->linkedWorks(10)[0]['cited_by'] === 3, 'Contagem de trabalhos distintos');
    verifyLink(array_column($model->citingWorks(4), 'ca_rdf') == [100, 200, 300], 'Ordem cronológica, sem duplicação, sem ano ao final');
    verifyLink($model->citingWorks(2) === [], 'Obra sem citações');
    verifyLink(count($model->groupedReferences(4)) === 6, 'Referências agrupadas incluem duplicatas do mesmo trabalho e registros sem RDF');
    verifyLink($model->groupedReferences(999) === [], 'ID sem referências agrupadas');
    verifyLink(array_values(array_unique(array_column($model->groupedReferences(4), 'ca_rdf'))) == [100, 200, 300, 0], 'Agrupamento usa ca_normalized da obra selecionada');
    verifyLink((int) $db->table('cited_normalize')->where('id_ca', 4)->get()->getRowArray()['ca_cited'] === 3, 'Contagem persistida');
    verifyLink($model->citingWorks(4)[0]['title'] === 'Título do dataset', 'Metadados pelo ID do dataset');
    $db->table('cited_article')->where('ca_rdf', 100)->delete();
    $model->refreshCitationCounts([4]);
    verifyLink((int) $db->table('cited_normalize')->where('id_ca', 4)->get()->getRowArray()['ca_cited'] === 2, 'Recontagem após exclusão');
    $person = ['id' => 10, 'nome' => 'João da Silva', 'nome_citacao' => 'SILVA, João'];
    verifyLink($model::citationName($person) === 'SILVA, João', 'Busca preserva o nome de citação completo');
    verifyLink($model::citationName(['nome' => 'João da Silva']) === '', 'Busca não usa o nome civil');
    $db->table('cited_article')->where('ca_normalized', 1)->update(['ca_text' => 'SILVA, João. Obra A']);
    $db->table('cited_article')->where('ca_normalized', 4)->where('ca_rdf', 0)
        ->where('ca_text IS NOT NULL', null, false)->update(['ca_text' => 'SILVA, João. Já vinculada']);
    verifyLink($model::surname($person) === 'SILVA', 'Sobrenome de citação');
    verifyLink($model::surname(['nome' => 'João da Silva']) === 'Silva', 'Sobrenome pelo nome');
    verifyLink(count($model->candidates($person)) === 2, 'Texto/autores e exclusão apenas para este pensador');
    verifyLink($db->table('cached')->where('pensador_id', 10)->where('cache_date', date('Y-m-d'))->countAllResults() === 3, 'Cache diário criado a partir de cited_article.ca_text');
    verifyLink($model->linkSelected($person, ['1', '1', '3', '999']) === 1, 'Revalidar seleção e deduplicar');
    verifyLink($model->linkSelected($person, ['1']) === 0, 'Reenvio idempotente');
    verifyLink(count($model->candidates($person)) === 1, 'Remover vinculadas da seleção');
    verifyLink($model->linkSelected($person, ['2']) === 1, 'Vincular correspondência por autores');
    verifyLink($model->candidates($person) === [], 'Estado vazio');
    verifyLink(array_column($model->linkedWorks(10), 'id_ca') == [4, 2, 1], 'Obras vinculadas ordenadas por ano');
    $db->table('cited_normalize')->where('id_ca', 2)->update(['ca_doi' => '10.1234/test']);
    $db->table('cited_article')->insertBatch([
        ['ca_normalized' => null, 'ca_rdf' => 500, 'ca_year_origem' => 2024, 'ca_doi' => 'https://doi.org/10.1234/TEST'],
        ['ca_normalized' => 0, 'ca_rdf' => 500, 'ca_year_origem' => 2024, 'ca_doi' => 'doi:10.1234/test'],
        ['ca_normalized' => 0, 'ca_rdf' => 501, 'ca_year_origem' => 2025, 'ca_doi' => '10.1234/test'],
        ['ca_normalized' => 3, 'ca_rdf' => 502, 'ca_year_origem' => 2025, 'ca_doi' => '10.1234/test'],
    ]);
    $model->refreshCitationCounts([2]);
    verifyLink((int) $db->table('cited_normalize')->where('id_ca', 2)->get()->getRowArray()['ca_cited'] === 2, 'DOIs sem vínculo contados sem duplicar ou sobrescrever vínculo explícito');
    verifyLink($model->citingWorks(2) === [], 'Lista não associa citações apenas pelo DOI');
    $db->table('cited_article')->insertBatch([
        ['ca_normalized' => 2, 'ca_rdf' => 503, 'ca_year_origem' => 2026, 'ca_doi' => ''],
        ['ca_normalized' => 2, 'ca_rdf' => 503, 'ca_year_origem' => 2026, 'ca_doi' => null],
    ]);
    verifyLink(array_column($model->citingWorks(2), 'ca_rdf') == [503], 'Vínculo por ca_normalized sem DOI e sem duplicar trabalho');
    $db->table('cited_article')->where('ca_rdf', 503)->delete();
    $db->query('ALTER TABLE cited_normalize DROP COLUMN ca_cited');
    unset($db->dataCache['field_names']['cited_normalize']);
    $model->refreshCitationCounts([4]);
    verifyLink($model->linkedWorks(10)[0]['cited_by'] === null, 'Sem coluna não calcula no carregamento');
    verifyLink(count($model->citingWorks(4)) === 2, 'Página de citações sem coluna ca_cited');
    try {
        $model->linkSelected($person, [['1']]);
        throw new LogicException('Seleção inválida aceita');
    } catch (InvalidArgumentException $expected) {}
    try {
        $model->groupLinkedWorks(10, [1, 999], 1);
        throw new LogicException('Agrupamento de obra não vinculada aceito');
    } catch (InvalidArgumentException $expected) {}
    verifyLink($db->table('cited_normalize')->where('id_ca', 1)->countAllResults() === 1, 'Seleção inválida não altera obras');
    $db->query('ALTER TABLE cited_normalize ADD COLUMN ca_cited INT UNSIGNED NOT NULL DEFAULT 0');
    unset($db->dataCache['field_names']['cited_normalize']);
    $model->groupLinkedWorks(10, [1, 2], 1);
    verifyLink((int) $db->table('cited_normalize')->where('id_ca', 1)->get()->getRowArray()['ca_cited'] === 2, 'Agrupamento recalcula citantes distintos');
    verifyLink($db->table('cited_normalize')->where('id_ca', 2)->countAllResults() === 0, 'Registro duplicado removido');
    verifyLink($db->table('cited_pensador')->where('cited_normalize_id', 2)->countAllResults() === 0, 'Vínculos duplicados removidos');
    verifyLink($db->table('cited_pensador')->where('pensador_id', 20)->where('cited_normalize_id', 1)->countAllResults() === 1, 'Vínculo do outro pensador preservado');
    verifyLink($db->table('cited_article')->whereIn('ca_rdf', [500, 501])->where('ca_normalized', 1)->countAllResults() === 3, 'Citações por DOI transferidas ao ID mantido');
    verifyLink($db->table('cited_article')->where('ca_rdf', 502)->where('ca_normalized', 3)->countAllResults() === 1, 'Vínculo explícito de outra obra preservado');
    $model->unlinkWork(10, 1);
    verifyLink($db->table('cited_pensador')->where('pensador_id', 10)->where('cited_normalize_id', 1)->countAllResults() === 0, 'Desvincular apenas pensador selecionado');
    verifyLink($db->table('cited_normalize')->where('id_ca', 1)->countAllResults() === 1, 'Desvincular preserva a obra');
    verifyLink($db->table('cited_pensador')->where('pensador_id', 20)->where('cited_normalize_id', 1)->countAllResults() === 1, 'Desvincular preserva outros pensadores');
    echo "OK: candidatos, citações, seleção, agrupamento e desvinculação.\n";
} finally {
    $db->query('DROP TEMPORARY TABLE IF EXISTS test_author_data');
    $db->query('DROP TEMPORARY TABLE IF EXISTS test_author_class');
    $db->query('DROP TEMPORARY TABLE IF EXISTS test_thinkers');
    $db->query('DROP TEMPORARY TABLE IF EXISTS test_citing_dataset');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_article');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cached');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_pensador');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_normalize');
    $db->close();
}
