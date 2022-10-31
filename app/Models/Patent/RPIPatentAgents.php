<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIPatentAgents extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_patent_agents';
    protected $primaryKey       = 'id_pag';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pag',
        'pag_patent',
        'pag_agent', 'pag_type', 'updated_at'
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

    function register($patent, $ag, $type)
    {
        $dt = $this->where('pag_patent', $patent)->where('pag_type', $type)->where('pag_agent', $ag)->findAll();

        if (count($dt) == 0) {
            $data = array();
            $data['pag_patent'] = $patent;
            $data['pag_type'] = $type;
            $data['pag_agent'] = $ag;
            $id = $this->insert($data);
        } else {
            $id = $dt[0]['id_pag'];
        }
        return $id;
    }

    function show_proccess($id_ag)
        {
            $sx = '';
            $dt = $this
                ->join('rpi_patent_nr', 'pag_patent = id_p', 'left')
                ->where('pag_agent', $id_ag)
                ->orderBy('p_year DESC, p_nr')
                ->findAll();
            $xyear = '';
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $year = $line['p_year'];
                    if ($year != $xyear)
                        {
                            $xyear = $year;
                            $sx .= h($year,3);
                        }
                    $sx .= $line['p_nr'].'<br>';
                }
            return $sx;
        }
}
