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

        $smtpHost  = getenv('EMAIL_SMTP');
        $smtpUser  = getenv('EMAIL_USER_AUTH');
        $smtpPass  = getenv('EMAIL_PASSWORD');
        $smtpPort  = getenv('EMAIL_SMTP_PORT');
        $smtpCrypt = getenv('EMAIL_SMTP_CRYPTO');

        $fromEmail = getenv('EMAIL_FROM');
        $fromName  = getenv('EMAIL_FROM_NAME');

        $sx  = h('Teste de envio de e-mail', 1);

        $sx .= '<div class="alert alert-info">';
        $sx .= 'Destinatário: <b>' . esc($email) . '</b>';
        $sx .= '</div>';

        $sx .= '<div class="card mt-3">';
        $sx .= '<div class="card-header"><b>Configuração SMTP</b></div>';
        $sx .= '<div class="card-body"><pre>';

        $sx .= 'EMAIL_SMTP...........: ' . esc($smtpHost) . "\n";
        $sx .= 'EMAIL_SMTP_PORT......: ' . esc($smtpPort) . "\n";
        $sx .= 'EMAIL_SMTP_CRYPTO....: ' . esc($smtpCrypt) . "\n";
        $sx .= 'EMAIL_USER_AUTH......: ' . esc($smtpUser) . "\n";
        $sx .= 'EMAIL_FROM...........: ' . esc($fromEmail) . "\n";
        $sx .= 'EMAIL_FROM_NAME......: ' . esc($fromName) . "\n";

        if (!empty($smtpPass)) {
            $sx .= 'EMAIL_PASSWORD.......: **************' . "\n";
        } else {
            $sx .= 'EMAIL_PASSWORD.......: NÃO CONFIGURADA' . "\n";
        }

        $sx .= '</pre></div></div>';

        $txt  = '<center>';
        $txt .= '<img src="cid:$image1" style="width:600px">';
        $txt .= h('Hello World!');
        $txt .= '<p>Welcome to Brapci 3.1!</p>';
        $txt .= '<p><b>' . date('d/m/Y H:i:s') . '</b></p>';
        $txt .= '</center>';

        $result = $this->sendmail(
            $email,
            'Teste SMTP - Brapci',
            $txt
        );

        $sx .= '<div class="card mt-4">';

        if ($result['success']) {

            $sx .= '<div class="card-header bg-success text-white">';
            $sx .= '✔ E-mail enviado';
            $sx .= '</div>';

            $sx .= '<div class="card-body">';
            $sx .= '<pre>' . esc($result['message']) . '</pre>';
            $sx .= '</div>';
        } else {

            $sx .= '<div class="card-header bg-danger text-white">';
            $sx .= '✘ Falha no envio';
            $sx .= '</div>';

            $sx .= '<div class="card-body">';
            $sx .= '<pre>' . esc($result['message']) . '</pre>';

            if (!empty($result['debug'])) {
                $sx .= '<hr>';
                $sx .= '<h5>Debugger SMTP</h5>';
                $sx .= '<pre>';
                $sx .= esc($result['debug']);
                $sx .= '</pre>';
            }

            $sx .= '</div>';
        }

        $sx .= '</div>';

        return bs(
            bsc($sx, 12)
        );
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
