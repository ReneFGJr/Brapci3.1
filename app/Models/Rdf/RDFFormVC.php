<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfFormVC extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'rdfformvcs';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	function search($d1,$d2,$d3)
		{
			$limit = 10; /* Limite de retorno */
			$RDFConcept = new \App\Models\Rdf\RDFConcept();
			$sx = '<select class="form-control" size="5" name="dd51" id="dd51">';
			/********************************/
			$q = get("q");
			$sx .= $q;
			$sx .= $d2;
			if (strlen($q) >=1)
				{
					//$sx .= '<option value="">Buscando ... '.$q.'</option>'.cr();
					$dt = $RDFConcept->like($q,$d1,$limit);
					$dt1 = $RDFConcept
					    ->join("rdf_name","cc_pref_term = id_n")
						->where('id_cc',$q)->findAll();
					$dt = array_merge($dt,$dt1);

					for($r=0;$r < count($dt);$r++)
						{
							$ln = (array)$dt[$r];
							$idx = $ln['id_cc'];
							$pref = '';
							if ($ln['cc_use'] > 0)
								{
									$idx = $ln['cc_use'];
									$pref = ' <i>(UP)</i>';
								}
							$name = trim($ln['n_name']) . $pref;
							if (strpos($name,'#')) { $name = substr($name,0,strpos($name,'#')); }
							$sx .= '<option value="'.$idx.'">'.$name.'</option>'.cr();
						}
				}
			$sx .= '</select>';
			$sx .= '<script>';

			if (strlen($q) >=3)
			{
			if (count($dt) > 0)
				{
					$sx .= '$("#b3").attr("disabled", false);';
					$sx .= '$("#b2").attr("disabled", false);';
					$sx .= '$("#b1").attr("disabled", true);';
				} else {
					$sx .= '$("#b3").attr("disabled", true);';
					$sx .= '$("#b2").attr("disabled", true);';
					$sx .= '$("#b1").attr("disabled", false);';
				}
			}
			$sx .= '</script>';

			return $sx;
		}

	function edit($form_class, $prop_name, $form_id, $register, $range)
		{
		$sx = '';
		$sx .= h(lang('rdf.catalog_input').' '.lang('rdf.'.$range),4);

		/************************* SALVA REGISTRO */
		$action = get("action");

		$path = PATH.COLLECTION.'/form/edit/'. $prop_name.'/'. $form_id.'/'. $register;
		$dd['name'] = 'RDFFORM';
		$sx .= form_open($path,$dd);
		$sx .= '<div id="busy" name="busy" style="position: fixed; top:0; right:0; text-align=right;"></div>';
		$sx .= '<span class="small">'.lang('rdf.filter_to').' '.lang('rdf.'.$range).'</span>';
		$sx .= '<input type="text" id="dd50" name="dd50" class="form-control-lg full">';

		/* Select */
		$sx .= '<span class="small mt-1">'.lang('rdf.select_an').' '.lang('rdf.'.$range).'</span>';
		$sx .= '<div id="dd51a"><select class="form-control-lg full" size="5" name="dd51" id="dd51"></select></div>';

		$bts = '<br>';
		$bts .= '<input type="button" id="b1" class="btn btn-outline-secondary" value="'.lang('rdf.force_create').'" onclick="submitb1(\''.$range.'\');"> ';
		$bts .= '<input type="button" id="b2" class="btn btn-outline-primary" disabled value="'.lang('rdf.save_continue').'" onclick="submitb(1);"> ';
		$bts .= '<input type="button" id="b3" class="btn btn-outline-primary" disabled value="'.lang('rdf.save').'" onclick="submitb(0);"> ';
		$bts .= '<button onclick="window.close();" id="b4" class="btn btn-outline-danger">'.lang('rdf.cancel').'</buttontype=>';

		$sx .= bsc($bts,12);
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
								url: "'.PATH.MODULE.'/rdf/ajax/set/",
								data: "act=set&reload="+$c+"&reg='. $register.'&prop='. $prop_name.'&vlr="+$vlr,
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
								url: "'.PATH.MODULE.'/rdf/ajax/vc_create/",
								data: "act=set&reload="+$c+"&reg='. $register.'&prop='. $prop_name. '&vlr="+$vlr,
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
		$busy = false;
		jQuery("#dd50").keyup(function()
		{
			$("#busy").html("<span style=\"color: red;\">'.lang('rdf.searching').'</span>");
			if ($busy == false)
			{
				$busy = true;
				var $key = jQuery("#dd50").val();
				$.ajax(
					{
						type: "POST",
						url: "'.PATH.MODULE.'/rdf/ajax/search/'.$range. '/?q="+$key,
						async: true,
						success: function(data){
							$("#dd51a").html(data);
							$busy = false;
							$("#busy").html("<span style=\"color: green;\"></span>");
						}
					}
				);
			}
		});
		</script>';
		return $sx.$js;
	}

	function ajax_save()
		{
			$act = get("act");
			switch ($act)
				{
					case 'set':
						$RDFData = new \App\Models\Rdf\RDFData();
						$RDFData->set_rdf_data(get("reg"),get("prop"),get("vlr"));

						if (get("reload") == 1)
							{
								$sx = '<script>location.reload();</script>';
							} else {
								$sx = wclose();
								//'<script>window.close();</script>';
							}
						break;
				}
				echo bsmessage("SAVED");
				echo $sx;
				return "";
		}
}
