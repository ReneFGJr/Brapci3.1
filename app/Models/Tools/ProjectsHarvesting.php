<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

class ProjectsHarvesting extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_tools.projects_harvesting';
    protected $primaryKey       = 'id_ph';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ph', 'ph_project_id', 'ph_project', 'ph_status', 'updated_at', 'ph_harvested', 'ph_total'
    ];
    protected $typeFields    = [
        'hidden', 'hidden', 'string*', 'hidden',
        'up', 'hidden', 'hidden'
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


    function updateXML($id)
        {
            $ProjectsHarvestingXml = new \App\Models\Tools\ProjectsHarvestingXml();
            $ProjectsHarvestingXml->update_counter($id);
        }
    function harvesting_new($id)
        {
            $idh = $this->select('count(*) as total')->where('ph_project_id',$id)->first();
            $idh = $idh['total']+1;
            $dt = array();
            $dt['ph_project'] = 'Harvesting #' . $idh;
            $dt['ph_project_id'] = $id;
            $dt['ph_status'] = 0;
            $dt['updated_at'] = date("Y-m-d H:i:s");
            $dt['ph_harvested'] = 0;
            $dt['ph_total'] = 0;
            $this->set($dt)->insert();
            return "";
        }

    function btn_harvesting_new($id)
        {
            $sx = '<a href="' . base_url(PATH . COLLECTION. '/lattes/harvesting_new/' . $id) . '" class="btn btn-outline-secondary">';
            $sx .= lang('tools.harvesting_new');
            $sx .= '<a/>';
            return $sx;
        }

    function form($id)
        {
            $ProjectsHarvestingXml = new \App\Models\Tools\ProjectsHarvestingXml();
            $this->updateXML($id);
            $sx = 'FOrm';
            $sx .= form_open();
            $sx .= form_hidden(array('id' => $id));
            $sx .= form_textarea(array('name' => 'ph_project', 'class' => 'form-control', 'rows' => 5));
            $sx .= form_submit(array('name' => 'submit', 'class' => 'btn btn-outline-secondary', 'value' => lang('tools.harvesting_new')));
            $sx .= form_close();
            $txt = get("ph_project");
            if (strlen($txt) > 0)
                {
                    $ln = explode(chr(13),$txt);
                    for ($r=0;$r < count($ln);$r++)
                        {
                            $l = trim($ln[$r]);
                            if (strlen($l) == 16)
                                {
                                    $sx .= $ProjectsHarvestingXml->register($id,$l);
                                    $sx .= '<br>';
                                }
                        }
                }
            return $sx;
        }

    function list($id)
        {
            $dt = $this->where('ph_project_id',$id)->findAll();
            $sx = '';
            $sx .= '<table class="table">';
            $sx .= '<tr class="small">';
            $sx .= '<th width="85%">'.lang('tools.ph_project').'</th>';
            $sx .= '<th width="5%">' . lang('tools.ph_status') . '</th>';
            $sx .= '<th width="5%">' . lang('tools.ph_total') . '</th>';
            $sx .= '<th width="5%">' . lang('tools.ph_harvested') . '</th>';
            $sx .= '</tr>';
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $link = '<a href="'.PATH . COLLECTION. '/lattes/harvested/' . $line['id_ph'].'">';
                    $linka = '</a>';
                    $sx .= '<tr>';
                    $sx .= '<td>'.$link.$line['ph_project'].$linka.'</td>';
                    $sx .= '<td class="text-center">'.$line['ph_status'].'</td>';
                    $sx .= '<td class="text-center">'.$line['ph_total'].'</td>';
                    $sx .= '<td class="text-center">'.$line['ph_harvested'].'</td>';
                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            return $sx;
        }

}
