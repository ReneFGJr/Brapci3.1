<?php
/*
@category API
@package Brapci OAUTH2
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/socials/signin?user=teste&pwd=teste
@abstract Autenticador de usuário
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Oauth extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_source';
    protected $primaryKey       = 'id_jnl';
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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        header('Access-Control-Allow-Origin: *');
        header("Content-type: application/json; charset=utf-8");
        switch($d1)
            {
                case 'signin':
                    #user, pwd
                    $dd = [];
                    $user = get('user');
                    if ($user == 'teste')
                    {
                        $nome = 'Usuário de Teste';
                        $dd['status'] = '200';
                        $dd['message'] = 'Loged';
                        $dd['displayName'] = $nome;
                        $dd['email'] = 'teste@teste.com.br';
                        $dd['givenName'] = trim(substr($nome, 0, strpos($nome, ' ')));
                        $dd['sn'] = trim(substr($nome, strpos($nome, ' '), strlen($nome)));
                        $dd['token'] = md5('teste');
                        $dd['persistent-id'] = PATH . 'api/socials/apikey/' . md5('teste');
                    } else {
                        $dd = $this->signin();
                    }
                    break;
                default:
                    return $this->all();
            }
            echo json_encode($dd);
            exit;
    }

    function signin()
        {
            $Socials = new \App\Models\Socials();
            $rsp = $Socials->signin();
            $dd = [];

            if (strpos($rsp,'ERROR'))
                {
                    $dd['error'] = '400';
                    $dd['message'] = 'User or Password incorrect';
                } else {
                    if (isset($_SESSION['apikey']))
                        {
                            $nome = $_SESSION['user'];
                            $dd['status'] = '200';
                            $dd['message'] = 'Loged';
                            $dd['displayName'] = $nome;
                            $dd['email'] = $_SESSION['email'];
                            $dd['givenName'] = trim(substr($nome,0,strpos($nome,' ')));
                            $dd['sn'] = trim(substr($nome, strpos($nome, ' '), strlen($nome)));
                            $dd['token'] = $_SESSION['apikey'];
                            $dd['persistent-id'] = PATH.'api/socials/apikey/'.$dd['token'];
                        } else {
                            $dd['error'] = '400';
                            $dd['message'] = 'Session expired';
                        }
                }
            return $dd;
        }

    function all()
        {
            $dt['error'] = '400';
            $dt['message'] = 'Verb not informed';
            return $dt;
        }

    function collections($d1,$d2)
        {
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");
            $Collections = new \App\Models\Base\Collections();

            echo $Collections->list('json');
            exit;


        }
}
