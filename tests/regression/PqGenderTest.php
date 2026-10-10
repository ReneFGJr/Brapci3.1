<?php
// Run: php tests/regression/PqGenderTest.php (no database access).
namespace CodeIgniter {
    class Model {
        public function set(...$args) { throw new \RuntimeException('Unexpected write'); }
        public function update(...$args) { throw new \RuntimeException('Unexpected write'); }
    }
}
namespace App\Models\RDF2 {
    class RDFdata {
        public static array $genders = [];
        public static int $reads = 0;
        public function genderLabels($id) { self::$reads++; return array_map(fn($g) => ['Property' => 'hasGender', 'Caption' => $g], self::$genders[$id] ?? []); }
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
namespace App\Models\PQ {
    class Bolsistas {
        public static array $saved = [];
        public function saveMissingGender(int $id, string $gender): bool {
            self::$saved[] = [$id, $gender];
            return true;
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
        $data = array_map(fn($g) => ['Property' => 'hasGender', 'Caption' => $g], $votes);
        verify($person->check_genere(['data' => $data], [], true) === $expected, 'RDF classification');
    }
    verify($person->check_genere([], [], true) === 'X', 'Missing RDF record');
    \App\Models\RDF2\RDFdata::$genders = [1 => ['Masculino'], 2 => [], 3 => ['X']];
    verify($person->identifyGender(1, 'Test F') === 'M', 'RDF takes precedence');
    verify($person->identifyGender(2, 'Test F') === 'F', 'Name fallback');
    verify($person->identifyGender(3, '') === 'X', 'Unknown remains X');
    $pq = new \App\Models\Api\Endpoint\Pq();
    $rows = [
        ['bs_rdf_id' => 1, 'bs_nome' => 'Test F', 'bs_genero' => ''],
        ['id_bs' => 10, 'bs_rdf_id' => 1, 'bs_nome' => 'Test F', 'bs_genero' => null],
        ['bs_rdf_id' => 1, 'bs_nome' => 'Test F', 'bs_genero' => 'F'],
        ['bs_rdf_id' => 2, 'bs_nome' => 'Test F', 'bs_genero' => '  '],
    ];
    $cache = [];
    $before = \App\Models\RDF2\RDFdata::$reads;
    $result = $pq->identifyMissingGenders($rows, $cache);
    verify(array_column($result, 'bs_genero') === ['M', 'M', 'F', 'F'], 'Missing values and preservation');
    verify(\App\Models\PQ\Bolsistas::$saved === [[10, 'M']], 'Only NULL gender is persisted by scholar ID');
    $pq->identifyMissingGenders($rows, $cache);
    verify(\App\Models\RDF2\RDFdata::$reads - $before === 2, 'Shared per-request cache');
    $api = new \App\Models\Api\Endpoint\Genere();
    $_GET = ['rdf' => '1', 'name' => 'Test F'];
    ob_start(); $api->index('gender', 'check_genere'); $output = ob_get_clean();
    verify(json_decode($output, true) === ['status' => '200', 'gender' => 'M'], 'JSON API');
    $_GET = ['rdf' => '-1'];
    ob_start(); $api->index('gender', 'check_genere'); $output = ob_get_clean();
    verify(http_response_code() === 422 && json_decode($output, true)['status'] === '422', 'Invalid input');
    echo "PASS: RDF votes, name fallback, preservation, caching, JSON, validation and NULL persistence\n";
}
