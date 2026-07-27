<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Email extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'emails';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function test()
    {
        $email = 'renefgj@gmail.com';
        $smtpHost = getenv('EMAIL_SMTP');
        $smtpUser = getenv('EMAIL_USER_AUTH');
        $smtpPass = getenv('EMAIL_PASSWORD');
        $smtpPort = getenv('EMAIL_SMTP_PORT');
        $fromEmail = getenv('EMAIL_FROM');
        $fromName = getenv('EMAIL_FROM_NAME');

        $sx = h('Email de teste', 1);
        $sx .= '<p>Enviado para ' . $email . '</p>';

        $sx .= '<div class="mt-3"><h4>Parâmetros do .env</h4><pre class="border p-3 bg-light">';
        $sx .= 'EMAIL_SMTP: ' . htmlspecialchars((string) $smtpHost) . "\n";
        $sx .= 'EMAIL_SMTP_PORT: ' . htmlspecialchars((string) $smtpPort) . "\n";
        $sx .= 'EMAIL_USER_AUTH: ' . htmlspecialchars((string) $smtpUser) . "\n";
        $sx .= 'EMAIL_FROM: ' . htmlspecialchars((string) $fromEmail) . "\n";
        $sx .= 'EMAIL_FROM_NAME: ' . htmlspecialchars((string) $fromName) . "\n";
        $sx .= 'EMAIL_PASSWORD: ' . htmlspecialchars(substr((string) $smtpPass, 0, 6)) . "\n";
        $sx .= '</pre></div>';

        $txt = '';
        $txt .= '<center>';
        $txt .= '<img src="cid:$image1" style="width: 600px;">';
        $txt .= h('Hello World!');
        $txt .= '<p>Welcome to Brapci 3.1!</p>';
        //$this->sendmail($email, , $txt);

        $result = $this->sendmail($email, 'E-mail de teste', $txt);

        $sx .= '<div class="mt-3"><h4>Resultado do envio</h4><pre class="border p-3 bg-light">';
        $sx .= htmlspecialchars($result);
        $sx .= '</pre></div>';

        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    public function sendmail(
        string $to = '',
        string $subject = '',
        string $text = '',
        array $files = []
    ): array {

        $email = \Config\Services::email();

        $config = [
            'protocol'     => 'smtp',
            'SMTPHost'     => getenv('EMAIL_SMTP'),
            'SMTPUser'     => getenv('EMAIL_USER_AUTH'),
            'SMTPPass'     => getenv('EMAIL_PASSWORD'),
            'SMTPPort'     => (int) getenv('EMAIL_SMTP_PORT'),
            'SMTPCrypto'   => getenv('EMAIL_SMTP_CRYPTO') ?: '',
            'SMTPTimeout'  => 30,

            'mailType'     => 'html',
            'charset'      => 'UTF-8',
            'wordWrap'     => true,

            'CRLF'         => "\r\n",
            'newline'      => "\r\n",
        ];

        $email->initialize($config);

        $fromEmail = getenv('EMAIL_FROM');
        $fromName  = getenv('EMAIL_FROM_NAME');

        if (empty($fromName)) {
            $fromName = $fromEmail;
        }

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        $email->setSubject($subject);

        /*
     * Imagem incorporada
     */
        $filename = FCPATH . 'img/email/bg-email-hL3a.jpg';

        if (is_file($filename)) {
            $email->attach($filename);

            $cid = $email->setAttachmentCID($filename);

            $text = str_replace('$image1', $cid, $text);
        } else {
            log_message('warning', 'Imagem do e-mail não encontrada: ' . $filename);

            $text = str_replace('$image1', '', $text);
        }

        /*
     * Anexos extras
     */
        foreach ($files as $file) {
            if (is_file($file)) {
                $email->attach($file);
            }
        }

        $email->setMessage($text);

        try {

            if ($email->send()) {

                return [
                    'success' => true,
                    'message' => 'E-mail enviado com sucesso.',
                    'debug'   => ''
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao enviar o e-mail.',
                'debug'   => $email->printDebugger([
                    'headers',
                    'subject',
                    'body'
                ])
            ];
        } catch (\Throwable $e) {

            log_message('error', $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'debug'   => $e->getTraceAsString()
            ];
        }
    }
}
