<?php

namespace App\Models\Handle;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
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

    var $dts = [];

    function header($handle)
        {
            $Server = new \App\Models\Handle\Server();
            $ds = $Server->getHandle($handle);
            $this->dts = $ds;
            if ($ds == '') { return ''; }
            $cmd = '';
            $cmd .= 'AUTHENTICATE PUBKEY:300:0.NA/'.trim($ds['s_handle']).cr();
            $cmd .= trim($ds['s_admpriv']).'|'.trim($ds['s_password']).cr();
            $cmd .= cr();
            $cmd .= 'HOME '.$ds['s_home'].cr();
            $cmd .= '0.NA/'.$ds['s_handle'].cr();
            $cmd .= cr();

            return $cmd;
        }

    function create($handle,$url,$desc)
    {
        $message = '';
        $cmd = '';
        $hdl = substr($handle,0,strpos($handle,'/'));
        $cmd .= $this->header($hdl);
        if ($cmd == '')
            {
                $RSP['message'] = 'Handle '.$handle.' not found in Database';
                $RSP['status'] = '401';
                return $RSP;
            }

        $cmd .= 'CREATE '.$handle.cr();
        $cmd .= '100 HS_ADMIN 86400 1110 ADMIN 200:111111111111:0.NA/'.$hdl.cr();
        $cmd .= '3 URL 86400 1110 UTF8 '.$url.cr();
        $cmd .= '7 EMAIL 86400 1110 UTF8 '.$this->dts['s_email'].cr();
        if ($desc != '')
            {
                $cmd .= '9 DESC 86400 1110 UTF8 '.$desc.cr();
            }
        $cmd .= cr();

        $Handle = new \App\Models\Handle\Handle();
        $status = $this->shell($cmd);
        $status = substr($status,strpos($status,'create:'),strlen($status));
        $status = substr($status,0,strpos($status,chr(10)));

        if (strpos($status, 'HANDLE ALREADY EXISTS'))
            {
                $sta = '101';
                $message = 'HANDLE ALREADY EXISTS';
            }

        if (strpos($status, 'HANDLE ALREADY EXISTS')) {
            $sta = '101';
            $message = 'HANDLE ALREADY EXISTS';
        }

        $Handle->register($hdl, $url, $this->dts['s_email'], $desc, $status);
        $RSP['status'] = $sta;
        if ($message != '')
            {
                $RSP['message'] = $message;
            }
        return $RSP;
    }

    function shell($cmd)
    {
        $dir = '/hs/cmd/handles/';
        dircheck($dir);
        $file = $dir.'cmd';

        file_put_contents($file,$cmd);

        $bash = '/hs/handle-9.3.0/bin/hdl-genericbatch '.$dir.'cmd';
        $rsp = shell_exec($bash);
        return $rsp;
    }

    function index($d1,$d2,$d3)
        {
            $RSP = [];
            switch($d1)
                {
                    case 'create':
                        $handle = get("handle");
                        $url = get("url");
                        $desc = get('desc');
                        if (($url == '') or ($handle == ''))
                        {
                            $RSP['message'] = 'Falta parametros da "url" ou "handle"';
                            $RSP['status'] = '504';
                        } else {
                            $RSP = $this->create($handle, $url, $desc);
                        }
                    break;

                    default:
                    $RSP['message'] = 'Verb not found - '.$d1;
                    $RSP['status'] = '500';
                }
            echo json_encode($RSP);
            exit;
        }
}
