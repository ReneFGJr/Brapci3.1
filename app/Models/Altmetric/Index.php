<?php

namespace App\Models\Altmetric;

use CodeIgniter\Model;

class Index extends Model
{
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function plum($doi = '')
    {
        $sx = '';
        if (strlen($doi) > 0) {
            $sx .= '<span id="plum" style="font-size: 70%;">Plum X Metrics</span><br/>';
            $sx .= '<a href="https://plu.mx/plum/a/?doi=' . $doi . '" class="plumx-plum-print-popup img-fluid"></a>';
            $sx .= '<script type="text/javascript" src="//cdn.plu.mx/widget-all.js"></script>';
            $sx .= '<script>window.__plumX.widgets.init();</script>';
        }
        return ($sx);
    }

    function altmetrics($doi = '')
    {
        if ((strlen($doi) == 0) or (substr($doi, 0, 2) != '10')) {
            //echo '==ERRO=='.$doi;
            return ("");
        }
        //$doi = '10.1590/2318-08892018000300005';
        $rs = $this->harvested($doi);
        $ar = (array)json_decode($rs);

        if (isset($ar['images'])) {
            $url = $ar['details_url'];
            $link = '<a href="' . $url . '" target="_new">';
            $linka = '</a>';
            $img = '<span style="font-size: 70%;">Altmetrics</span><br>' . $link . '<img src="' . $ar['images']->medium . '">' . $linka;
        } else {
            $img = '';
        }
        return ($img);
    }
}
