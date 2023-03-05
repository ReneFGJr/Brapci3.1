<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Strings extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'strings';
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

    function check_next($class)
    {
        $BUGS = new \App\Models\Functions\Bugs();
        $task = 'CHECK_'.$class;
        $limit = 500;
        $BOTS = new \App\Models\Bots\Index();
        $dt = $BOTS->task($task);

        if ($dt['task_status'] != 1) {
            $BOTS->task_remove($task);
            return "FIM";
        }

        $sx = '';
        $RDFLiteral = new \App\Models\Rdf\RDFLiteral();
        $RDFConcept = new \App\Models\Rdf\RDFConcept();

        $RDF = new \App\Models\Rdf\RDF();
        $prop = $RDF->getClass($class);

        $dd = $RDFConcept
            ->select("count(*) as total")
            ->where('cc_class', $prop)
            ->first();
        $total = $dd['total'];

        $dd = $RDFConcept
            ->join('rdf_name', 'id_n = cc_pref_term')
            ->where('cc_class', $prop)
            ->orderBy('id_n')
            ->get($limit, $dt['task_offset'])
            ->getResult();

        $upper = false;

        echo 'Process '.count($dd).cr();

        foreach ($dd as $id=>$row) {
            $app = '';
            $upper = false;
            $txt = (string)$row->n_name;
            $txtO = (string)$row->n_name;

            $txt = strip_tags($txt);

            /* ENTER */
            $chars = [chr(13),chr(10),'(',')','"',"'"];
            $txt = troca($txt,' -','-');
            foreach($chars as $ch)
                {
                    if (strpos(' ' . $txt, $ch)) {
                        $txt = troca($txt, $ch, ' ');
                    }
                }

            if ($txt != $txtO)
                {
                    $txt = nbr_author($txt, 7);
                    $da['n_name'] = trim($txt);
                    $sa = '<br>=='.$txt;
                    $sa .= '<br>==' . $txtO;
                    echo $sa;
                    $RDFLiteral->set($da)->where('id_n', $row->id_n)->update();
                }
        }

        $dt['task_offset'] = $dt['task_offset'] + $limit;
        if ($dt['task_offset'] > $total) {
            $dt['task_offset'] = 0;
            $dt['task_status'] = 0;
        }

        $BOTS
            ->set($dt)
            ->where('task_id', $task)
            ->update();

        if (agent() == 1) {
            $perc = number_format($dt['task_offset'] / $total * 100, 1) . '%';
            $sx = '<br>Offset ' . $dt['task_offset'] . '/' . $total . ' ' . $perc . $sx;
            $sx .= metarefresh('', 1);
        } else {
            echo "OPS";
        }

        return $sx;
    }
}
