<?php

namespace App\Services\Auth;

use App\Models\Functions\Email;

class SigninAlertService
{
    public function __construct(private ?Email $mailer = null)
    {
        $this->mailer ??= new Email();
    }

    public function notify(bool $successful, string $username, string $ipAddress, string $reason = ''): void
    {
        $recipient = trim((string) (getenv('AUTH_SIGNIN_ALERT_EMAIL') ?: getenv('EMAIL_FROM')));
        if ($recipient === '' || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            log_message('warning', 'Alerta de signin nao enviado: destinatario nao configurado.');
            return;
        }

        $status = $successful ? 'SUCESSO' : 'FALHA';
        $project = trim((string) getenv('app.project_name')) ?: 'BRAPCI';
        $occurredAt = date('d/m/Y H:i:s');
        $safeUsername = htmlspecialchars($username !== '' ? $username : '(nao informado)', ENT_QUOTES, 'UTF-8');
        $safeIp = htmlspecialchars($ipAddress !== '' ? $ipAddress : '(indisponivel)', ENT_QUOTES, 'UTF-8');
        $safeReason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        $message = '<h2>Tentativa de login: ' . $status . '</h2>';
        $message .= '<p><strong>Usuario:</strong> ' . $safeUsername . '</p>';
        $message .= '<p><strong>IP:</strong> ' . $safeIp . '</p>';
        $message .= '<p><strong>Data e hora:</strong> ' . $occurredAt . '</p>';
        if (!$successful && $safeReason !== '') {
            $message .= '<p><strong>Motivo:</strong> ' . $safeReason . '</p>';
        }

        try {
            $result = $this->mailer->sendmail(
                $recipient,
                '[' . $project . '] Login - ' . $status,
                $message
            );

            if (!($result['success'] ?? false)) {
                log_message('error', 'Falha ao enviar alerta de signin para {recipient}.', ['recipient' => $recipient]);
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Erro ao enviar alerta de signin: {message}', ['message' => $exception->getMessage()]);
        }
    }
}
