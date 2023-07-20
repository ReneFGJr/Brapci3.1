<?php

namespace App\Models\Dataverse\API;

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

    var $url = '';

    function curlExec($dt)
    {
        $rsp = array();
        $rsp['msg'] = '';

        //return shell_exec("e:\projeto\curl.exe");

        if ((!isset($dt['url'])) or (!isset($dt['api'])) or (!isset($dt['apikey']))) {
            $sx = "Error: Missing URL(url), API or API(api) Key (AUTH)";
            $sx .= '<br>url=' . $dt['url'];
            $rsp['msg'] = $sx;
        } else {
            $url = $dt['url'] . $dt['api'];
            $apiKey = $dt['apikey'];

            /* Comando */
            $cmd = getenv("curl").'curl ';
            /* APIKEY */
            if (isset($dt['apikey'])) {
                $cmd .= '-H X-Dataverse-key:' . $apiKey . ' -H Content-type:application/json ';
            }

            /* POST */
            if (isset($dt['POST'])) {
                $cmd .= '-X POST ' . $url . ' ';
            } else {
                $cmd .= ' ' . $url . ' ';
            }

            /* POST */
            if (isset($dt['FILE'])) {
                if (!file_exists($dt['FILE'])) {
                    $rsp['msg'] .= bsmessage('File not found - ' . $dt['FILE'], 3);
                }
                //		$cmd .= '-H "Content-Type: application/json" ';
                $cmd .= '--upload-file "' . ($dt['FILE']) . '" ';
            }
            $sx = '<tt>'.$cmd.'</tt>';
            $txt = shell_exec($cmd);
            $sx.= '<hr>'.$txt;
            $this->url = $cmd;
            return $sx;
        }
        return $sx;
    }

    function curl($dt)
    {
        $METH = 'GET';
        if (isset($dt['method'])) {
            $METH = $dt['method'];
        }
        $url = $dt['url'] . $dt['api'];
        $header = ['Content-Type' => 'application/json', 'key' => $dt['apikey']];
        $header = [];

        if (isset($dt['apikey'])) {
            if (strpos($url, '?')) {
                if (trim($dt['apikey']) != '')
                    {
                        $url .= '&key=' . $dt['apikey'];
                    }

            } else {
                if (trim($dt['apikey']) != '')
                {
                    $url .= '?key=' . $dt['apikey'];
                }
            }
        }

        $this->url = $url;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $nr = 0;

        if (isset($dt['FILE'])) {
            $file = $dt['FILE'];
            $json = file_get_contents($file);
            $DV = json_decode($json,true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($DV));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $METH);

        $data = curl_exec($curl);
        $erro = curl_errno($curl);
        curl_close($curl);

        if ($erro == 0) {
            return $data;
        } else {
            echo "ERRO CURL: " . $erro;
            echo '<br>' . $url;
        }
        return ($data);
    }
}
