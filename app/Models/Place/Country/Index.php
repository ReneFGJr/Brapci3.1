<?php

namespace App\Models\Place\Country;

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


    function flag($place,$size=32)
        {
            $dir = URL.'/img/flags/';
            $flags = array('br'=>'flag-brazil');
            if (isset($flags[$place]))
                {
                    $img = $dir.$flags[$place];
                } else {
                    $img = $dir . 'no-flag.svg';
                }

            if ($size == 'fluid')
                {
                    $img = '<img src="'.$img.'" class="img-fluid">';
                } else {
                    $img = '<img src="' . $img . '" style="width: '.$size.'">';
                }
            return $img;
        }
}
