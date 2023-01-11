<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIIssue extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_issue';
    protected $primaryKey       = 'id_rpi';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_rpi', 'rpi_nr', 'rpi_data', 'rpi_status'
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

    function summary($id)
        {
            $RDPDespacho = new \App\Models\Patent\RPIDespacho;
            $dt = $RDPDespacho
                ->select("count(*) as total, rsec_name, rsec_code")
                ->join('rpi_section', 'id_rsec = p_section', 'left')
                //->where('p_issue', $id)
                ->groupBy('rsec_name, rsec_code')
                ->orderBy('rsec_code')
                ->findAll();
            $sx = '<ul>';
            for ($r=0;$r < count($dt);$r++)
            {
                $line = $dt[$r];
                $sx .= '<li>';
                $sx .= '('.$line['rsec_code'].') ';
                $sx .= $line['rsec_name'];
                $sx .= ' - ';
                $sx .= $line['total'];
                $sx .= '</li>';
            }
            $sx .= '</ul>';

            return $sx;
        }

    function viewissue($id)
        {
            $dt = $this->find($id);
            if ($dt == '')
                {
                    echo "ISSUE NOT FOUND";
                    exit;
                }
            $dt['rpi_title'] = 'Revista da Propriedade Industrial';
            $dt['summary'] = $this->summary($id);
            $dt['action'] = $this->action($dt);
            $sx = '';
            $sx .= view('Patent/issue',$dt);
            return $sx;
        }

    function panel()
        {
            $sx = '';
            $dt = $this->findAll();
            /* Mountal Panel */
            $pnl = array();
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $year = trim(substr($line['rpi_data'],0,4));
                    if ($year == '')
                        {
                            $year = '0000';
                        }
                    $pnl[$year][] = $line;
                }

            foreach($pnl as $year=>$issue)
                {
                    if ($year == '0000')
                        {
                            $sx .= bsc(msg('patent.no_year'),1);
                        } else {
                            $sx .= bsc($year,1);
                        }
                        $issues = array();
                        for ($r=0;$r < count($issue);$r++)
                            {
                                $nr = $issue[$r]['rpi_nr'];
                                $link = '<a href="'.PATH.COLLECTION. '/viewissue/'.$issue[$r]['id_rpi'].'">'.$nr.'</a>';
                                $issues[$nr] = $link;
                            }
                        krsort($issues);
                        $sa = '';
                        foreach($issues as $nr=>$link)
                            {
                                $sa .= $link.' ';
                            }
                        $sx .= bsc($sa,11);
                }
            $sx = bs($sx);
            return $sx;
        }

    function register_id($id,$status)
        {
        $dt = $this->where('id_rpi', $id)->findAll();
        if (count($dt) > 0) {
            $data['rpi_status'] = $status;
            $this->set($data)->where('id_rpi', $id)->update();
            return $dt[0]['id_rpi'];
        }

        }

    function action($dt)
        {
        $sx = '';
        $rpi_nr = $dt['rpi_nr'];
        $rpi_status = $dt['rpi_status'];

        switch ($rpi_status) {
            case '0':
                $link = '<a href="' . base_url(PATH . '/patente/harvesting/' . $rpi_nr) . '" class="">';
                $linka = '</a>';
                $sx .=  $link . bsicone('harvesting', 32) . $linka;
                break;
            case '2':
                $link = '<a href="' . base_url(PATH . '/patente/proccess/' . $rpi_nr) . '" class="">';
                $linka = '</a>';
                $sx .=  $link . bsicone('download', 32) . $linka;
                break;
            case '3':
                $link = '<a href="' . base_url(PATH . '/patente/proccess/' . $rpi_nr) . '" class="">';
                $linka = '</a>';
                $sx .=  $link . bsicone('download', 32) . $linka;
                break;
            case '4':
                $link = '<a href="' . base_url(PATH . '/patente/proccess/' . $rpi_nr) . '" class="">';
                $linka = '</a>';
                $sx .=  $link . bsicone('download', 32) . $linka;
                break;
            case '5':
                $link = '<a href="' . base_url(PATH . '/patente/proccess/' . $rpi_nr) . '" class="">';
                $linka = '</a>';
                $sx .=  $link . bsicone('download', 32) . $linka;
                break;
            default:
                $sx .= 'Status: ' . $rpi_status;
                break;
        }
        return $sx;
        }

    function register($issue,$status=0)
    {
        $dt = $this->where('rpi_nr', $issue)->findAll();
        if (count($dt) > 0) {
            $data['rpi_status'] = $status;
            $this->set($data)->where('rpi_nr', $issue)->update();
            return $dt[0]['id_rpi'];
        } else {
            $data['rpi_nr'] = $issue;
            $data['rpi_status'] = $status;
            return $this->insert($data);
        }
    }

    function harvesting($id)
    {
        $sx = '';
        $dir = '../.tmp/.inpi/patent/';
        $filename = $dir . 'P' . strzero($id, 4) . '.zip';

        if (file_exists($filename)) {
            $sx .= bsmessage('Cached ' . $filename, 1);
        } else {
            $RPI = new \App\Models\Patent\RPI;
            $url = $RPI->url . 'P' . strzero($id, 4) . '.zip';
            $sx .= h($url);

            $file = read_link($url);
            $sx .= h(strlen($file));

            dircheck('../.tmp/');
            dircheck('../.tmp/.inpi/');
            dircheck('../.tmp/.inpi/patent/');

            if (strlen($file) > 1000) {
                $this->register($id,1);
                file_put_contents($filename, $file);
                $sx .= bsmessage('Harvested ' . $id, 1);
                $this->register($id, 1);
            } else {
                $sx .= bsmessage('Error Harvested ' . $id, 3);
                return $sx;
            }
        }

        $filename_txt = $dir . 'P' . strzero($id, 4) . '.txt';
        if (!file_exists($filename_txt)) {
            $zip = new \ZipArchive();
            $res = $zip->open($filename);
            if ($res === TRUE) {
                $zip->extractTo($dir);
                $zip->close();
            }
            $sx .= bsmessage('Unziped ' . $filename, 1);
            $this->register($id, 2);
            $sx .= metarefresh('',5);
        } else {
            $this->register($id, 2);
        }

        /***************************************************** RETURN */
        return $sx;
    }
}
