<?php

namespace App\Services\Auth;

use App\Models\Auth\SigninAccessLog;

class SigninAccessLogService
{
    public function __construct(private ?SigninAccessLog $accessLog = null)
    {
        $this->accessLog ??= new SigninAccessLog();
    }

    public function record(
        bool $successful,
        string $username,
        string $ipAddress,
        string $reason = '',
        ?int $userId = null,
        string $userAgent = ''
    ): void {
        try {
            $saved = $this->accessLog->insert([
                'user_id' => $userId,
                'username' => mb_substr(trim($username), 0, 255),
                'successful' => $successful ? 1 : 0,
                'reason' => mb_substr(trim($reason), 0, 255),
                'ip_address' => mb_substr(trim($ipAddress), 0, 45),
                'user_agent' => mb_substr(trim($userAgent), 0, 500),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($saved === false) {
                log_message('error', 'Nao foi possivel registrar o acesso de signin no banco brapci_click.');
            }
        } catch (\Throwable $exception) {
            // A indisponibilidade do log nunca deve bloquear a autenticacao.
            log_message('error', 'Erro ao registrar acesso de signin: {message}', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
