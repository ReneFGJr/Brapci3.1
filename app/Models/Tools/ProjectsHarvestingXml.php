<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

class ProjectsHarvestingXml extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_tools.projects_harvesting_xml';
    protected $primaryKey       = 'id_hx';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'hx_project', 'hx_name','hx_id_lattes', 'hx_status', 'hx_updated', 'created_at', 'updated_at'
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

    function resume($prj)
    {
        $dt = $this
            ->select('count(*) as total, hx_status')
            ->where('hx_project', $prj)
            ->groupBy('hx_status')
            ->findAll();
        $sta = array(0, 0, 0, 0, 0, 0, 0);
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sta[$line['hx_status']] = $line['total'];
        }
        $sx = '<ul>';
        $reg = 0;
        for ($r = 0; $r < count($sta); $r++) {
            if ($sta[$r] > 0) {
                $reg++;
                $sx .= '<li>' . lang('brapci.harvesting_status_' . $r) . ' (' . $sta[$r] . ')</li>';
            }
        }
        $sx .= '</ul>';
        if ($reg == 0) {
            $sx = bsmessage(lang('brapci.lattes_register_nothing'));
        }
        return $sx;
    }

    function inport($ln,$prj)
        {
            $Lattes = new \App\Models\Api\Lattes\Index();
            $sx = '';
            $ln = troca($ln, chr(10), '');
            $ln = troca($ln, chr(8), '');
            $ln = troca($ln,chr(13),';');
            $ln = troca($ln, chr(10), ';');
            $ln = troca($ln,';;',';');
            $ln = explode(';',$ln);
            $sx .= '<ul>';
            $sf = '';
            for ($r=0;$r < count($ln);$r++)
                {
                    $l = substr(trim(sonumero($ln[$r])),0,16);
                    if (strlen($l) > 0)
                        {

                            $valid = '';
                            if ($Lattes->checkID($l) == 1)
                                {
                                    $valid = ' <span style="color: green">';
                                    $valid .= '<b>OK</b>';
                                    $valid .= '</span>';

                                    $this->register($prj,$l);
                                } else {
                                    $valid = ' <span style="color: red">';
                                    $valid .= '<b>ERRO</b>';
                                    $valid .= '</span>';
                                    $sf .= $l.chr(13);
                                }
                            $sx .= '<li>' . $l . $valid. '</li>';
                        }

                }
            $sx .= '</ul>';
            $_POST['lattes'] = $sf;
            return $sx;
        }

    function harvesting($id)
        {
            $LattesDados = new \App\Models\LattesExtrator\LattesDados();

            $sx = '';
            $LT = new \App\Models\LattesExtrator\Index();
            $dt = $LT->harvesting($id);
            $sx .= '<br>Harvesting '.$id;
            $file_xml = '../.tmp/lattes/'.$id.'.xml';
            if (file_exists($file_xml))
                {
                    $sx .= '<br>File XML OK';
                } else {
                    $sx .= '<br>File ERRO OK';
                }

            /******************** Update */
            $dta = $LattesDados->le_id($id);
            if (count($dta) == 0)
                {
                    echo "ERRO: registro vazio";
                    exit;
                }
            $dt = array();
            $dt['hx_name'] = $dta['lt_name'];
            $dt['hx_updated'] = $dta['lt_atualizacao'];
            $dt['updated_at'] = date("Y-m-d H:i:s");
            $dt['hx_status'] = 1;

            $this->set($dt)->where('hx_id_lattes',$id)->update();
            echo $sx;
            exit;
        }

    function list($prj)
    {
        $sx = '';
        $dt = $this
            ->where('hx_project', $prj)
            ->orderBy('hx_name, hx_id_lattes')
            ->findAll();
        $sx .= '<table class="table" style="width: 100%">';
        $sx .= '<tr>
                <th>IDLattes</th>
                <th>Nome</th>
                <th>ShortKey</th>
                <th>Status</th>
                <th>Update</th>
                </tr>';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $link = '<a href="'.PATH.COLLECTION.'/lattes/viewid/'. $line['id_hx'].'">';
            $linka = 'X</a>';
            $sx .= '<tr>';
            $sx .= '<td>' . $line['hx_id_lattes'] . '</td>';
            $sx .= '<td>' . $link.$line['hx_name'] . $linka.'</td>';
            $sx .= '<td>' . $line['hx_key'] . '</td>';
            $sx .= '<td>' . lang('brapci.harvesting_status_' . $line['hx_status']) . '</td>';
            $sx .= '<td>' . stodbr($line['hx_updated']) . '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        return $sx;
    }

    function getXML($id)
    {
        $sx = '===>' . $id;
        $file = '../.tmp/xml/' . $id . '.xml';
        if (file_exists($file)) {
            $sx = file_get_contents($file);
        } else {
            $dt = $this
                ->where('hx_project', $id)
                ->where('hx_status', 0)
                ->first();
            if ($dt != '') {
                $id_lattes = $dt['hx_id_lattes'];
                $sx .= $id_lattes;
                $url = 'https://brapci.inf.br/ws/api/?verb=lattes&q=' . $id_lattes;
                $sx .= h($url);
            }
        }
        return $sx;
    }

    function btn_harvesting($id)
    {
        $link = PATH . COLLECTION . '/lattes/harvest/' . $id;
        $sx = '<a href="' . $link . '">Harvesting</a>';
        return $sx;
    }

    function update_counter($id)
    {
        $total = 0;
        $harvested = 0;

        $dt = $this
            ->select("count(*) as total, hx_status")
            ->where('hx_project', $id)
            ->groupBy('hx_status')
            ->findAll();

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $total = $total + $line['total'];
            if ($line['hx_status'] == 1) {
                $harvested = $line['total'];
            }
        }

        $ProjectsHarvesting = new \App\Models\Tools\ProjectsHarvesting();
        $data['ph_total'] = $total;
        $data['ph_harvested'] = $harvested;
        $ProjectsHarvesting->set($data)->where('id_ph', $id)->update();
        return "";
    }

    function register($idp, $lattes)
    {
        $sx = '';
        $dt = $this->where('hx_id_lattes', $lattes)->where('hx_project', $idp)->findAll();
        if (count($dt) == 0) {
            $dt['hx_project'] = $idp;
            $dt['hx_id_lattes'] = $lattes;
            $dt['hx_status'] = 0;
            $dt['hx_updated'] = '1900-01-01 00:00:00';
            $this->set($dt)->insert();
            $sx .= $lattes . ' inserted';
        } else {
            $sx .= $lattes . ' skipped';
        }
    }
}
