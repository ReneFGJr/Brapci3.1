<?php
// Executar: php tests/regression/BugsSolutionTest.php
namespace CodeIgniter {
    class Model {
        public $db;
        public $report;
        public $data;
        public function __construct() { $this->db = $this; }
        public function where(...$args) { return $this; }
        public function first() { return $this->report; }
        public function set($data) { $this->data = $data; return $this; }
        public function update() { $this->report = null; return true; }
        public function affectedRows() { return 1; }
    }
}
namespace App\Models {
    class Socials {
        public static $email = 'reporter@example.org';
        public function find($id) { return ['us_email' => self::$email]; }
    }
}
namespace App\Models\Functions {
    class Email {
        public static $calls = [];
        public static $success = true;
        public function sendmail(...$args) {
            self::$calls[] = $args;
            return ['success' => self::$success];
        }
    }
}
namespace {
    define('PATH', 'https://example.org');
    function esc($value, $context = 'html') { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
    function log_message(...$args) {}
    require __DIR__ . '/../../app/Models/Functions/Bugs.php';
    function check($condition, $message) { if (!$condition) { throw new \RuntimeException($message); } }
    $report = ['bug_user' => 7, 'bug_name' => '<Reporter>', 'bug_problem' => 'title', 'bug_v' => 123];
    $bugs = new \App\Models\Functions\Bugs();
    $bugs->report = $report;
    $bugs->corrected(42, '<script>solution</script>');
    $call = \App\Models\Functions\Email::$calls[0];
    check($bugs->data['bug_status'] === 2, 'Marcar como resolvido');
    check($call[0] === 'reporter@example.org', 'Destinatário do relato');
    check($call[5] === ['brapcici@gmail.com'], 'Cópia visível para BRAPCI');
    check(strpos($call[2], 'cid:$image1') !== false, 'Cabeçalho padrão');
    check(strpos($call[2], 'Agradecemos') !== false, 'Agradecimento');
    check(strpos($call[2], '&lt;script&gt;') !== false, 'Escapar solução HTML');
    $bugs->corrected(42, 'Repetido');
    check(count(\App\Models\Functions\Email::$calls) === 1, 'Não reenviar relato resolvido');
    $bugs->report = $report;
    \App\Models\Socials::$email = '';
    check(strpos($bugs->corrected(43, 'Corrigido'), 'alert-warning') !== false, 'Avisar ausência de e-mail');
    check(count(\App\Models\Functions\Email::$calls) === 1, 'Não enviar para endereço ausente');
    $bugs->report = $report;
    \App\Models\Socials::$email = 'reporter@example.org';
    \App\Models\Functions\Email::$success = false;
    check(strpos($bugs->corrected(44, 'Corrigido'), 'alert-warning') !== false, 'Avisar falha SMTP');
    echo "OK: solução, destinatário, cópia, cabeçalho, escape e falhas de envio.\n";
}
