<?php

namespace App\Models\Find\Books\Db;

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

    function register($isbn, $RSP = [])
    {
        $isbn = sonumero($isbn);

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

        /***************************************** CHECK ISBN */
        $ISBN = new \App\Models\Functions\Isbn();
        $check = $ISBN->check($isbn);

        $RSP['isbn'] = $isbn;

        /******** Inser ISBN na Base */
        if ($check) {
            $BooksExpression = new \App\Models\Find\Books\Db\BooksExpression();
            /***************** Checa se ja existe na base */
            if (!$BooksExpression->existISBN($isbn)) {
                /* Obra não existe */

                /************* Consulta ISBNdb */
                $ISBNdb = new \App\Models\ISBN\Isbndb\Index();
                $djson = $ISBNdb->search($isbn);
                $dt = (array)json_decode($djson);

                if (isset($dt['book'])) {
                    $dt = (array($dt['book']));
                    $dt = $ISBNdb->convert($dt);
                    $dt['status'] = 3;
                    $RSP = $BooksExpression->register($RSP,$dt);
                    pre($RSP);
                }
                echo "FIM";
                exit;
                $RSP = $BooksExpression->registerEmpty($isbn);

            } else {
                $RSP['status'] = '201';
                $RSP['message'] = 'ISBN Já existente';
                $RSP['isbn'] = $isbn;
            }
        } else {
            $RSP['status'] = '200';
        }
        pre($RSP);
        return $RSP;
    }

    function listStatus($sta)
    {
        $RSP = [];
        /******************************************* CHECK LIBRATY */
        $Libraries = new \App\Models\Find\Books\Db\Library();
        $RSP = $Libraries->checkLibrary($RSP);
        if ($RSP['status'] == '200') {

            /* Lista por usuário */

            /* Biblioteca Informada */
            $library = get("library");
            $BooksLibrary = new \App\Models\Find\Books\Db\BooksLibrary();
            $RSP = $BooksLibrary->listItem($library, $sta);
        }
        echo json_encode($RSP);
        exit;
    }
}
