<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFdata extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_data';
    protected $primaryKey       = 'id_d';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'd_r1', 'd_r2', 'd_p', 'd_literal', 'd_ativo'
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

    function register($ID, $id_prop, $ID2, $lit)
    {
        $d = [];
        $d['d_r1'] = $ID;
        $d['d_r2'] = $ID2;
        $d['d_p'] = $id_prop;
        $d['d_literal'] = $lit;

        if ((($ID2 == 0) and ($lit == 0)) or ($id_prop == 0)) {
            echo "<br>OPS2 - registro inválido - PROP ($id_prop) - ID2 ($ID2)  - LIT ($lit)<br>";
            exit;
        }

        $dt = $this
            ->where('d_r1', $ID)
            ->where('d_r2', $ID2)
            ->where('d_p', $id_prop)
            ->where('d_literal', $lit)
            ->first();
        if ($dt == null) {
            $this->set($d)->insert();
        } else {
            /* Update */
        }
    }

    function le($id)
    {
        $cp = 'n_name as caption, id_cc as ID, cc_use as USE ';
        $cp .= ', prefix_ref as prefix, c_class as Class, "" as Prop ';

        $cp = '';
        $cp .= 'prefix_ref as Prefix,';
        $cp .= ', C1.c_class as Class';
        $cp .= ', C2.c_class as Property';
        $cp .= ', RC1.id_cc as ID';
        $cp .= ', n_name as Caption';
        $cp .= ', n_lang as Lang';
        $cp .= ', "" as URL';

        //$cp = '*';

        $dtA = $this
            ->select($cp.',"N" as tp')
            ->join('rdf_concept as RC1', 'RC1.id_cc = d_r2')
            ->join('rdf_class as C1', 'RC1.cc_class = C1.id_c')
            ->join('rdf_prefix', 'c_prefix = id_prefix')

            ->join('rdf_class as C2', 'd_p = C2.id_c')
            ->join('rdf_literal', 'RC1.cc_pref_term = id_n')

            ->where('d_r1', $id)
            ->where('d_r2 <> 0')
            ->findAll();

        $cp = 'prefix_ref as Prefix';
        $cp .= ', "Literal" as Class';
        $cp .= ', c_class as Property';
        $cp .= ', 0 as ID';
        $cp .= ', n_name as Caption';
        $cp .= ', n_lang as Lang';
        $cp .= ', "" as URL';

        $dtB = $this
            ->select($cp . ',"R" as tp')
            ->join('rdf_literal', 'd_literal = id_n')
            ->join('rdf_class', 'd_p = id_c')
            ->join('rdf_prefix', 'c_prefix = id_prefix')
            ->where('d_r2', $id)
            ->findAll();

        $cp = 'prefix_ref as Prefix';
        $cp .= ', "Literal" as Class';
        $cp .= ', c_class as Property';
        $cp .= ', 0 as ID';
        $cp .= ', n_name as Caption';
        $cp .= ', n_lang as Lang';
        $cp .= ', "" as URL';

        $dtC = $this
            ->select($cp . ',"R" as tp')
            ->join('rdf_literal', 'd_literal = id_n')
            ->join('rdf_class', 'd_p = id_c')
            ->join('rdf_prefix', 'c_prefix = id_prefix')
            ->where('d_r1', $id)
            ->where('d_r2', 0)
            ->findAll();

        $dt = array_merge($dtA, $dtB, $dtC);
        $dt = $this->auxiliar($dt);
        return $dt;
    }

    function auxiliar($dt)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFimage = new \App\Models\RDF2\RDFimage();

        foreach ($dt as $id => $line) {
            //pre($line,false);
            if
           (($line['Class'] == 'Image') and ($line['Property'] == 'hasCover')) {

                $ID = $line['ID'];
                $dt[$id]['Caption'] = $RDFimage->cover($ID);
                $dt[$id]['URL'] = $dt[$id]['Caption'];
            }
        }
        return $dt;
    }

    function view_data($dt)
        {
            $sx = '';
            $data = $dt['data'];
            if (count($data) == 0)
                {
                    $sx .= bsc(bsmessage('No records to show',3),12,'mt-3');
                }
            foreach($data as $id=>$line)
                {
                    $link = '';
                    $linka = '';
                    if ($line['ID'] > 0)
                        {
                            $link = '<a href="'.PATH.'/v/'.$line['ID'].'">';
                            $linka = '</a>';
                        }
                    $sx .= bsc($line['Class'],3,'text-end');
                    $sx .= bsc($link.$line['Caption']. $linka, 9, 'border-top border-secondary');
                    $sx .= bsc($line['Lang'], 1,'border-top border-secondary small');
                }
            return bs($sx);
        }

    function dataview($id)
    {
        $dt = $this->le($id);
        return ($dt);
    }
}
