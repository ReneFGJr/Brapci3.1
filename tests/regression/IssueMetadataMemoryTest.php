<?php
// Run: php tests/regression/IssueMetadataMemoryTest.php (no database required).
namespace CodeIgniter {
    class Model {}
}
namespace App\Models\Metadata {
    class Abnt {
        public function short($data, $full) { return $data['title']; }
    }
}
namespace App\Models\Cited {
    class Index {
        public static int $reads = 0;
        public function total_cited($id) { self::$reads++; return 2; }
    }
}
namespace App\Models\Base {
    class Issues {
        public static array $row = [
            'jnl_name' => 'Journal', 'id_jnl' => 7, 'is_source' => 8,
            'is_year' => 2026, 'is_nr' => 1, 'is_vol_roman' => '', 'is_vol' => 10,
        ];
        public function join(...$args) { return $this; }
        public function where(...$args) { return $this; }
        public function first() { return self::$row; }
    }
    class IssuesWorks {
        public static int $reads = 0;
        public function issueWorks($id) {
            self::$reads++;
            return [['ID' => 101894, 'json' => '{"title":"Example"}']];
        }
    }
}
namespace {
    require __DIR__ . '/../../app/Models/RDF2/RDFmetadata.php';
    function verify($condition, $message) {
        if (!$condition) { throw new \RuntimeException($message); }
    }
    $model = new \App\Models\RDF2\RDFmetadata();
    $input = ['concept' => ['id_cc' => 123]];
    $simple = $model->metadataIssue($input, true);
    verify($simple['is_year'] === 2026 && $simple['publisher'] === 'Journal', 'Issue header preserved');
    verify(!isset($simple['works']), 'Simple metadata has no works');
    verify(\App\Models\Base\IssuesWorks::$reads === 0, 'Simple metadata must not load work JSON');
    verify(\App\Models\Cited\Index::$reads === 0, 'Simple metadata must not count work citations');
    $full = $model->metadataIssue($input);
    verify(count($full['works']) === 1 && strpos($full['works'][0], 'Example') === 0, 'Full issue listing preserved');
    verify($model->worksID === [101894], 'Full issue work IDs preserved');
    verify(\App\Models\Base\IssuesWorks::$reads === 1 && \App\Models\Cited\Index::$reads === 1, 'Full metadata loads works and citations');
    \App\Models\Base\Issues::$row = [];
    verify($model->metadataIssue($input, true) === [], 'Missing issue returns empty metadata');
    echo "Issue metadata memory regression: OK\n";
}
