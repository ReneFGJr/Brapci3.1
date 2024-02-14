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
    protected $allowedFields    = [
        'id_f'
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

    function search()
    {
        $RDFliteral = new \App\Models\RDF2\RDFliteral();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();

        $q = get("q");
        if (strlen($q) < 3) {
            return [];
        }
        $prop = get("prop");
        /* Filter de Range of Classes */
        $concept = get("id");

        /** */
        $prop = 31;

        /************ Recupera o RANGE de possibilidades */
        $classes = [];
        $dtc = [];
        /*
        $dtc = $RDFclassRange
            ->select('cr_range')
            ->where('cr_property', $prop)
            ->findAll();
        */
        $dtci = [];
        $qi = explode(' ', $q);
        foreach ($dtc as $idx => $idy) {
            array_push($dtci, round($idy['cr_range']));
        }

        $RDFliteral->select('id_cc as ID, n_name as Name, n_lang as Lang, c_class as Class');
        $RDFliteral->join('rdf_data', 'id_n = d_literal');
        $RDFliteral->join('rdf_concept', 'd_r1 = id_cc');
        $RDFliteral->join('rdf_class', 'cc_class = id_c');
        $RDFliteral->orWhereIn('cc_class', $dtci);
        foreach ($qi as $id => $ti) {
            $RDFliteral->Like('n_name', $ti);
        }
        $RDFliteral->orderBy('n_name');
        $dt = $RDFliteral->findAll(40);

        return $dt;
    }

    function searchSelect()
    {

        $sx = '';
        $RDF = new \App\Models\RDF2\RDF();
        $Class = new \App\Models\RDF2\RDFclass();
        $Property = new \App\Models\RDF2\RDFproperty();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdomain = new \App\Models\RDF2\RDFclassDomain();

        $q = get("q").get("term");
        $prop = get("prop");
        $ID = get("ID");

        $RSP = [];
        $RSP['data']['q'] = $q;
        $RSP['data']['prop'] = $prop;
        $RSP['data']['ID'] = $ID;

        if (($prop == '') or ($ID == '')) {
            $RSP = [];
            $RSP['status'] = '500';
            $RSP['message'] = 'Busca Inválida, o campo ID ou prop está vazio';
            $RSP['data']['q'] = $q;
            $RSP['data']['prop'] = $prop;
            $RSP['data']['ID'] = $ID;
            return $RSP;
        }

        /************************************ GET PROPRIETY */

        $IDprop = $Property->getProperty($prop);
        $dtc = $RDF->le($ID);
        if (!isset($dtc['concept']['c_class'])) {
            $RSP = [];
            $RSP['status'] = '500';
            $RSP['message'] = 'Conceito não existe';
            return $RSP;
        }
        $class = $dtc['concept']['id_c'];

        /*** Check Rule Domain*/
        $dtd = $RDFdomain
            ->join('brapci_rdf.rdf_class as C1', 'cd_range = id_c')
            ->where('cd_domain', $class)
            ->where('cd_property', $IDprop)
            ->findAll(1);

        if ($dtd == []) {
            $RSP = [];
            $RSP['status'] = '500';
            $RSP['message'] = 'Classe não é compatível com a propriedade';
            return $RSP;
        }

        $Range = [];
        $RG = [];
        foreach ($dtd as $idr => $liner) {
            $Range[$liner['cd_range']] = $liner['c_class'];
            array_push($RG, $liner['id_c']);
        }
        /**************** Range Select Class ********/
        $n = 0;

        $RSP['data']['range'] = $RG;

        $RDFconcept->select('id_cc as ID, n_name as name, n_lang as lang, cc_use as use');
        $RDFconcept->join('brapci_rdf.rdf_literal', 'cc_pref_term = id_n');

        $q = explode(' ', trim($q));

        foreach ($q as $idq => $qt) {
            $qt = trim($qt);

            if ($qt != '') {
                $RDFconcept->like('n_name', $qt);
            }
        }

        $RDFconcept->whereIn('cc_class', $RG);
        foreach ($Range as $idr => $name) {
            //$RDFconcept->orwhere('cc_class',$idr);
        }
        $RDFconcept->orderby('n_name');
        $RSP = $RDFconcept->findAll(100);
        return $RSP;

    }

    function show_data($dt, $prop)
    {
        $sx = '';
        foreach ($dt as $id => $line) {
            if ($line['Property'] == $prop) {
                if ($sx != '') {
                    $sx .= '<br>';
                }
                $idD = $line['idD'];
                /* Edit */
                if ($line['Class'] == 'Literal')
                    {
                        $idL = $line['idL'];
                        $sx .= '<span class="pointer text-red" onclick="newxy(\'' . PATH . '/popup/rdf/literal/' . $idL . '\',800,300);">' . bsicone('edit', 16) . '</span>';
                    }

                /* Delete */
                $sx .= '<span class="pointer text-red" onclick="newxy(\''.PATH.'/popup/rdf/delete/'.$idD.'\',800,300);">'.bsicone('trash', 16). '</span>';

                /* Label */
                $sx .= '&nbsp;';
                $sx .= $line['Caption'];
                $sx .= '<sup>' . $line['Lang'] . '</sup>';
            }
        }
        return $sx;
    }

    function editRDF($id)
    {
        $sx = '';
        $RDF = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();

        $dt = $RDF->le($id);
        if (!isset($dt['concept']['n_name']))
            {
                echo $RDF->e404();
                exit;
            }

        $sx .= bsc(h($dt['concept']['n_name'], 3));
        $sx .= bsc(h($dt['concept']['c_class'], 6) . '<hr>');

        $cp = '*';
        $cp = 'c_class, rf_group';

        $df = $this
            ->select($cp)
            ->join('brapci_rdf.rdf_class', 'id_c = cd_property')
            ->join('brapci_rdf.rdf_form', 'rf_class = cd_property', 'left')
            ->where('cd_domain', $dt['concept']['id_c'])
            ->groupby($cp)
            ->orderBy('rf_order, rf_group')
            ->findAll();

        $xgrp = '';
        $data = $dt['data'];
        //$PATH = 'http://localhost:4200/#/';
        $PATH = 'https://brapci.inf.br/#/';
        foreach ($df as $idf => $linef) {
            $grp = $linef['rf_group'];

            $linkEd = '<span onclick="newxy2(\'' . $PATH . 'popup/rdf/add/' . $id . '/' . $linef['c_class'] . '\',1024,600);" class="cursor ms-1">';
            $linkEd .= bsicone('plus');
            $linkEd .= '</span>' . cr();

            if ($grp != $xgrp) {
                $xgrp = $grp;
                $sx .= '<tr>';
                $sx .= '<th><h4>' . lang('brapci.' . $grp) . '</h4></th>';
                $sx .= '</tr>';
            }
            $sx .= bsc(lang('rdf.' . $linef['c_class']) . $linkEd, 2, 'text-end');
            $sx .= bsc($this->show_data($data, $linef['c_class'], True, $id), 10, 'border-top border-secondary mb-3');
        }
        return bs($sx);
    }
}
