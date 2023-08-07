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
        'bl_ISBN', 'bl_exemplar', 'bl_place'
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
            ->where('bl_library', $DT['library'])
            ->where('bl_ISBN', $DT['isbn'])
            ->where('bl_place', $DT['place'])
            ->findAll();

        if (count($dt) > 0) {
            $ex = $this->exemplar($DT);
        } else {
            $ex = 1;
        }

        $dd = [];
        $dd['bl_library'] = $DT['library'];
        $dd['bl_exemplar'] = $ex;
        $dd['bl_ISBN'] = $DT['isbn'];
        $dd['bl_catalogador'] = $DT['user'];
        $dd['bl_tombo'] = $DT['tombo'];
        $dd['bl_status'] = 1;
        $dd['bl_usuario'] = 0;
        $dd['bl_place'] = $DT['place'];

        $idit = $this->set($dd)->insert();
        $DT['item'] = $idit;
        return $DT;
    }

    function exemplar($DT)
    {
        $dt = $this
            ->where('bl_library', $DT['library'])
            ->where('bl_ISBN', $DT['isbn'])
            ->findAll();
        $tot = count($dt) + 1;
        return $tot;
    }

    function nextTombo($library)
    {
        $dt = $this
            ->select('*')
            ->where('bl_library', $library)
            ->orderBy('bl_tombo DESC')
            ->first();
        if ($dt == '') {
            return 1;
        } else {
            $tombo = round('0' . $dt['bl_tombo']);
            return $tombo;
        }
    }
    function getItens($isbn)
        {
            $dt = $this
                ->join('library', 'id_lb = bl_library')
                ->join('library_place', 'id_lp = bl_place')
                ->where('bl_ISBN',$isbn)
                ->orderBy('lb_name, bl_exemplar')
                ->findAll();
            return $dt;
        }

    function listItem($library, $sta)
    {
        if ($sta == 'A') {
            $dt = $this
                ->where('(bl_status = 0 or bl_status = 1', '')
                ->where('bl_library', $library)
                ->findAll();
        } else {
            $dt = $this
                ->where('bl_status', $sta)
                ->where('bl_library', $library)
                ->findAll();
        }
        return $dt;
    }
}
