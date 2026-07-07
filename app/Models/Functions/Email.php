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

        $sx = h('Email de teste', 1);
        $sx .= '<p>Enviado para '.$email.'</p>';

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

    function sendemail($to = '', $subject = '', $text = '', $files = array())
        {
            return $this->sendmail($to,$subject,$text,$files);
        }

    function sendmail($to = '', $subject = '', $text = '', $files = array())
    {
        $this->email = \Config\Services::email();

        $config = [];
        $config['protocol'] = 'smtp';
        $config['SMTPHost'] = getenv('EMAIL_SMTP');
        $config['SMTPUser'] = getenv('EMAIL_USER_AUTH');
        $config['SMTPPass'] = getenv('EMAIL_PASSWORD');
        $config['SMTPPort'] = (int) getenv('EMAIL_SMTP_PORT');
        $config['SMTPCrypto'] = 'ssl';
        $config['SMTPTimeout'] = 10;

        $config['wordWrap'] = true;
        $config['fromEmail'] = getenv('EMAIL_FROM');
        $config['fromName'] = getenv('EMAIL_FROM_NAME');

        $config['charset']    = 'utf-8';
        //$config['newline']    = "\r\n";
        $config['mailType'] = 'html';

        $filename = 'img/email/bg-email-hL3a.jpg';
        if (file_exists($filename)) {
            $this->email->attach($filename);
            $cid = $this->email->setAttachmentCID($filename);
            $text = troca($text, '$image1', $cid);
        } else {
            echo "Logo not found";
        }

        $this->email->initialize($config);

        $this->email->setFrom('brapcici@gmail.com', 'Brapci');
        $this->email->setTo($to);
        $this->email->setBCC($to);
        //$this->email->setCC('rene.gabriel@ufrgs.br');
        //$email->setBCC('them@their-example.com');

        $this->email->setSubject($subject);
        $this->email->setMessage($text);

        $sent = $this->email->send(false);

        $sx = '';
        $sx .= $sent ? 'Email enviado com sucesso.' : 'Erro ao enviar o email.';
        $sx .= "\n\n";
        $sx .= $this->email->printDebugger(['headers', 'subject', 'body']);
        return $sx;
    }
}
