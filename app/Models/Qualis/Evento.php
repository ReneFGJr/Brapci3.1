<?php

namespace App\Models\Qualis;

use CodeIgniter\Model;

class Evento extends Model
{
    protected $DBGroup          = 'capes';
    protected $table            = 'qualis_event';
    protected $primaryKey       = 'id_ev';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ev','ev_name','ev_year_start','ev_year_end','updated_at'
    ];
    protected $typeFields    = [
        'hidden', 'string', 'year', 'year', 'now'
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

    function options()
        {
            $dt = $this->orderby("ev_year_start DESC")->FindAll();
            $rst = array(''=>'');
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $rst[$line['id_ev']] = $line['ev_name'];
                }
            return $rst;
        }

    function index($d1,$d2)
        {
            $sx = 'QUALIS_EVENTO';
            $this->path = PATH.COLLECTION. '/qualis/event';
            $this->path_back = $this->path;
            switch($d1)
                {
                    case 'viewid':
                        $this->id = $d2;
                        $sx .= $this->viewid($d2);
                        break;

                    case 'edit':
                        $this->id = $d2;
                        $sx .= form($this);
                        break;
                    default:
                        $sx = tableview($this);
                }
            return $sx;
        }
}
