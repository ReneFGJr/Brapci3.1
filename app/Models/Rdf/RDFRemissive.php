<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RDFRemissive extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfremissives';
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

    function edit($id)
        {
            $RDF = new \App\Models\Rdf\RDF();
            $dt = $RDF->le($id);

            $Class = $dt['concept']['c_class'];
            //$IdClass = $RDF->getClass($dt['concept']['c_class']);
            $d = [];
            for($r=2;$r <=5;$r++)
                {
                    $d[$r] = get('d'.$r);
                }

            $sx = $this->remissive($id,$d[2],$d[3],$d[4],$Class);
            return $sx;
        }

    function remissive($d2, $d3, $d4, $d5, $class = "Person")
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($d2);
        $nome = $dt['concept']['n_name'];
        $sx = '';

        $nome = troca($nome, ',', '');
        $wd = explode(' ', $nome);
        $sa = '';
        for ($r = 0; $r < count($wd); $r++) {
            $sa .= '<a href="' . PATH . '/popup/remissive/'. $d2. '?arg=' . $wd[$r] . '">' . $wd[$r] . '</a> ';
        }
        $sx .= bsc($sa, 12);

        /******************************* SAVE */
        $act = get("action");
        if ($act != '') {
            $d1x = get("id_cc");
            $d2x = get("id_use");

            if (is_array($d2x)) {
                for ($q = 0; $q < count($d2x); $q++) {
                    $d2xa = $d2x[$q];
                    $dt['cc_use'] = $d1x;
                    $RDF->set($dt)->where('id_cc', $d2xa)->update();
                    $RDF->change($d1x, $d2xa);
                }
                $sx = metarefresh(PATH . '/popup/remissive/'.$d2 . '?arg=' . get('arg'));
                return $sx;
            } else {
                if ($d2x != '') {
                    $dt['cc_use'] = $d1x;
                    $RDF->set($dt)->where('id_cc', $d2x)->update();
                    $RDF->change($d1x, $d2x);
                    $sx = metarefresh(PATH .'/popup/remissive/' . $d2. '?arg=' . get('arg'));
                    return $sx;
                }
            }
        }

        /********************************************** Classe */
        $id_class = $RDF->getClass($class);

        $nm = get("arg");
        if (strlen($nm) == 0) {
            $nm = $wd[0];
        }

        $RDF->join('rdf_name', 'cc_pref_term = id_n');
        $RDF->where('cc_class', $id_class);
        $RDF->where('cc_use', 0);
        $RDF->where('id_cc <> ' . $d2);
        $RDF->like('n_name', $nm);
        $RDF->orderBy('n_name');
        $dt = $RDF->FindAll();

        $sx .= form_open();
        $sx .= '<input type="text" name="arg" value="' . $nm . '" size="20" class="form-control">';
        $sx .= '<input type="hidden" name="id_cc" value="' . $d2 . '">';
        $sx .= count($dt) . ' names found';
        $sx .= '<select name="id_use[]" style="width: 100%;" size=12 multiple>';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sx .= '<option value="' . $line['id_cc'] . '">';
            $sx .= $line['n_name'];
            $sx .= '</option>';
        }
        $sx .= '</select>';
        $sx .= '<input type="submit" name="action" value="' . lang('Join') . '" class="btn btn-outline-primary">';
        $sx .= form_close();

        return $sx;
    }
}
