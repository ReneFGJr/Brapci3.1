<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class News extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'news';
    protected $primaryKey       = 'id_nw';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_nw', 'nw_data', 'nw_title',
        'nw_description', 'nw_build',
    ];

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

    function news($limit = 10)
        {
            $sx = '';
            $dt = $this->orderBy('nw_data desc')->findAll(10);
            $xver = '';
            foreach($dt as $id=>$line)
                {
                    $ver = $line['nw_build'];
                    if ($ver != $xver)
                        {
                            $sx .= '<span class="fw-bold mt-2" style="font-size: 1.4em; weigth: bold;">'.$ver.'</span><br>';
                            $xver = $ver;
                        }
                    $sx .= '<span class="mb-2 bulletIT small" title="'.$line['nw_description'].'">'.$line['nw_title'].'</span><br> ';
                }
            $dd['text'] = $sx;
            $dd['status'] = '200';
            return $dd;
        }
}
