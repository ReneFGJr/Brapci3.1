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
            $RDFclassRange = new \App\Models\RDF2\RDFclassRange();

            $q = get("q");
            if (strlen($q) < 3)
                {
                    return [];
                }
            $prop = get("prop");
            /* Filter de Range of Classes */
            $concept = get("id");

            /** */
            $prop = 31;

            /************ Recupera o RANGE de possibilidades */
            $classes = [];
            $dtc = $RDFclassRange
                ->select('cr_range')
                ->where('cr_property',$prop)
                ->findAll();
            $dtci = [];
            $qi = explode(' ',$q);
            foreach($dtc as $idx=>$idy)
                {
                    array_push($dtci,round($idy['cr_range']));
                }

            $RDFliteral->select('id_cc as ID, n_name as Name, n_lang as Lang, c_class as Class');
            $RDFliteral->join('rdf_data', 'id_n = d_literal');
            $RDFliteral->join('rdf_concept', 'd_r1 = id_cc');
            $RDFliteral->join('rdf_class', 'cc_class = id_c');
            $RDFliteral->orWhereIn('cc_class', $dtci);
            foreach($qi as $id=>$ti)
                {
            $RDFliteral->Like('n_name', $ti);
                }
            $RDFliteral->orderBy('n_name');
            $dt = $RDFliteral->findAll(40);

            return $dt;
        }
    function add3($d1, $d2='', $d3='', $range='')
    {
        $sx = '';
        $sx .= h($range, 2);

        /************************* SALVA REGISTRO */
        $action = get("action");

        $path = PATH . MODULE . '/rdf/form/edit/' . $d1 . '/' . $d2 . '/' . $d3;
        $dd['name'] = 'RDFFORM';
        $sx .= form_open($path, $dd);
        $sx .= '<span class="small">' . lang('rdf.filter_to') . ' ' . lang('rdf.' . $range) . '</span>';
        $sx .= '<input type="text" id="dd50" name="dd50" class="form-control">';

        /* Select */
        $sx .= '<span class="small mt-1">' . lang('find.select_an') . ' ' . lang('rdf.' . $range) . '</span>';
        $sx .= '<div id="dd51a"><select class="form-control" size="5" name="dd51" id="dd51"></select></div>';

        $bts = '';
        $bts .= '<input type="button" id="b1" class="btn btn-outline-secondary" disabled value="' . lang('rdf.force_create') . '" onclick="submitb1(\'' . $range . '\');"> ';
        $bts .= '<input type="button" id="b2" class="btn btn-outline-primary" disabled value="' . lang('rdf.save_continue') . '" onclick="submitb(1);"> ';
        $bts .= '<input type="button" id="b3" class="btn btn-outline-primary" disabled value="' . lang('rdf.save') . '" onclick="submitb(0);"> ';
        $bts .= '<button onclick="window.close();" id="b4" class="btn btn-outline-danger">' . lang('rdf.cancel') . '</buttontype=>';

        $sx .= bsc($bts, 12);
        $sx .= form_close();

        $js = '';
        $js .= '<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>';
        $js .= '<script>
		function submitb($c)
			{
				$vlr = $("#dd51").val();
				if ($vlr == null)
					{
						alert("OPS, selecione um registro");
					} else {
						$.ajax(
							{
								type: "GET",
								url: "' . PATH . MODULE . '/api/rdf/set/",
								data: "act=set&reload="+$c+"&reg=' . $d3 . '&prop=' . $d1 . '&vlr="+$vlr,
								dataType: "html",
							})
							.done(function(data)
								{
									$("#dd51a").html(data);
								}
							);
					}
			}

			function submitb2()
			{
				$vlr = $("#dd51").val();
				if ($vlr == null)
					{
						alert("OPS");
					} else {

					}
			}

			function submitb1($c)
			{
				$vlr = $("#dd50").val();
				if ($vlr == null)
					{
						alert("OPS");
					} else {
						$.ajax(
							{
								type: "GET",
								url: "' . PATH . MODULE . '/api/rdf/vc_create/",
								data: "act=set&reload="+$c+"&reg=' . $d3 . '&prop=' . $d1 . '&vlr="+$vlr,
								dataType: "html",
							})
							.done(function(data)
								{
									$("#dd51a").html(data);
								}
							);
					}
			}
		/************ keyup *****************/
		jQuery("#dd50").keyup(function()
		{
			var $key = jQuery("#dd50").val();
			$.ajax(
				{
					type: "POST",
					url: "' . PATH . MODULE . '/api/rdf/searchSelect/' . $range . '/?q="+$key,
					success: function(data){
						$("#dd51a").html(data);
					}
				}
			);
		});
		</script>';
        return $sx . $js;
        }

    function add($d1,$d2)
        {
            $sx = '';
            $RDF = new \App\Models\RDF2\RDF();
            $RDFproperty = new \App\Models\RDF2\RDFproperty();
            $RDFdomain = new \App\Models\RDF2\RDFclassDomain();

            $dt = $RDF->le($d1);
            $sx .= 'Class: <b>'.$dt['concept']['c_class']. '</b>';
            $sx .= '<hr>';
            $idc = $dt['concept']['id_c'] . '</b>';
            $idp = $RDFproperty->getProperty($d2);

            /*********************************** Class */
            $class = $dt['concept']['id_c'];


            /********************************** Ranges */
            $cp = "c_class";
            $dt = $this
                ->select($cp)
                ->join('rdf_class','cd_range = id_c')
                ->where('cd_domain',$idc)
                ->where('cd_property',$idp)
                ->findAll();
            $types = '';
            foreach($dt as $id=>$line)
                {
                    if ($types != '') { $types .= ', '; }
                    $types .= $line['c_class'];
                }

            $sx .= '<div class="container-fluid">';
            $sx .= '<div class="row">';
            $sx .= '<div class="col-12">';
            $sx .= form_open();
            $sx .= form_label("Termo de busca");
            $sx .= '<div class="input-group mb-3">
                    <input name="term" id="term" type="text" class="full form-control border border-secondary" placeholder="Termo de busca" aria-label="Termo de Busca" aria-describedby="button-addon2">
                    <button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="submitAction()">Busca</button>
                    </div>';
            //$sx .= form_label("Selecione o conceito - ".$d2.' - '.$types.'.');
            $sx .= '<div id="dd51a" name="dd51a"><select name="opt" class="full form-control border border-secondary" size=6></div>';
            $sx .= '</select>';
            $sx .= '<hr>';
            $sx .= '<button id="btn_add" class="btn btn-outline-secondary me-2" disabled>Inserir Conceito</button>';
            $sx .= '<button id="btn_sign" class="btn btn-outline-primary me-2" disabled>Salvar e fechar</button>';
            $sx .= '<button id="btn_signc" class="btn btn-outline-primary me-2" disabled>Salbar e continuar</button>';
            $sx .= '<button id="btn_cancel" onclick="wclose();" class="btn btn-outline-warning me-2">Cancelar</button>';
            $sx .= form_close();
            $sx .= '</div></div></div>';

            $sx .= '<script>'.cr();
            $sx .= 'function submitAction() {
                        term = $("#term").val()
                        url = "'.PATH.'/api/rdf/searchSelect/"
                        url += "?prop='.$d2. '"
                        url += "&class=' . $class . '"
                        url += "&q="+term

                        $.ajax(
                            {
                                type: "POST",
                                url: url,
                                success: function(data){
                                    $("#dd51a").html(data);
                                }
                            })
                    }';
            $sx .= '</script>' . cr();
            $sx .= '<div id="mypar" name="mypar class="full">XXXXX</div>';

            return $sx;
        }

    function searchSelect()
    {
        $sx = '';
        $Class = new \App\Models\RDF2\RDFclass();
        $Property = new \App\Models\RDF2\RDFproperty();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdomain = new \App\Models\RDF2\RDFclassDomain();

        $q = get("q");
        $prop = get("prop");
        $class = get("class");
        $IDprop = $Property->getProperty($prop);

        /*** Check Rule Domain*/
        $dtd = $RDFdomain
            ->join('brapci_rdf.rdf_class as C1','cd_range = id_c')
            ->where('cd_domain', $class)
            ->where('cd_property', $IDprop)
            ->findAll();
        $Range = [];
        $RG = [];
        foreach($dtd as $idr=>$liner)
            {
            $Range[$liner['cd_range']] = $liner['c_class'];
            array_push($RG, $liner['id_c']);
            }
        /**************** Range Select Class ********/
        $n = 0;

        $RDFconcept->select('id_cc, n_name, n_lang, cc_use');
        $RDFconcept->join('brapci_rdf.rdf_literal','cc_pref_term = id_n');

        $q = explode(' ',$q.' ');
        pre($q,false);

        $RDFconcept->likeIn('n_name', $q);
        $RDFconcept->whereIn('cc_class', $RG);
        foreach($Range as $idr=>$name)
            {
                //$RDFconcept->orwhere('cc_class',$idr);
            }
        $RDFconcept->orderby('n_name');
        $drr = $RDFconcept->findAll(100);


        $sx = '<select name="id" class="full form-control border border-secondary" size=6>';
        $sx .= '<option>::Select</option>';
        foreach($drr as $id=>$line)
            {
                $sx .= '<option value="'.$line['id_cc'].'">'.$line['n_name'].' ['.$line['n_lang'].'] ('.$line['id_cc'].'=>'.$line['cc_use'].')</option>'.cr();
            }
        $sx .= '</select>';
        $sx .= 'q='.$q;
        $sx .= '<br>prop=' . $prop;
        $sx .= '<br>class=' . $class;

        echo $sx;
        exit;

        return $sx;
    }

    function show_data($dt,$prop)
        {
            $sx = '';
            foreach($dt as $id=>$line)
                {
                    if ($line['Property'] == $prop)
                        {
                            if ($sx != '') { $sx .= '<br>'; }
                            $sx .= bsicone('edit',16);
                            $sx .= bsicone('trash', 16);
                            $sx .= '&nbsp;';
                            $sx .= $line['Caption'];
                            $sx .= '<sup>'.$line['Lang'].'</sup>';
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

        $cp = '*';

        $df = $this
            ->select($cp)
            ->join('brapci_rdf.rdf_class', 'id_c = cd_property')
            ->join('brapci_rdf.rdf_form', 'rf_class = cd_property', 'left')
            ->where('cd_domain', $dt['concept']['id_c'])
            ->orderBy('rf_order, rf_group')
            ->findAll();

        $xgrp = '';
        $data = $dt['data'];
        foreach($df as $idf=>$linef)
            {
                $grp = $linef['rf_group'];

                $linkEd = '<span onclick="newxy2(\'' . PATH . '/popup/rdf/add/' . $id . '/' . $linef['c_class'] . '\',1024,800);" class="cursor">';
                $linkEd .= bsicone('plus');
                $linkEd .= '</span>' . cr();

                if ($grp != $xgrp)
                    {
                        $xgrp = $grp;
                        $sx .= '<tr>';
                        $sx .= '<th><h4>'.lang('brapci.'.$grp).'</h4></th>';
                        $sx .= '</tr>';
                    }
                $sx .= bsc(lang('rdf.'.$linef['c_class']).$linkEd,2,'text-end');
                $sx .= bsc($this->show_data($data, $linef['c_class'], True, $id),10,'border-top border-secondary mb-3');
            }
        return bs($sx);
     }
}
