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
            case  'signin':
                $dd = $this->signin();
                echo json_encode($dd);
                exit;
                break;
        }
    }

    function signin()
    {
        header("Access-Control-Allow-Origin: http://localhost:4200");
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        header('Access-Control-Allow-Origin: *');
        header("Content-type: application/json; charset=utf-8");

        $dd = [];
        $dd['process'] = date("Y-m-d H:i:s");
        $dd['semd'] = $request;

        $json_convertido = json_decode(file_get_contents('php://input'), true);

        //Exibindo os dados enviados para seu arquivo PHP
        echo '<pre>' . print_r($json_convertido, true) . '</pre>';
        return "";

        $Socials = new \App\Models\Socials();
        $rsp = $Socials->signin();
        $rsp = strip_tags($rsp);

        if (strpos($rsp, 'ERROR')) {
            $dd['error'] = '400';
            $dd['message'] = 'User or Password incorrect';
            return $dd;
        } else {
            if (isset($_SESSION['apikey'])) {
                $nome = $_SESSION['user'];
                $dd['status'] = '200';
                $dd['message'] = 'Loged';
                $dd['message'] = 'Loged';
                $dd['id'] = $_SESSION['id'];
                $dd['email'] = $_SESSION['email'];
                $dd['givenName'] = trim(substr($nome, 0, strpos($nome, ' ')));
                $dd['sn'] = trim(substr($nome, strpos($nome, ' '), strlen($nome)));
                $dd['token'] = $_SESSION['apikey'];
                $dd['persistent-id'] = PATH . 'api/socials/apikey/' . $dd['token'];
            } else {
                $dd['error'] = '400';
                $dd['message'] = 'Error Login';
                $dd['content'] = get("user");
            }
        }
        return $dd;
    }
}
