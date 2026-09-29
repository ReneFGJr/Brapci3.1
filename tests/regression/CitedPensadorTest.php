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
    $db->query('CREATE TEMPORARY TABLE cited_normalize (id_ca BIGINT UNSIGNED PRIMARY KEY, ca_text TEXT, ca_authors TEXT, ca_year INT, ca_doi VARCHAR(120))');
    $db->query('CREATE TEMPORARY TABLE cited_pensador (pensador_id INT UNSIGNED, cited_normalize_id BIGINT UNSIGNED, PRIMARY KEY (pensador_id, cited_normalize_id)) ENGINE=InnoDB');
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
    $model = new App\Models\Pensador\CitedPensador($db);
    $person = ['id' => 10, 'nome' => 'João da Silva', 'nome_citacao' => 'SILVA, João'];
    verifyLink($model::surname($person) === 'SILVA', 'Sobrenome de citação');
    verifyLink($model::surname(['nome' => 'João da Silva']) === 'Silva', 'Sobrenome pelo nome');
    verifyLink(count($model->candidates($person)) === 2, 'Texto/autores e exclusão apenas para este pensador');
    verifyLink($model->linkSelected($person, ['1', '1', '3', '999']) === 1, 'Revalidar seleção e deduplicar');
    verifyLink($model->linkSelected($person, ['1']) === 0, 'Reenvio idempotente');
    verifyLink(count($model->candidates($person)) === 1, 'Remover vinculadas da seleção');
    verifyLink($model->linkSelected($person, ['2']) === 1, 'Vincular correspondência por autores');
    verifyLink($model->candidates($person) === [], 'Estado vazio');
    try {
        $model->linkSelected($person, [['1']]);
        throw new LogicException('Seleção inválida aceita');
    } catch (InvalidArgumentException $expected) {}
    echo "OK: sobrenome, candidatos, isolamento por pensador, vínculos, duplicação e seleção inválida.\n";
} finally {
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_pensador');
    $db->query('DROP TEMPORARY TABLE IF EXISTS cited_normalize');
    $db->close();
}
