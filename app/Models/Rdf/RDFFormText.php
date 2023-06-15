<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfFormText extends Model
{
	var $DBGroup              = 'rdf';
	var $table                = PREFIX . 'rdf_name';
	protected $primaryKey           = 'id_n';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_n','n_name','n_lang'
	];

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

	function edit($id,$prop='',$idf=0,$idc=0)
		{
			$sx = '';
			$RDFLiteral = new \App\Models\Rdf\RDFLiteral();

			/************************* SALVA REGISTRO */
			$action = get("action");
			if ($action != '')
				{
					$texto = get("descript");
					$data = array('n_lang'=>get("lang"),'n_name'=>$texto);
					if ($id > 0)
						{
							/******************************* ATUALIZA */
							$RDFLiteral->atualiza($data,$id);
						} else {
							/******************************* NOVA ENTRADA */
							$RDFData = new \App\Models\Rdf\RDFData();
							$RDFClass = new \App\Models\Rdf\RDFClass();
							$lang = get("n_lang");
							if ($lang == '') { $lang = 'pt-BR'; }
							$da = array();
							$da['d_literal'] = $RDFLiteral->name($texto,$lang);
							$da['d_r1'] = $idc;
							$da['d_p'] = $RDFClass->class($prop,false);
							$da['d_library'] = LIBRARY;
							$Socials = new \App\Models\Socials();
							$da['d_user'] = $Socials->getUser();
							$da['d_update'] = date("Y-m-d H:i:s");
							$RDFData = new \App\Models\Rdf\RDFData();
							$RDFData->insert($da);
						}
					return wclose();
				} else {

					/************************** Form */
					if ($id > 0)
					{
						$dt = $RDFLiteral->le($id);
						$texto = $dt['n_name'];
						$lang = $dt['n_lang'];
						$path = PATH.'/rdf/data/text/'.$id;
					} else {
						$texto = get("descript");
						$lang = 'pt-BR';
						$path = PATH.MODULE.'/rdf/data/edit/'.$prop.'/'.$idf.'/'.$idc;
					}
				}

			$sx .= $this->form_edit($path,$texto,$lang);
			return $sx;
		}

	function form_edit_id($id)
		{
			$sx = '';

			$dt = $this->find($id);
			if (get("action") != '')
				{
					$dt['n_name'] = get("descript");
					$dt['n_lang'] = get("n_lang");
					$this->set($dt)->where('id_n',$id)->update();
					$sx .= wclose();
					return $sx;
				}
			$dt = $this->find($id);

			$text = $dt['n_name'];
			$lang = $dt['n_lang'];
			$path = PATH.'/rdf/text/'.$id;
			$sx .= $this->form_edit($path, $text, $lang);
			return $sx;
		}
	function form_edit($path,$texto,$lg)
		{
			$lang = array('pt-BR','en','es','fr');
			$txt = '';
			$mchecked = true;
			$txt = form_open($path);
			$txt .= '<span class="supersmall">'.lang('rdf.textarea').'</span>';
			$txt .= '<textarea id="descript" name="descript" style="width: 100%; height: 250px;" class="form-control-lg">'.$texto.'</textarea>';
			for ($r=0;$r < count($lang);$r++)
				{
					$mchecked = false;
					if ($lg==$lang[$r]) { $mchecked = true; }
					$txt .= form_radio('n_lang', $lang[$r], @$mchecked, 'id=n_lang') .
							form_label(lang('brapci.'.$lang[$r]), $lang[$r]);
					$txt .= '<br>';
				}

			$txt .= form_submit('action', lang('rdf.save'), 'class="btn btn-primary supersmall m-3"');
			$txt .= form_close();
			return $txt;
		}
}
