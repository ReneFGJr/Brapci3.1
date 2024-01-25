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
            $dt = $RDF->le($d1);
            $sx .= 'Class: <b>'.$dt['concept']['c_class']. '</b>';
            $sx .= '<hr>';
            $idc = $dt['concept']['id_c'] . '</b>';
            $idp = $RDFproperty->getProperty($d2);

            /*********************************** Class */
            $class = 1;

            /********************************** Ranges */
            $cp = "c_class";
            $dt = $this
                ->select($cp)
                ->join('rdf_class_range', 'cr_property = cd_property')
                ->join('rdf_class','cr_range = id_c')
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
            $sx .= '<div id="dd51a"><select name="opt" class="full form-control border border-secondary" size=6></div>';
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
                        url += "&q=term"

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
        $q = get("q");
        $prop = get("prop");
        $class = get("class");
        $sx = '<select name="id" class="full form-control border border-secondary" size=6>';
        $sx .= '<option>::Select</option>';
        $sx .= '</select>';
        $sx .= 'q='.$q;
        $sx .= '<br>prop=' . $prop;
        $sx .= '<br>class=' . $class;
        echo $sx;
        exit;

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
        $cp .= "c_class, d_r1, d_p, d_r2, d_literal,id_d,id_c,rf_order,rf_label ";

        $dt = $this
            ->select($cp)
            ->join('brapci_rdf.rdf_class', 'cd_property = id_c', "left")
            ->join('brapci_rdf.rdf_form', 'id_c = rf_class', "left")
            ->join('brapci_rdf.rdf_data', 'cd_property = d_p', "left")
            ->join('brapci_rdf.rdf_literal as lt1', 'd_literal = lt1.id_n', "left")
            ->join('brapci_rdf.rdf_concept', 'd_r2 = id_cc', 'left')
            ->join('brapci_rdf.rdf_literal as lt2', 'cc_pref_term = lt2.id_n', "left")
            ->where('cd_domain', $idc)
            ->where('(d_r1 = '.$id.' or d_r1 is null)')
            ->orderBy('rf_order, c_class')
            ->findAll();

        //pre($dt,false);
        $sx = '';
        $sx .= h("Class:".$Class);

        $sx .= '<table class="table full">' . cr();
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

                $linkEd .= '<span onclick="newxy2(\'' . PATH . '/popup/rdf/add/'.$id.'/' . $line['c_class'] . '\',1024,800);" class="cursor">';
                $linkEd .= bsicone('plus');
                $linkEd .= '</span>' . cr();
            }
            pre($line,false);
            $dr2 = ground($line['d_r2']);
            if ($dr2 > 0) {
                $link = '<a href="' . PATH . '/v/' . $dr2 . '">';
                $linka = '</a>';
            }

            $name = '';
            if ($line['lt1'] != null) { $name .= trim($line['lt1']); }
            if ($line['lt2'] != null) { $name .= trim($line['lt2']); }
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
