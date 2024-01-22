<?php
/*
@category API
@package Find
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/find/libraries/
@example $URL/api/find/lastitens/
@abstract $URL/api/find/libraries/ -> Lista as bibliotecas do Sistema
@abstract API para consulta de metadados de livros com o ISBN
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Oauth extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'finds';
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

    function index($d1, $d2, $d3)
    {
        switch ($d1) {
            case 'forgot':
                $dd = $this->forgot();
                echo json_encode($dd);
                exit;
            case 'signup':
                $dd = $this->signup();
                echo json_encode($dd);
                exit;
            case  'signin':
                $dd = $this->signin();
                echo json_encode($dd);
                exit;
                break;
        }
    }

    function check()
        {

        }

    function logout()
        {
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");

            $session = \Config\Services::session();
            $url = \Config\Services::url();
            $session->destroy();

            $dd['process'] = date("Y-m-d H:i:s");
            $dd['status'] = '200';

            echo json_encode($dd);
            exit;
        }

    function forgot()
        {
            $Socials = new \App\Models\Socials();
            $email = get("email");
            $dd = [];
            $dd['email'] = $email;
            $dd['data'] = date("Y-m-dTH:i:s");
            $dd['message'] = $Socials->forgout($email);

            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");
            //echo json_encode($dd);
            //exit;

            return $dd;
        }

    function signup()
        {
            $dd = [];
            $dd['process'] = date("Y-m-d H:i:s");
            $Socials = new \App\Models\Socials();

            $sx = $Socials->signup();
            $status = $Socials->error;
            switch($status)
                {
                    case '510':
                        $msg = msg("brapci.email_invalid");
                        break;
                    case '511':
                        $msg = msg("brapci.email_already_exist");
                        break;
                    default:
                        $msg = msg('brapci.valid');
                }
            $dd['message'] = $msg;
            $dd['status'] = $status;

            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");

            return $dd;

        }

    function signin()
    {
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header('Access-Control-Allow-Origin: *');
        header("Content-type: application/json; charset=utf-8");

        $dd = [];
        $dd['process'] = date("Y-m-d H:i:s");
        $Socials = new \App\Models\Socials();
        $rsp = $Socials->signin();
        $rsp = strip_tags($rsp);

        if (strpos($rsp, 'ERROR')) {
            $dd['status'] = '400';
            $dd['message'] = 'User or Password incorrect';
            return $dd;
        } else {
            if (isset($_SESSION['apikey'])) {
                $nome = $_SESSION['user'];
                $dd['status'] = '200';
                $dd['message'] = 'Loged';
                $dd['message'] = 'Loged2';
                $dd['id'] = $_SESSION['id'];
                $dd['email'] = $_SESSION['email'];
                $dd['givenName'] = trim(substr($nome, 0, strpos($nome, ' ')));
                $dd['sn'] = trim(substr($nome, strpos($nome, ' '), strlen($nome)));
                $dd['token'] = $_SESSION['apikey'];
                $dd['persistent-id'] = PATH . 'api/socials/apikey/' . $dd['token'];
            } else {
                $dd['status'] = '400';
                $dd['message'] = 'Error Login';
                $dd['user'] = get("user");
            }
        }
        return $dd;
    }
}
