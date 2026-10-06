<?php
// Run: php tests/regression/PqGenderTest.php (no database access).
namespace CodeIgniter {
    class Model {
        public function set(...$args) { throw new \RuntimeException('Unexpected write'); }
        public function update(...$args) { throw new \RuntimeException('Unexpected write'); }
    }
}
namespace App\Models\Rdf {
    class RDF {
        public static array $genders = [];
        public static int $reads = 0;
        public function le($id) { self::$reads++; return ['concept' => ['id_cc' => $id], 'data' => self::$genders[$id] ?? []]; }
        public function recover($data, $property) { return $data['data']; }
        public function c($value) { return $value; }
    }
}
namespace App\Models\AI\Person {
    class Genere {
        public function getGenere($name, $debug = true) {
            if ($debug) { throw new \RuntimeException('Debug must be disabled'); }
            return ['Test F' => 'feminino', 'Test M' => 'masculino'][$name] ?? 'indefinido';
        }
    }
}
namespace {
    require __DIR__ . '/../../app/Models/Authority/Person.php';
    require __DIR__ . '/../../app/Models/Api/Endpoint/Pq.php';
    require __DIR__ . '/../../app/Models/Api/Endpoint/Genere.php';
    function get($key) { return $_GET[$key] ?? ''; }
    function verify(bool $ok, string $message): void {
        if (!$ok) { throw new \RuntimeException($message); }
    }
    $person = new \App\Models\Authority\Person();
    foreach ([[['M'], 'M'], [['F'], 'F'], [['M', 'F'], 'X'], [['X', 'X', 'M'], 'X'], [[], 'X'], [[''], 'X']] as [$votes, $expected]) {
        verify($person->check_genere(['concept' => ['id_cc' => 1], 'data' => $votes], [], true) === $expected, 'RDF classification');
    }
    verify($person->check_genere([], [], true) === 'X', 'Missing RDF record');
    \App\Models\Rdf\RDF::$genders = [1 => ['M'], 2 => [], 3 => ['X']];
    verify($person->identifyGender(1, 'Test F') === 'M', 'RDF takes precedence');
    verify($person->identifyGender(2, 'Test F') === 'F', 'Name fallback');
    verify($person->identifyGender(3, '') === 'X', 'Unknown remains X');
    $pq = new \App\Models\Api\Endpoint\Pq();
    $rows = [
        ['bs_rdf_id' => 1, 'bs_nome' => 'Test F', 'bs_genero' => ''],
        ['bs_rdf_id' => 1, 'bs_nome' => 'Test F', 'bs_genero' => null],
        ['bs_rdf_id' => 1, 'bs_nome' => 'Test F', 'bs_genero' => 'F'],
        ['bs_rdf_id' => 2, 'bs_nome' => 'Test F', 'bs_genero' => '  '],
    ];
    $cache = [];
    $before = \App\Models\Rdf\RDF::$reads;
    $result = $pq->identifyMissingGenders($rows, $cache);
    verify(array_column($result, 'bs_genero') === ['M', 'M', 'F', 'F'], 'Missing values and preservation');
    $pq->identifyMissingGenders($rows, $cache);
    verify(\App\Models\Rdf\RDF::$reads - $before === 2, 'Shared per-request cache');
    $api = new \App\Models\Api\Endpoint\Genere();
    $_GET = ['rdf' => '1', 'name' => 'Test F'];
    ob_start(); $api->index('gender', 'check_genere'); $output = ob_get_clean();
    verify(json_decode($output, true) === ['status' => '200', 'gender' => 'M'], 'JSON API');
    $_GET = ['rdf' => '-1'];
    ob_start(); $api->index('gender', 'check_genere'); $output = ob_get_clean();
    verify(http_response_code() === 422 && json_decode($output, true)['status'] === '422', 'Invalid input');
    echo "PASS: RDF votes, name fallback, preservation, caching, JSON and validation; no writes\n";
}
