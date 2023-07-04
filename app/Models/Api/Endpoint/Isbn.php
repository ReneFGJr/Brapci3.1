<?php
/*
@category API
@package ISBN
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2023 CC-BY
@access public/private/apikey
@example $URL/api/isbn/97800000000
@abstract API validação e consulta do ISBN
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Isbn extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = '*';
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
        $ISBN = new \App\Models\Functions\Isbn();
        $isbn = trim(sonumero($d1));

        $rst = [];
        $rst['valid'] = 0;
        if (strlen($isbn) == 13)
            {
                $chk = $ISBN->genchksum13(substr($isbn, 0, 12));
                $rst['check'] = $chk;
                $rst['valid'] = (substr($isbn,12,1) == $chk);
                $rst['isbn13'] = $isbn;
                $rst['isbn10'] = $ISBN->isbn13to10($isbn);
            } elseif (strlen($isbn) == 10) {
                $isbn10 = $isbn;
                $isbn = $ISBN->isbn10to13($isbn);
                if ($isbn10 == $ISBN->isbn13to10($isbn))
                    {
                        $rst['valid'] = (substr($isbn, 12, 1) == $ISBN->genchksum13(substr($isbn, 0, 12)));
                        $rst['isbn13'] = $isbn;
                        $rst['isbn10'] = $ISBN->isbn13to10($isbn);
                    }

            } else {
                $rst ['valid'] = false;
            }
        header('Access-Control-Allow-Origin: *');
        echo  json_encode($rst);
        exit;
    }
}