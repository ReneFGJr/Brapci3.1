<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIDespacho extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_depacho';
    protected $primaryKey       = 'id_dsp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_dsp', 'p_patent_nr', 'p_issue', 'p_comment', 'p_section'
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

    function show($id)
        {
            $dt = $this
                ->join('rpi_issue', 'rpi_issue.rpi_nr = rpi_depacho.p_issue', 'left')
                ->join('rpi_section', 'rpi_section.id_rsec = rpi_depacho.p_section', 'left')
                ->where('p_patent_nr', $id)
                ->findAll();

            $sx = '';
            $sx .= '<table class="table small">';
            $sx .= '<tr>';
            $sx .= '<th width="3%">' . msg('rpi_nr') . '</th>';
            $sx .= '<th width="7%">' . msg('rpi_data') . '</th>';
            $sx .= '<th width="3%">' . msg('rsec_code') . '</th>';
            $sx .= '<th width="80%">' . msg('p_comment') . '</th>';
            $sx .= '</tr>';

            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    //pre($line);
                    $sx .= '<tr>';
                    $sx .= '<td>'.$line['rpi_nr'].'</td>';
                    $sx .= '<td>' . stodbr($line['rpi_data']) . '</td>';
                    $sx .= '<td>' . $line['rsec_code'] . '</td>';
                    $sx .= '<td>' . $line['p_comment'];
                    $sx .= '<br>';
                    $sx .= '<i class="text-secondary">'.$line['rsec_name'].'</i>';
                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            return $sx;
        }

    function register($idp, $id_issue, $id_sec, $coment)
        {
            $dt = $this
                ->where('p_patent_nr', $idp)
                ->where('p_issue', $id_issue)
                ->where('p_section', $id_sec)
                ->where('p_comment', $coment)
                ->findAll();

            if (count($dt) == 0) {
                $data['p_patent_nr'] = $idp;
                $data['p_issue'] = $id_issue;
                $data['p_section'] = $id_sec;
                $data['p_comment'] = $coment;
                $this->insert($data);
            }
        }
}
