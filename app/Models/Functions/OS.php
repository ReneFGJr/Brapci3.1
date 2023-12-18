<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class OS extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'os';
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

    function resume()
        {
            $sx = h(msg('brapci.OS'), 4);
            $df = disk_free_space("/");
            $dt = disk_total_space("/");
            $alert = '';

            $pl = $df/$dt;
            $alert = ' ('.number_format($pl*100,1,'.',',').'%'.')';
            if ($pl < 0.1)
                {
                    $alert = '<span class="text-danger">'.$alert.' '.bsicone('tools').'</span>';
                }

            $df = $this->HumanSize($df);
            $dt = $this->HumanSize($dt);

            $sx .= '<ul class="small">';
            $sx .= '<li>Disco: ' . $dt . '</li>';
            $sx .= '<li>Espaço livre: '.$df.$alert.'</li>';
            $sx .= '</ul>';
            return $sx;
        }

    function HumanSize($Bytes)
    {
        $Type = array("", "kilo", "mega", "giga", "tera", "peta", "exa", "zetta", "yotta");
        $Type = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
        $Index = 0;
        while ($Bytes >= 1024) {
            $Bytes /= 1024;
            $Index++;
        }
        return ("" . number_format($Bytes,1,'.',',') . " " . $Type[$Index] . "bytes");
    }
}
