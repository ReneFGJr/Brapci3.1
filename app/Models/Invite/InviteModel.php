<?php

namespace App\Models\Invite;

use CodeIgniter\Model;

class InviteModel extends Model
{
    protected $DBGroup          = 'brapci';
    protected $table            = 'invite';
    protected $primaryKey       = 'id_iv';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'iv_contact',
        'iv_contact_2',
        'iv_language',
        'iv_journal',
        'iv_contact_name',
        'iv_url',
        'iv_status'
    ];

    protected $useTimestamps = false;
    // created_at já é gerado automaticamente pelo MySQL (current_timestamp)

    protected $validationRules = [
        'iv_contact'   => 'required|max_length[100]',
        'iv_contact_2' => 'required|max_length[100]',
        'iv_language'  => 'required|max_length[3]',
        'iv_status'    => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'iv_contact' => [
            'required' => 'O contato principal é obrigatório.'
        ],
        'iv_language' => [
            'required' => 'O idioma é obrigatório.'
        ]
    ];

    protected $skipValidation = false;

    function index($d1='',$d2='')
        {
            $sx = '';
            switch($d1)
                {
                    case 'view':
                        $sx .= $this->view($d2);
                        break;
                    case 'status':
                        $sx .= $this->status_row($d2);
                    break;

                    case 'create':
                        $sx .= $this->create();
                    break;

                    case 'store':
                        $sx .= $this->store();
                    break;

                    case 'resend':
                        /* Enviar ou reenviar convite */
                        $sx .= $this->send_email($d2);
                        break;

                    case 'resume':
                        $dt = $this->resume();
                        $sx = view('Invite/resume', ['resume' => $dt]);
                        break;
                    default:
                        $sx = $this->index('resume');
                        break;
                }
            return $sx;
        }

    function send_email($id)
        {
            $dt = $this->where('id_iv',$id)->first();
            $name = $dt['iv_contact_name'];
            $user = $dt['iv_contact'];
            $journal = $dt['iv_journal'];

            /* Enviar e-mail */
            $txt = '';
            $txt .= '<table width="600" border=0>';
            $txt .= '<tr><td><img src="cid:$image1" style="width: 100%;"></td></tr>';
            $txt .= '<tr><td>';
            $txt .= 'Prezado usuário ' . $name . ',<br>';
            $txt .= '<br>';
            $txt .= 'Convite.';
            $txt .= '<br><br>';
            $txt .= '<b>'.$journal.'</b><br>';
            $txt .= 'Usuário: ' . $user . '<br>';
            $txt .= '</td></tr></table>';
            $subject = '[BRAPCI] ';
            $subject .= lang('social.social_user_add');
            //sendemail($user, $subject, $txt);

            $email = sendemail("renefgj@gmail.com", $subject, $txt);
            pre($email);
        }

    function view($id)
        {
            $dt = $this->where('id_iv',$id)->first();
            return view('Invite/view_header',['invite'=>$dt, 'status'=>$this->status()])
            . view('Invite/view_actions',['status'=>$dt['iv_status'],'invite'=>$dt]);
        }

    function status_row($status)
        {
            $dt = $this->getStatus($status);
            $sx = view('Invite/row_show', ['invites' => $dt, 'status'=>$this->status()]);
            return $sx;
        }

    function getStatus($status)
        {
            $dt =
            $this->where('iv_status',$status)
            ->orderBy('created_at','DESC')
            ->findAll();


            return $dt;
        }

    public function create()
    {
        $model = new InviteModel();

        return view('Invite/create', [
            'statusList' => $model->status(),
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        $model = new InviteModel();

        $data = [];
        foreach($_POST as $var=>$value)
            {
                $dt[$var] = get($var);
            }

        $model->set($dt)->insert();
        $sx = $this->index('resume');
        return $sx;
    }

    function status()
        {
            $sta = [];
            $sta[0] = 'Inválido';
            $sta[1] = 'Enviar e-mail do Convite enviado';
            $sta[2] = 'Convite enviado';
            $sta[3] = 'Convite em análise';
            $sta[4] = 'Instruções enviadas para o editor';
            $sta[5] = 'Checagem de indexação';
            $sta[8] = 'Revista Indexada na Brapci';
            $sta[9] = 'Convite RECUSADO';
            $sta[10] = 'Convite ACEITO';
            return $sta;
        }

    function statusProcess($dt)
        {
            $sta = $this->status();
            foreach($dt as $id=>$dd)
                {
                    if (isset($dd['status']))
                        {
                            $idSta = round($dd['status']);
                            $dt[$id]['description'] = $sta[$idSta];
                        }
                }
            return $dt;
        }

    function resume()
        {
            $dt = $this
                ->select("count(*) as total, iv_status as status, '' as descritpion")
                ->groupby('iv_status')
                ->findAll();


            if (!$dt)
                {
                    $dt[] = ["total"=>0, "status"=>'1', 'description'=>''];
                }
            $dt = $this->statusProcess($dt);
            return $dt;
        }
}
