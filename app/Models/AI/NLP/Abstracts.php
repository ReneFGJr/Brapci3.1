<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Abstracts extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'abstracts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function check_next()
    {
        $BUGS = new \App\Models\Functions\Bugs();
        $task = 'CHECK_ABSTRACT';
        $limit = 100;
        $BOTS = new \App\Models\Bots\Index();
        $dt = $BOTS->task($task);

        $sx = '';
        $RDFLiteral = new \App\Models\Rdf\RDFLiteral();

        $RDF = new \App\Models\Rdf\RDF();
        $prop = $RDF->getClass('hasTitle');

        $RDFData = new \App\Models\Rdf\RDFData();
        $dd = $RDFData
            ->select("count(*) as total")
            ->join('rdf_name', 'id_n = d_literal')
            ->where('d_p', $prop)
            ->findAll();
        $total = $dd[0]['total'];

        $dd = $RDFData
            ->join('rdf_name', 'id_n = d_literal')
            ->where('d_p', $prop)
            ->orderBy('id_d')
            ->get($limit, $dt['task_offset'])
            ->getResult();

        foreach ($dd as $row) {
            $t = explode(' ', $row->n_name);
            $id = 0;
            if (count($t) < 2) {
                $idc = $row->d_r1;
                echo $row->n_name.'<br>';
                //$BUGS->register($idc, 'titleSHORT');
            } else {
                while (strlen($t[$id]) < 2) {
                    $id++;
                }
                /******************** CAIXA ALTA/BAIXA */
                $upper = uppercase($t[$id]);
                $so = sonumero($row->n_name);
                if (($upper == $t[$id]) and ($so == '')) {
                    $title_o = $row->n_name;
                    $title = ucfirst(mb_strtolower($row->n_name));
                    $sx .= '<hr>';
                    $sx .=  '<br>==>' . $title_o;
                    $sx .=  '<br>==>' . $title;
                    $da = array();
                    $da['n_name'] = $title;
                    $RDFLiteral->set($da)->where('id_n', $row->id_n)->update();
                }
            }
        }
        $dt['task_offset'] = $dt['task_offset'] + $limit;
        if (agent() == 1) {
            $perc = number_format($dt['task_offset'] / $total * 100, 1) . '%';
            $sx = '<br>Offset ' . $dt['task_offset'] . '/' . $total . ' ' . $perc . $sx;
            $sx .= metarefresh('', 1);
        } else {
            echo "OPS";
        }

        $BOTS
            ->set($dt)
            ->where('task_id', $task)
            ->update();
        return $sx;
    }
}
