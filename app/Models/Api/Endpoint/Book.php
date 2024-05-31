<?php
/*
@category API
@package Brapci Book
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $URL/api/book/?isbn=97800000000
@abstract API para consulta de metadados de livros com o ISBN
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Book extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'books';
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

    function index($d1, $d2, $d3, $d4)
    {echo "OK";
    exit;
        $RSP = [];
        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }
        switch ($d2) {
            case 'vitrine':
                $Book = new \App\Models\Base\Book();
                $dt = $Book->vitrine('');
                echo json_encode($dt);
                exit;
                break;
            case 'submit':
                $PS = array_merge($_POST, $_GET);
                $booksSubmit = new \App\Models\Books\BooksSubmit();
                $booksSubmit->register($PS);
                $dt = [];
                $dt['status'] = '200';
                $dt['message'] = 'Registro salvo';
                $dt['data'] = $PS;
                echo json_encode($dt);
                exit;
                break;
            default:
                $RSP['status'] = '500';
                $RSP['message'] = 'Método não existe - ' . $d2;
                $ISBN = new \App\Models\Functions\Isbn();
                $isbn = get("isbn");
                if ($isbn != '') {
                    $BooksServer = new \App\Models\BooksServer\Books();
                    $RSP = $BooksServer->search($isbn, '');
                }
                break;
        }
        echo json_encode($RSP);
        exit;
    }
}
