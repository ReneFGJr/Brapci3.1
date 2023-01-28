<?php

namespace App\Models\ISBN;

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

    /*********************** isbn
    978-65-89999-01-3
    978 -> Código GTIN-13
    65 -> Código do país
    89999 -> Código do editor
    01 -> Código do título
    3 -> Dígito verificador
    */

    function standard($isbn)
        {
            $l1 = substr($isbn,strlen($isbn)-1,1);
            $l2 = sonumero($isbn);
            if ($l1 =='X')
                {
                    $l2 .= $l1;
                }
            return $l2;
        }

    function format($isbn)
        {
            $isbn = troca($isbn,'ISBN','');
            if (count($isbn) == 13)
            {
                $sx = substr($isbn,0,3).'-'.
                    substr($isbn,3,2).'-'.
                    substr($isbn,5,5).'-'.
                    substr($isbn,10,2).'-'.
                    substr($isbn,12,1);
            } else {
                $sx = '';
            }
            return $sx;
        }
}
