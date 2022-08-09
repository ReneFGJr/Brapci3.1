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
        $sx = h('Email de teste', 1);

        $sx = bs(bsc($sx, 12));
        $txt = h('teste de email');
        $txt .= '<center>';
        $txt .= '<img src="cid:$image1" style="width: 600px;">';
        $txt .= '<p>Hello World!</p>';
        $this->sendmail('renefgj@gmail.com', 'E-mail deteste', $txt);
        return $sx;
    }

    function sendmail($to = '', $subject = '', $text = '', $files = array())
    {
        $this->email = \Config\Services::email();

        $config['protocol'] = 'sendmail';
        $config['mailPath'] = '/usr/sbin/sendmail';

        $config['wordWrap'] = true;
        $config['smtp_timeout'] = '7';
        $config['SMTPHost'] = 'ssl://smtp.gmail.com';
        $config['SMTPUser'] = 'brapcici@gmail.com';
        $config['SMTPPass'] = getenv('email_password');
        $config['SMTPPort'] = '465';
        $config['charset']    = 'utf-8';
        //$config['newline']    = "\r\n";
        $config['mailType'] = 'html';

        $filename = 'img/email/bg-email-hL3a.jpg';
        if (file_exists($filename)) {
            $this->email->attach($filename);
            $cid = $this->email->setAttachmentCID($filename);
            $sx = troca($text, '$image1', $cid);
        } else {
            echo "Logo not found";
        }

        $this->email->initialize($config);

        $this->email->setFrom('brapcici@gmail.com', 'Brapci');
        $this->email->setTo('renefgj@gmail.com');
        //$this->email->setCC('rene.gabriel@ufrgs.br');
        //$email->setBCC('them@their-example.com');

        $this->email->setSubject($subject);
        $this->email->setMessage($text);

        $this->email->send();

        $sx = 'Send email';
        return $sx;
    }
}
