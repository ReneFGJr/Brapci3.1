<?php
// php tests/regression/ThinkerGroupsTest.php — SQLite em memória, sem alterar bases existentes.
putenv('CI_ENVIRONMENT=testing');
define('FCPATH', __DIR__ . '/../../public/');
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
class ThinkerGroupsTestBoot extends CodeIgniter\Boot
{
    public static function start($paths): void
    {
        static::definePathConstants($paths);
        static::loadDotEnv($paths);
        static::defineEnvironment();
        static::bootTest($paths);
    }
}
ThinkerGroupsTestBoot::start($paths);
$db = Config\Database::connect(['DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '', 'DBDebug' => true], false);
function checkGroup(bool $condition, string $message): void
{
    if (!$condition) { throw new RuntimeException($message); }
}
try {
    $db->query('CREATE TABLE pensadores (id INTEGER PRIMARY KEY, nome TEXT)');
    $db->table('pensadores')->insertBatch([['id' => 1, 'nome' => 'Wilson A'], ['id' => 2, 'nome' => 'Wilson B'], ['id' => 3, 'nome' => 'Outro']]);
    require APPPATH . 'Database/Migrations/2026-11-10-120000_CreateThinkerGroups.php';
    class MemoryThinkerGroupsMigration extends App\Database\Migrations\CreateThinkerGroups { protected $DBGroup = null; }
    $migration = new MemoryThinkerGroupsMigration(Config\Database::forge($db));
    $migration->up();
    $model = new App\Models\Pensador\ThinkerGroup($db);
    $id = $model->saveMembers('Wilson', ['1', '2', '1']);
    checkGroup(count($model->members($id)) === 2, 'Membros repetidos não duplicam vínculos');
    $model->saveMembers('', ['2', '3'], $id);
    checkGroup(count($model->members($id)) === 3, 'Adicionar em grupo existente preserva membros');
    foreach ([['', ['1']], ['Nome', []], ['Nome', [['1']]], ['Nome', ['999']], ['Nome', ['-1']]] as [$name, $ids]) {
        try {
            $model->saveMembers($name, $ids);
            throw new RuntimeException('Seleção inválida foi aceita');
        } catch (InvalidArgumentException $expected) {}
    }
    checkGroup($model->countAllResults() === 1, 'Erros não deixam grupos parciais');
    $db->query('CREATE TABLE cited_normalize (id_ca INTEGER PRIMARY KEY, ca_text TEXT, ca_authors TEXT, ca_year INTEGER, ca_doi TEXT)');
    $db->query('CREATE TABLE cited_pensador (pensador_id INTEGER, cited_normalize_id INTEGER)');
    $db->table('cited_normalize')->insertBatch([
        ['id_ca' => 10, 'ca_text' => 'Compartilhada', 'ca_year' => 2020],
        ['id_ca' => 20, 'ca_text' => 'Exclusiva', 'ca_year' => 2021],
        ['id_ca' => 30, 'ca_text' => 'Fora do grupo', 'ca_year' => 2022],
    ]);
    $db->table('cited_pensador')->insertBatch([
        ['pensador_id' => 1, 'cited_normalize_id' => 10],
        ['pensador_id' => 2, 'cited_normalize_id' => 10],
        ['pensador_id' => 2, 'cited_normalize_id' => 20],
        ['pensador_id' => 3, 'cited_normalize_id' => 30],
    ]);
    $worksModel = new App\Models\Pensador\CitedPensador($db);
    $works = $worksModel->linkedWorksForGroup([1, 2]);
    checkGroup(array_column($works, 'id_ca') == [20, 10], 'Obras únicas ordenadas por ano, sem obras de outros membros');
    checkGroup(array_values($works[1]['thinkers']) == [1, 2], 'Obra compartilhada preserva ambos os autores');
    checkGroup($worksModel->linkedWorksForGroup([]) === [], 'Grupo vazio não retorna obras');
    $model->removeMember($id, 2);
    checkGroup(count($model->members($id)) === 2, 'Remover membro altera apenas o grupo');
    checkGroup($db->table('pensadores')->countAllResults() === 3, 'Cadastros preservados');
    checkGroup($db->table('cited_pensador')->countAllResults() === 4, 'Vínculos de obras preservados');
    $migration->down();
    checkGroup(!$db->tableExists('pensador_grupos'), 'Migração reversível');
    echo "OK: grupos, validação, obras compartilhadas e preservação dos cadastros.\n";
} finally {
    $db->close();
}
