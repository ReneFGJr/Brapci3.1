<?php
namespace CodeIgniter { class Model {} }
namespace App\Models {
    class Socials {
        public static $user = ['ID' => 1, 'perfil' => '#ADM'];
        public function validToken() { return self::$user; }
    }
}
namespace App\Models\ElasticSearch {
    class Register {
        public static $deleted = [];
        public static $fail = false;
        private $id;
        public function where($key, $id) { \check($key === 'ID', 'Delete by work ID'); $this->id = $id; return $this; }
        public function delete() { self::$deleted[] = $this->id; return !self::$fail; }
    }
}
namespace App\Models\Base {
    class IssuesWorks {
        public function where($key, $value) { return $this; }
        public function delete() { return true; }
    }
}
namespace App\Models\RDF2 {
    class RDFconcept {
        public static $changes = [];
        public static $exists = true;
        public function le($id) { return self::$exists ? ['id_cc' => $id, 'cc_pref_term' => 100] : []; }
        public function set($changes) { self::$changes = $changes; return $this; }
        public function where($key, $value) { return $this; }
        public function update() { return true; }
    }
    class RDFdata {
        public static $updates = [];
        private $changes;
        private $id;
        public function groupStart() { return $this; }
        public function groupEnd() { return $this; }
        public function where($key, $value) { if ($key === 'id_d') $this->id = $value; return $this; }
        public function orWhere($key, $value) { return $this; }
        public function findAll() {
            return [
                ['id_d' => 1, 'd_r1' => 37033, 'd_p' => 3, 'd_r2' => 7, 'd_literal' => 0],
                ['id_d' => 2, 'd_r1' => 8, 'd_p' => 4, 'd_r2' => 37033, 'd_literal' => 9],
            ];
        }
        public function set($changes) { $this->changes = $changes; return $this; }
        public function update() { self::$updates[$this->id] = $this->changes; return true; }
    }
}
namespace {
    function check($condition, $message) { if (!$condition) throw new \RuntimeException($message); }
    function log_message(...$args) {}
    require $argv[1] ?? __DIR__ . '/../../app/Models/RDF2/RDF.php';
    $rdf = new \App\Models\RDF2\RDF();
    check($rdf->remove('bad')['status'] === '400', 'Reject invalid ID');
    \App\Models\Socials::$user = [];
    check($rdf->remove(37033)['status'] === '401', 'Reject missing token');
    \App\Models\Socials::$user = ['ID' => 1, 'perfil' => '#USER'];
    check($rdf->remove(37033)['status'] === '403', 'Reject non-admin');
    check(\App\Models\ElasticSearch\Register::$deleted === [], 'No deletion before authorization');
    \App\Models\Socials::$user['perfil'] = '#ADM';
    \App\Models\RDF2\RDFconcept::$exists = false;
    check($rdf->remove(37033)['status'] === '404', 'Reject unknown record');
    \App\Models\RDF2\RDFconcept::$exists = true;
    \App\Models\ElasticSearch\Register::$fail = true;
    check($rdf->remove(37033)['status'] === '500', 'Report dataset failure');
    check(\App\Models\RDF2\RDFconcept::$changes === [], 'Do not report canceled after dataset failure');
    \App\Models\ElasticSearch\Register::$fail = false;
    check($rdf->remove(37033)['status'] === '200', 'Successful removal');
    check(end(\App\Models\ElasticSearch\Register::$deleted) === 37033, 'Delete matching dataset');
    $updates = \App\Models\RDF2\RDFdata::$updates;
    check($updates[1]['d_r2'] === -7 && $updates[2]['d_r1'] === -8, 'Preserve distinct RDF links');
    check(\App\Models\RDF2\RDFconcept::$changes['cc_status'] === 9, 'Cancel concept');
    echo "PASS: authorization, dataset deletion, failure handling and individual RDF links\n";
}
