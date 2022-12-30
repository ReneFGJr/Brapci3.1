<?php

namespace App\Models\Qualis;

use CodeIgniter\Model;

class Qualis extends Model
{
    protected $DBGroup          = 'capes';
    protected $table            = 'qualis';
    protected $primaryKey       = 'id_q';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_q','q_issn','q_area','q_estrato', 'q_event'
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

    function register($dt)
        {
            $dta = $this
                ->where('q_issn',$dt['q_issn'])
                ->where('q_event', $dt['q_event'])
                ->where('q_area', $dt['q_area'])
                ->findAll();
            if (count($dta) == 0)
                {
                    $id = $this->set($dt)->insert();
                } else {
                    $id = $dta[0]['id_q'];
                }
            return $id;
        }

    function list($event, $area)
    {
        $sx = '';
        $dt = $this
            ->join('journals', 'q_issn = j_issn')
            ->where('q_event', $event)
            ->where('q_area', $area)
            ->orderBy('j_name')
            ->findAll();

        $sx .= '<table class="" style="width: 100%;">';
        $stl = ' style="border-bottom: 1px solid #000;" ';
        $sx .= '<tr>
                    <th width="10%">ISSN</th>
                    <th width="87%">JOURNAL/Revista</th>
                    <th width="3%">Q</th>
                </tr>';
        for($r=0;$r < count($dt);$r++)
            {
                $line = $dt[$r];
                $sx .= '<tr>';
                $sx .= '<td '.$stl.'>'.$line['q_issn'].'</td>';
                $sx .= '<td ' . $stl . '>' . $line['j_name'] . '</td>';
                $sx .= '<td ' . $stl . ' class="text-end">' . $line['q_estrato'] . '</td>';
                $sx .= '</tr>';
            }
        $sx .= '</table>';
        return $sx;
    }

    function statistic($event,$area)
        {
            $dt = $this
                ->select('count(*) as total, q_estrato')
                ->where('q_event',$event)
                ->where('q_area',$area)
                ->groupby('q_estrato')
                ->orderby('q_estrato')
                ->findAll();
            $sca = '';
            $scb = '';
            $scc = '';

            $tot = 0;
            for ($r = 0; $r < count($dt); $r++) {
                $line = $dt[$r];
                $tot = $tot + $line['total'];
            }

            for($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];

                    $sca .= '<td class="text-center">';
                    $sca .= h($line['q_estrato'],3);
                    $sca .= '</td>';

                    $scb .= '<td class="text-center">';
                    $scb .= h($line['total'],4);
                    $scb .= '</td>';

                    $scc .= '<td class="text-center">';
                    $scc .= h(number_format($line['total']/$tot*100,1,',','.').'%', 5);
                    $scc .= '</td>';
                }

                $sca .= '<td class="text-center">';
                $sca .= h('Total', 4);
                $sca .= '</td>';

                $scb .= '<td class="text-center">';
                $scb .= h(number_format($tot,0,',','.'), 4);
                $scb .= '</td>';

                $scc .= '<td class="text-center">';
                $scc .= h('100%', 5);
                $scc .= '</td>';


            $sx = '<table class="table" width="100%">';
            $sx .= '<tr>'.$sca.'</tr>';
            $sx .= '<tr>' . $scb . '</tr>';
            $sx .= '<tr>' . $scc . '</tr>';
            $sx .= '</table>';
            return $sx;
        }
}
