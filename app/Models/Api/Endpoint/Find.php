<?php
/*
@category API
@package Find
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/find/libraries/
@exmaple $URL/api/find/vitrine/
@exmaple $URL/api/find/status/1
@abstract $URL/api/find/libraries/ -> Lista as bibliotecas do Sistema
@abstract API para consulta de metadados de livros com o ISBN
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Find extends Model
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

    function lastitens($d1, $d2)
    {
        $Books = new \App\Models\Find\Books\Db\Books();
        $dt = $Books->lastItens($d1, $d2);
        echo json_encode($dt);
        exit;
    }

    function getID($id)
    {
        $Books = new \App\Models\Find\Books\Db\Books();
        $dt = $Books->getid($id);
        echo json_encode($dt);
        exit;
    }

    function isbn($isbn, $action)
    {
        $RSP = [];
        $RSP['date'] = date("Y-m-dTH:i:s");
        $RSP['verb'] = $action;

        switch ($action) {
            case 'add':
                $FIND = new \App\Models\Find\Books\Db\Find();
                $RSP = $FIND->register($isbn, $RSP);
                break;
        }
        echo json_encode($RSP);
        exit;
    }

    function lastItensStatus($status)
    {
        $Find = new \App\Models\Find\Books\Db\Find();
        $Find->listStatus($status);
    }

    function index($d1, $d2 = '', $d3 = '')
    {
        header('Access-Control-Allow-Origin: *');
        $RSP = [];

        switch ($d1) {
            case 'status':
                $this->lastItensStatus($d2, $d3);
                break;
            case 'isbn':
                $this->isbn($d2, $d3);
                break;
            case 'vitrine':
                $this->lastItens($d2, $d3);
                break;
            default:
                $RSP['status'] = '400';
                $RSP['message'] = 'Serviço não localizado';
                break;
        }
        echo json_encode($RSP);
    }


    /******************************************************** */
    function index2($d1, $d2 = '', $d3 = '')
    {
        header('Access-Control-Allow-Origin: *');
        switch ($d1) {
            case 'check':
                switch ($d2) {
                    case 'post':
                        $this->checkPost();
                        break;

                    case 'get':
                        $this->checkGet();
                        break;
                    default:
                        echo json_encode(array_merge($_POST, $GET));
                        break;
                }
                exit;
                break;
            case 'saveField':
                $this->saveField();
                break;
            case 'status':
                $this->list_status($d2);
                break;
            case 'isbn':
                $library = get("library");
                if ($library == '') {
                    $dd['status'] = '500';
                    $dd['message'] = 'Biblioteca não informada';
                    $dd['time'] = date("Y-m-dTH:i:s");
                    echo json_encode($dd);
                    exit;
                } else {
                    $id = $this->insert_isbn($d2, $library);
                    $dd['status'] = '200';
                    $dd['message'] = 'Insirido com sucesso ' . $id;
                    $dd['rdf'] = $id;
                    echo json_encode($dd);
                }
                exit;
                break;
            case 'getID':
                echo $this->getID($d2);
                exit;
                break;
            case 'getItem':
                echo $this->getItem($d2);
                exit;
                break;
            case  'vitrine':
                echo $this->lastItens();
                exit;
                break;

            case 'libraries':
                $Library = new \App\Models\Find\Library\Index();
                $dt = $Library->listAll();
                echo json_encode($dt);
                exit;
                break;
        }
    }

    function saveField()
    {
        $find = new \App\Models\Find\Books\Db\Books();
        $library = get("library");
        $item = get("item");
        $field = get("field");
        $value = get("value");
        $find->saveData($library, $item, $field, $value);

        echo json_encode($_POST);
        exit;
    }

    function checkPost()
    {
        echo json_encode($_POST);
        exit;
    }

    function checkGet()
    {
        echo json_encode($_POST);
        exit;
    }

    function list_status($sta)
    {
        $library = get("library");
        $find = new \App\Models\Find\Books\Db\Books();
        $find->list_status($library, $sta);
        exit;
    }

    function getItem($id)
    {
        $Books = new \App\Models\Find\Books\Db\Books();
        $dt = $Books->getItem($id);
        pre($dt);
    }


    function insert_isbn($isbn, $library)
    {
        $key = get("apikey");
        $find = new \App\Models\Find\Books\Db\Books();
        $find->insertISBN($isbn, $library, 1);
        return false;
    }
}
