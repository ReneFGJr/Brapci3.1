<?php

namespace App\Models\Api\Lattes;

use CodeIgniter\Model;

class KtoN extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'k_to_n';
    protected $primaryKey       = 'id_kn';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kn ', 'kn_idk', 'kn_idn', 'kn_status', 'updated_at'
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

    // https://buscatextual.cnpq.br/buscatextual/visualizacv.do?id=K4518324E6
    // https://buscatextual.cnpq.br/buscatextual/visualizacv.do?id=K9336847Z6

    function resume()
        {
            $sx = '';
            $dt = $this
                ->select('count(*) as total, kn_status')
                ->groupBy('kn_status')
                ->orderBy('total desc')
                ->findAll();
            $sx = '<table width="100%" class="table" style="font-size: 0.7em;>';
            $sx .= '<tr><th colspan=2"><th>'.lang('brapci.lattes_kton').'</th></tr>';
            foreach($dt as $id=>$line)
                {
                    $sx .= '<tr>';
                    $sx .= '<td width="80%">';
                    $sx .= lang('brapci.lattes_kton_'.$line['kn_status']);
                    $sx .= '</td>';

                    $sx .= '<td width="20%" class="text-end">';
                    $sx .= $line['total'];
                    $sx .= '</td>';

                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            return $sx;
        }

    function convert_KtoN($n)
    {
        $n = trim($n);
        $rsp = array();
        if (substr($n, 0, 1) != 'K') {
            $rsp['erro'] = '400';
            $rsp['message'] = 'Código ' . $n . ' é inválido';
            echo json_encode($rsp);
            exit;
        }

        $dir = substr($n, 0, 2) . '/' . substr($n, 2, 3) . '/' . substr($n, 5, 3) . '/';
        $path = getenv('app.lattes.apoio');
        $filename = $path . $dir . $n . '.txt';

        if (!file_exists($filename)) {
            $rsp['status'] = '403';
            $rsp['message'] = 'Código ' . $n . ' é não localizado';
            $this->register($n);
            echo json_encode($rsp);
            exit;
        } else {
            $txt = file_get_contents($filename);
            $t = explode(',', $txt);
            $rsp['status'] = '200';
            $rsp['lattes_id'] = $t[0];
            echo json_encode($rsp);
            exit;
        }
        exit;
    }

    function register($k)
        {
            $dt = $this->where('kn_idk',$k)->findAll();
            if (count($dt) == 0)
                {
                    $data['kn_idk'] = $k;
                    $data['kn_idn'] = '';
                    $data['kn_status'] = 1;
                    $this->set($data)->insert();
                }
        }
}
