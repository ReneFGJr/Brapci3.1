<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class BooksLibrary extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'book_library';
    protected $primaryKey       = 'id_bl';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bl', 'bl_library', 'bl_item',
        'bl_tombo', 'bl_catalogador', 'bl_status',
        'bl_emprestimo', 'bl_renovacao', 'bl_usuario',
        'bl_expression'
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

    function register($DT)
        {
            $dt = $this
                ->where('bl_library',$DT['library'])
                ->where('bl_expression', $DT['expression'])
                ->where('bl_tombo', $DT['tombo'])
                ->findAll();
            if (count($dt) == 0)
                {
                    $ex = $this->exemplar($DT);

                    $dd = [];
                    $dd['bl_library'] = $DT['library'];
                    $dd['bl_exemplar'] = $ex;
                    $dd['bl_expression'] = $DT['expression'];
                    $dd['bl_catalogador'] = $DT['user'];
                    $dd['bl_tombo'] = $DT['tombo'];
                    $dd['bl_status'] = 1;
                    $dd['bl_usuario'] = 0;
                    $idit = $this->set($dd)->insert();
                    $DT['item'] = $idit;
                    return $DT;
                } else {

                }
            return $DT;
        }

    function exemplar($DT)
        {
        $dt = $this
            ->where('bl_library', $DT['library'])
            ->where('bl_expression', $DT['expression'])
            ->findAll();
        $tot = count($dt)+1;
        return $tot;
        }

    function nextTombo($library)
        {
            $dt = $this
                ->select('*')
                ->where('bl_library',$library)
                ->orderBy('bl_tombo DESC')
                ->first();
            if ($dt == '')
                {
                    return 1;
                } else {
                    $tombo = round('0'.$dt['bl_tombo']);
                    return $tombo;
                }

        }
}
