<?php

namespace App\Models\Base\Admin;

use CodeIgniter\Model;

class Reports extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'reports';
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
        $sx = '';
        switch ($d1) {
            case 'catalog_manutention':
                $sx .= $this->catalog_manutention($d2, $d3);
                break;

            default:
                $sx = "CMD - $d1";
                break;
        }
        $sx = bs(bsc($sx, 12));
        return $sx;
    }

    function catalog_manutention($d1, $d2)
    {
        $sx = '';
        switch ($d1) {
            case 'revision':
                $RDFData = new \App\Models\Rdf\RDFData();
                $sx .= $RDFData->report('index');
                break;
            default:
                $sx = h(lang('brapci.reports'), 5);
                $sx .= '<ul>';
                $opt = array('revision');
                foreach ($opt as $id => $type) {
                    $link = '<a href="' . PATH . '/admin/reports/catalog_manutention/' . $type . '">';
                    $linka = '</a>';
                    $sx .= '<li>' . $link . lang('brapci.' . $type) . $linka . '</li>';
                }
                $sx .= '</ul>';
        }
        return $sx;
    }
}
