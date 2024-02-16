<?php
/*
@category API
@package BrCris
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY
@access public/private/apikey
@example $URL/api/brapci/services
@example $URL/api/brapci/search?q=TERM&di=1972&df=2023
*/
namespace App\Models\API\Endpoint;
//https://brcris.ibict.br/vivo/individual/pers_c888cc57-bd54-4db4-b1ff-b91bccf83b2d/pers_c888cc57-bd54-4db4-b1ff-b91bccf83b2d.rdf

use CodeIgniter\Model;

class Brcris extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brcris';
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
}
