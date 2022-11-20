<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Event extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event';
    protected $primaryKey       = 'id_ev';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ev','ev_name','ev_place','ev_ative','ev_permanent',
        'ev_data_start','ev_data_end','ev_deadline',
        'ev_url','ev_description','ev_image'
    ];

    protected $typeFields = [
        'hidden','string*','string','sn*','ns*',
        'date','date','date',
        'url','text','string'
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

    function index($d1='',$d2='',$d3='')
        {
            $this->path = PATH. COLLECTION.'/events';
            $this->path_back = PATH. COLLECTION.'/events';
            $sx = '';
            switch($d1)
                {
                    case 'delete':
                        $this->delete($d2);
                        $sx .= metarefresh($this->path);
                        break;
                    case 'edit':
                        $this->id = $d2;
                        $sx .= form($this);
                        $sx = bs(bsc($sx,12));
                        break;
                    case 'cards':
                        return $this->cards($d2,$d3);
                        break;
                    default:
                        $sx .= tableview($this);
                        $sx = bs(bsc($sx,12));
                        break;
                }
            return $sx;
        }

    function cards($d2,$d3)
        {
            $sx = '';
            $dt = $this
                ->where('ev_ative',1)
                ->where('(ev_data_start >= "'.date("Y-m-d").'" or ev_permanent = 1)')
                ->findAll();
            for($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $img = $line['ev_image'];
                    if (substr($img,0,4) != 'http')
                        {
                            $img = URL.('/img/'.$img);
                        }
                    $date = range_data($line['ev_data_start'],$line['ev_data_end']);
                    $onclick = '';
                    if (strlen($line['ev_url']) > 0)
                        {
                            $onclick = 'onclick="window.open(\''.$line['ev_url'].'\',\'_blank\')"';
                        }

                    $sx .= '<div class="col-md-4 p-1" '.$onclick.'>';
                    $sx .= '<div class="card" style="width: 18rem;">
                            <img src="'.$img.'" class="card-img-top" alt="Event logo">
                            <div class="card-body">
                                <b>'.$line['ev_name'].'</b>
                                <p class="card-text">'.$line['ev_place'].'</p>
                                '.$date.'
                            </div>
                            </div>';
                            $sx .= '</div>';
                }
                $sx = bs($sx);
                return $sx;
        }
}
