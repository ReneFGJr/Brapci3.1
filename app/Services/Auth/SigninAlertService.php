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
        $message = $this->buildMessage(
            $successful,
            $status,
            htmlspecialchars($project, ENT_QUOTES, 'UTF-8'),
            $safeUsername,
            $safeIp,
            $occurredAt,
            $safeReason
        );

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

    private function buildMessage(
        bool $successful,
        string $status,
        string $project,
        string $username,
        string $ipAddress,
        string $occurredAt,
        string $reason
    ): string {
        $accentColor = $successful ? '#15803d' : '#b91c1c';
        $statusBackground = $successful ? '#dcfce7' : '#fee2e2';
        $statusText = $successful ? 'Acesso autorizado' : 'Acesso não autorizado';
        $reasonRow = '';

        if (!$successful && $reason !== '') {
            $reasonRow = $this->detailRow('Motivo', $reason, true);
        }

        return '<!doctype html>'
            . '<html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>'
            . '<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">'
            . '<div style="display:none;max-height:0;overflow:hidden;opacity:0;">Alerta de segurança: tentativa de login ' . $status . '.</div>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f9;">'
            . '<tr><td align="center" style="padding:32px 16px;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background-color:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">'
            . '<tr><td style="height:6px;background-color:' . $accentColor . ';font-size:0;line-height:0;">&nbsp;</td></tr>'
            . '<tr><td style="padding:32px 36px 18px;">'
            . '<div style="font-size:13px;font-weight:bold;letter-spacing:1.2px;text-transform:uppercase;color:#64748b;margin-bottom:16px;">' . $project . ' · Segurança</div>'
            . '<span style="display:inline-block;padding:7px 12px;border-radius:999px;background-color:' . $statusBackground . ';color:' . $accentColor . ';font-size:12px;font-weight:bold;letter-spacing:.5px;">' . $status . '</span>'
            . '<h1 style="margin:18px 0 8px;font-size:26px;line-height:1.25;color:#0f172a;">Tentativa de login</h1>'
            . '<p style="margin:0;color:#64748b;font-size:15px;line-height:1.6;">' . $statusText . ' na sua conta.</p>'
            . '</td></tr>'
            . '<tr><td style="padding:8px 36px 28px;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:separate;border-spacing:0;background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">'
            . $this->detailRow('Usuário', $username)
            . $this->detailRow('Endereço IP', $ipAddress)
            . $this->detailRow('Data e hora', $occurredAt)
            . $reasonRow
            . '</table>'
            . '</td></tr>'
            . '<tr><td style="padding:20px 36px;background-color:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;line-height:1.6;">'
            . 'Esta é uma mensagem automática de segurança. Se você não reconhece esta atividade, recomendamos alterar sua senha.'
            . '</td></tr>'
            . '</table>'
            . '</td></tr></table></body></html>';
    }

    private function detailRow(string $label, string $value, bool $highlight = false): string
    {
        $valueColor = $highlight ? '#b91c1c' : '#0f172a';

        return '<tr>'
            . '<td style="padding:13px 16px;border-bottom:1px solid #e2e8f0;color:#64748b;font-size:13px;width:35%;">' . $label . '</td>'
            . '<td style="padding:13px 16px;border-bottom:1px solid #e2e8f0;color:' . $valueColor . ';font-size:14px;font-weight:bold;word-break:break-word;">' . $value . '</td>'
            . '</tr>';
    }
}
