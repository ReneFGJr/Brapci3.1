<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Sources extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'source_source';
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

    function search_source()
    {
        if (isset($_SESSION['sj'])) {
            $sj = (array)json_decode($_SESSION['sj']);
        } else {
            $sj = array();
        }


        $dt = $this
            ->orderBy("jnl_collection, jnl_name")
            ->FindAll();
        $sx = '';

        $xcollection = '';
        $sx .= '<ul style="list-style-type: none;">';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $id = $line['id_jnl'];

            $check = '';
            if (isset($sj[$id])) {
                if ($sj[$id] == 1) {
                    $check = 'checked';
                }
            }
            $collection = trim($line['jnl_collection']);
            if ($collection != $xcollection) {
                $xcollection = $collection;
                $sx .= h(lang('brapci.' . $collection), 4);
            }
            $sx .= '<li>';
            $sx .= '<input type="checkbox" id="jnl_' . $id . '" ' . $check . ' class="me-2" onclick="markSource(' . $id . ',this);">';
            $sx .= $line['jnl_name'];
            if (strlen(trim($line['jnl_issn'])) > 0) {
                $sx .= ' (ISSN ' . $line['jnl_issn'] . ')';
            }
            $sx .= '</>';
        }
        $sx .= '</ul>';
        return $sx;
    }
}