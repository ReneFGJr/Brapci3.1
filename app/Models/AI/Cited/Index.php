<?php

namespace App\Models\AI\Cited;

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

    var $version = '0.28';

    function index()
        {

        }

    function show($id)
        {
            $sx = '';
            $sx .= '<div class="btn btn-outline-primary mt-2" style="width: 100%;">';
            $sx .= '<table width="100%">';
            $sx .= '<tr><td>';
            $sx .= 'NLP';
            $sx .= '</td><td>';
            $sx .= $this->version;
            $sx .= '</td><td>';
            $sx .= '</table>';
            $sx .= '</div>';
            return $sx;

        }
}
