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

    function isbn($isbn, $action = '')
    {
        $RSP = [];
        $RSP['date'] = date("Y-m-dTH:i:s");
        $RSP['verb'] = $action;
        $RSP['library'] = get("library");
        $FIND = new \App\Models\Find\Books\Db\Find();

        switch ($action) {
            case 'add':
                $RSP = $FIND->register($isbn, $RSP);
                $RSP2 = $FIND->getISBN($isbn);
                $FIND = array_merge($RSP, $RSP2);
                break;
            default:
                $RSP = $FIND->getISBN($isbn);
                $RSP['status'] = get("library");
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

    function getISBN($isbn)
    {
        $Find = new \App\Models\Find\Books\Db\Find();
        $Find->getISBN($isbn);
    }

    function saveField()
    {
        $find = new \App\Models\Find\Books\Db\Find();
        $library = get("library");
        $item = get("item");
        $field = get("field");
        $value = get("value");
        echo json_encode($find->saveData($library, $item, $field, $value));
        exit;
    }

    function getPlace($lib)
    {
        $LibraryPlace = new \App\Models\Find\Books\Db\LibraryPlace();
        echo json_encode($LibraryPlace->listPlaces($lib));
        exit;
    }

    function libraries()
    {
        $Library = new \App\Models\Find\Books\Db\Library();
        $dt = $Library->libraries();
        return $dt;
    }

    function check()
    {
        $RSP = [];
        /******************************************* CHECK LIBRATY */
        $Libraries = new \App\Models\Find\Books\Db\Library();
        $RSP = $Libraries->checkLibrary($RSP);
        if ($RSP['status'] != '200') {
            return $RSP;
        }

        /******************************************* CHECK USUARIO */
        $UserApi = new \App\Models\Find\Books\Db\UserApi();
        $RSP = $UserApi->checkUser();
        if ($RSP['status'] != '200') {
            return $RSP;
        }
        $RSP['user'] = $UserApi->user;
        return $RSP;
    }

    function putItemLibrary()
    {
        $RSP = $this->check();
        if ($RSP['status'] == '200') {
            $vars = ['library', 'tombo', 'place', 'isbn'];
            foreach ($vars as $id => $var) {
                if (get($var) == '') {
                    $RSP = [];
                    $RSP['status'] = '202';
                    $RSP['message'] = 'Campo ' . $var . ' está vazio';
                }
            }
            if ($RSP['status'] == '200') {
                $DT = [];
                $DT['library'] = get("library");
                $DT['tombo'] =  get("tombo");
                $DT['place'] =  get("place");
                $DT['isbn'] =  get("isbn");
                $DT['user'] =  $RSP['user']['id_us'];
                $BooksLibrary = new \App\Models\Find\Books\Db\BooksLibrary();
                $RSP = $BooksLibrary->register($DT);

                $RSP = $this->isbn($DT['isbn']);
            }
        }
        echo json_encode($RSP);
        exit;
    }

    function cover($isbn, $action = '')
    {
        $Cover = new \App\Models\Find\Books\Db\Cover();
        $RSP = [];
        $RSP['cover'] = 'Cover';
        switch ($action) {
            case 'upload':
                $RSP = $this->check();
                if ($RSP['status'] == '200') {
                    $data = get('data');
                    $RSP = $Cover->saveDataCover($isbn, $data);
                    $RSP['status'] = '200';
                }
                break;
            default:
                //$RSP['cover'] = $Cover->cover($isbn);
                $RSP['action'] = $action;
                break;
        }
        echo json_encode($RSP);
        exit;
    }

    function index($d1, $d2 = '', $d3 = '')
    {
        header('Access-Control-Allow-Origin: *');
        //header("Content-Type: application/json");

        $RSP = [];

        switch ($d1) {
            case 'cover':
                $RSP = $this->cover($d2, $d3);
                break;
            case 'libraries':
                $RSP['data'] = $this->libraries();
                break;
            case 'putItemLibrary':
                $this->putItemLibrary();
                break;
            case 'getISBN':
                $this->getISBN($d2);
                break;
            case 'getPlace':
                $lib = get('library');
                if ($lib == '') {
                    $lib = $d2;
                }
                $this->getPlace($lib);
                break;
            case 'status':
                $this->lastItensStatus($d2, $d3);
                break;
            case 'isbn':
                $this->isbn($d2, $d3);
                break;
            case 'saveField':
                $this->saveField($d2, $d3);
                break;
            case 'vitrine':
                $this->lastItens($d2, $d3);
                break;
            default:
                $RSP = $this->services($RSP);
                $RSP['verb'] = $d1;
                break;
        }
        echo json_encode($RSP);
        exit;
    }

    function services($RSP)
    {
        $srv = [];
        $srv['livros'] = [
            'libraries' => 'libraries',
            'vitrine' => 'vitrine',
            'isbn' => 'isbn',
            'getISBN' => 'getISBN',
            'getPlace' => 'getPlace',
        ];
        $RSP['services'] = $srv;
        return $RSP;
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
                        echo json_encode(array_merge($_POST, $_GET));
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
                echo $this->lastItens(1, 1);
                exit;
                break;

            case 'libraries':
                $Library = new \App\Models\Find\Library\Index();
                $dt = $Library->listAll();
                $RSP['data'] = $dt;
                return $RSP;
                break;
        }
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
        echo "GETITEM";
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
