<?php
// Executar: php tests/regression/SigninAlertServiceTest.php
namespace CodeIgniter {
    class Model {}
}
namespace App\Models\Functions {
    class Email {
        public static array $calls = [];
        public static bool $throws = false;
        public function sendmail(...$args): array {
            if (self::$throws) {
                throw new \RuntimeException('SMTP indisponivel');
            }
            self::$calls[] = $args;
            return ['success' => true];
        }
    }
}
namespace {
    function log_message(...$args): void {}
    function check(bool $condition, string $message): void {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    putenv('AUTH_SIGNIN_ALERT_EMAIL=owner@example.org');
    putenv('app.project_name=BRAPCI');
    require __DIR__ . '/../../app/Services/Auth/SigninAlertService.php';

    $service = new \App\Services\Auth\SigninAlertService();
    $service->notify(true, 'user@example.org', '203.0.113.10');
    $service->notify(false, '<script>', '198.51.100.8', 'Password is invalid');

    $calls = \App\Models\Functions\Email::$calls;
    check(count($calls) === 2, 'Enviar alertas de sucesso e falha');
    check($calls[0][0] === 'owner@example.org', 'Enviar ao responsavel configurado');
    check(strpos($calls[0][1], 'SUCESSO') !== false, 'Identificar login bem-sucedido');
    check(strpos($calls[0][2], '203.0.113.10') !== false, 'Informar IP do login');
    check(strpos($calls[1][1], 'FALHA') !== false, 'Identificar falha de login');
    check(strpos($calls[1][2], '&lt;script&gt;') !== false, 'Escapar identificador no HTML');
    check(strpos($calls[1][2], '198.51.100.8') !== false, 'Informar IP da falha');

    \App\Models\Functions\Email::$throws = true;
    $service->notify(false, 'user', '127.0.0.1', 'teste');

    echo "OK: alertas de signin com resultado, IP e tolerancia a falha SMTP.\n";
}
