<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFform extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_class_domain';
    protected $primaryKey       = 'id_cd';
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

    function index($act, $id, $d3)
    {
        $sx = '';
        switch ($act) {
            case 'editRDF':
                $sx .= $this->editRDF($id, $d3);
                break;
        }
        return $sx;
    }

    function search($t='',$class='')
        {
            $RDFliteral = new \App\Models\RDF2\RDFliteral();

            $dt = $RDFliteral->like($t, 'match')
                ->orderBy('n_name')
                ->findAll(40);
            return $dt;
        }

    function add($d1,$d2)
        {
            $sx = '';
            $RDF = new \App\Models\RDF2\RDF();
            $RDFproperty = new \App\Models\RDF2\RDFproperty();
            $dt = $RDF->le($d1);
            $sx .= 'Class: <b>'.$dt['concept']['c_class']. '</b>';
            $sx .= '<hr>';
            $idc = $dt['concept']['id_c'] . '</b>';
            $idp = $RDFproperty->getProperty($d2);

            /********************************** Ranges */
            $cp = "c_class";
            $dt = $this
                ->select($cp)
                ->join('rdf_class_range', 'cr_property = cd_property')
                ->join('rdf_class','cr_range = id_c')
                ->where('cd_domain',$idc)
                ->where('cd_property',$idp)
                ->findAll();
            pre($dt,false);

            $sx = form_open();
            $sx .= form_input('term');
            $sx .= form_close();

            return $sx;
        }
    function editRDF($id)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();

        $dt = $RDF->le($id);

        $Class = $dt['concept']['c_class'];
        $idc = $RDFclass->getClass($Class);

        $cp = "lt1.n_name as lt1, lt2.n_name as lt2,";
        $cp .= "lt1.n_lang as lg1, lt2.n_lang as lg2,";
        $cp .= "c_class, d_r1, d_p, d_r2, d_literal,id_d ";

        $dt = $this
            ->select($cp)
            ->join('brapci_rdf.rdf_data', 'd_p = cd_property', "left")
            ->join('brapci_rdf.rdf_class', 'd_p = id_c', "left")
            ->join('brapci_rdf.rdf_literal as lt1', 'd_literal = lt1.id_n', "left")
            ->join('brapci_rdf.rdf_concept', 'd_r2 = id_cc', 'left')
            ->join('brapci_rdf.rdf_literal as lt2', 'cc_pref_term = lt2.id_n', "left")
            ->where('cd_domain', $idc)
            ->where('d_r1', $id)
            ->findAll();

        $sx = '<table class="table full">' . cr();
        foreach ($dt as $idx => $line) {
            $sx .= '<tr>';
            $sx .= '<td valign="top" style="text-align: right;">';
            $sx .= $line['c_class'];
            $sx .= '</td>';

            $sx .= '<td valign="top">';
            $link = '';
            $linka = '';

            $idd = $line['id_d'];
            $linkEd = '';
            if ($idd > 0) {
                $linkEd = '<span onclick="newxy2(\'' . PATH . '/popup/rdf/delete/' . $idd . '\',800,600);" class="cursor">';
                $linkEd .= bsicone('trash');
                $linkEd .= '</span>' . cr();

                if ($line['d_literal'] > 0) {
                    $linkEd .= '<span onclick="newxy2(\'' . PATH . '/popup/rdf/edit/' . $idd . '\',800,600);" class="cursor">';
                    $linkEd .= bsicone('edit');
                    $linkEd .= '</span>' . cr();
                }

                $linkEd .= '<span onclick="newxy2(\'' . PATH . '/popup/rdf/add/'.$id.'/' . $line['c_class'] . '\',800,600);" class="cursor">';
                $linkEd .= bsicone('plus');
                $linkEd .= '</span>' . cr();
            }

            $dr2 = trim($line['d_r2']);
            if ($dr2 > 0) {
                $link = '<a href="' . PATH . '/v/' . $dr2 . '">';
                $linka = '</a>';
            }

            $name = trim($line['lt1']) . trim($line['lt2']);
            if ($name != '') {
                $sx .= $linkEd;
                $sx .= $link . $name . $linka . '@' . $line['lg1'] . $line['lg2'] . cr();
            }

            $sx .= '</td>' . cr();

            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        return $sx;
    }
}
