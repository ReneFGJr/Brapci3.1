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

    function index($d1)
    {
        header('Access-Control-Allow-Origin: *');
        if ((get("test") == '') and (get("header") == '')) {
            header("Content-Type: application/json");
        }

        $d2 = get("tamanho");
        $d3 = get("caracteresEspeciais");
        $d4 = get("menemonico");

        switch ($d1) {
            case 'check-change-password':
                $Social = new \App\Models\Socials();
                $dd = $Social->validRecover(get("apikey"));
                echo json_encode($dd);
                exit;
                break;
            case 'chagePassword':
                $Social = new \App\Models\Socials();
                $dd = $Social->chagePassword(get("apikey"),get("password"));
                echo json_encode($dd);
                exit;
                break;

            case 'generatePassword':
                if ($d2 == '') { $d2 = 12; }
                if ($d3 == '') { $d3 = 0; }
                if ($d4 == '') { $d4 = 0; }

                $Password = new \App\Models\Password\Index();
                $dd = [];
                for ($r=0;$r < 10;$r++)
                    {
                        if ($d4)
                            {
                                $dd['pass'][] = $Password->gerarSenhaMnemotecnica();

                            } else {
                                $dd['pass'][] = $Password->gerarSenha(round($d2), $d3);
                            }

                    }

                $dd['status'] = '200';
                echo json_encode($dd);
                exit;
                break;
            case 'oauth2':
                $token = get("token");
                $Socials = new \App\Models\Socials();
                $dt = $Socials->where('us_apikey',$token)->first();
                $RSP = [];

                if ($dt == [])
                    {

                        $RSP['status'] = '400';
                        $RSP['messagem'] = 'Token não validado';
                    } else {
                        $Name = $dt['us_nome'];
                        $givenName = '';
                        $displayName = '';
                        if (strpos($Name,' '))
                            {
                                $givenName = substr($dt['us_nome'],0,strpos($dt['us_nome'],' '));
                                $displayName = trim(substr($dt['us_nome'],strlen($givenName),100));
                            } else {
                                $givenName = $Name;
                            }
                        $RSP['status'] = '200';
                        $RSP['id'] = $dt['id_us'];
                        $RSP['displayName'] = $displayName;
                        $RSP['email'] = '';
                        $RSP['givenName'] = $givenName;
                        $RSP['token'] = $token;
                        $RSP['persistentId'] = '';
                        $RSP['admin'] = '';
                    }
                echo json_encode($RSP);
                exit;
                break;
            case 'chagePassword':
                $dd = $_POST;
                $Socials = new \App\Models\Socials();
                $key = get("apikey");
                $pass = get('pass1');
                $passv = get('pass2');
                if (($pass == $passv) and (strlen($pass) > 5))
                    {
                        if ($Socials->chagePassword($key,$pass) == True)
                            {
                                $dd['status'] = '200';
                                $dd['message'] = lang('brapci.successfull');
                            } else {
                                $dd['status'] = '200';
                                $dd['message'] = lang('brapci.apikey_expired');
                            }
                    } else {
                        $dd = $_POST;
                        $dd['status'] = '500';
                        $dd['message'] = lang('brapci.error - password incorrect');
                    }
                echo json_encode($dd);
                exit;
                break;
            case 'validApiRecover':
                $Socials = new \App\Models\Socials();
                $key = get("apikey");
                $dd= $Socials->validRecover($key);
                echo json_encode($dd);
                exit;
                break;
            case 'forgot':
                $dd = $this->forgot();
                echo json_encode($dd);
                exit;
                break;
            case 'signup':
                $dd = $this->signup();
                echo json_encode($dd);
                exit;
            case  'signin':
                $dd = $this->signin();
                echo json_encode($dd);
                exit;
                break;
            case 'checkEmail':
                $dd = [];
                $email = get("email");
                $Socials = new \App\Models\Socials();
                $da = $Socials->where('us_email',$email)->first();
                if ($da != [])
                {
                    $dt['nome'] = $da['us_nome'];
                    $dt['email'] = $email;
                    $dt['afiliacao'] = $da['us_affiliation'];
                    $dt['status'] = '200';
                } else {
                    $dt['status'] = '400';
                    $dt['message'] = 'e-mail not found';
                }
                echo json_encode($dt);
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
                        $msg = $sx;
                        break;
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
        $RSP = $Socials->signin();


        if ($RSP['status'] != '400') {
            $RSP['message'] = 'User or Password incorrect';
            return $RSP;
        } else {
                $dd['status'] = '200';
                $dd['message'] = 'Loged2';
                $dd['id'] = $RSP['ID'];
                $dd['email'] = $RSP['email'];
                $nome = $RSP['user'];
                $dd['givenName'] = trim(substr($nome, 0, strpos($nome, ' ')));
                $dd['sn'] = trim(substr($nome, strpos($nome, ' '), strlen($nome)));
                $dd['token'] = $RSP['apikey'];
                $dd['persistent-id'] = PATH . 'api/socials/apikey/' . $dd['token'];

                /********************************** */
                $adminX = 0;
                $ddp = (string)$Socials->validGroups($_SESSION['id']);
                if (strpos(' '. $ddp,'#ADM') > 0)
                    {
                        $adminX = 1;
                    }
                $dd['admin'] = $adminX;
            }
        return $dd;
    }
}
