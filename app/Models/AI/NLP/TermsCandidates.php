<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class TermsCandidates extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = '*';
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

	function painel($d1='',$d2='',$d3='')
		{
			$sx = '';
			$MyFiles = new \App\Models\Brapci\MyFiles();
			$Socials = new \App\Models\Socials();
			$user = $Socials->getID();
			if (round($user) == 0)
				{ echo metarefresh(PATH.MODULE); exit; }

			/*********************************************************** */

			switch($d1)
				{
					case 'prepare':					
						$TextPrepare = new \App\Models\AI\NLP\TextPrepare();
						$dt = $MyFiles->where('id_file',$d2)->where('file_own',$user)->findAll();
						$filename = $dt[0]['file_full'].'.txt';
						$sa = '<iframe src="'.base_url(PATH.MODULE.'file/'.$d2.'/txt').'" width="100%" height="600"></iframe>';
						$sb = '';

						$txt = $TextPrepare->JoinSentences($filename);
						if ((get("action") == 'save') and (strlen($txt) > 100))
							{
								file_put_contents($filename,$txt);
								file_put_contents($filename.'.2',$txt);
								$sb .= bsmessage("Arquivo salvo com sucesso!",1);
							}

						if ($txt == '')
							{
								$sb .= bsmessage("Arquivo não encontrado! ".$filename,1);
							}
						
						$sb .= '<a href="?action=save" class="btn btn-primary">Salvar</a>';
						$sb .= ' | ';
						$sb .= '<a href="'.PATH.MODULE.'ai/nlp/findTermsCandidates/file/'.$d2.'" class="btn btn-primary">Return</a>';
						$sb .= '<hr>';
						$sb .= troca($txt,chr(13),'<hr>');						

						$sa = bsc($sa,6);
						$sb = bsc($sb,6);					
						$sx = bs($sa.$sb);
						break;

					case 'file':
						$dt = $MyFiles->where('id_file',$d2)->where('file_own',$user)->findAll();
						$ext = '';
						$filename = $dt[0]['file_full'];
						if (file_exists($filename.'.txt'))
							{
								$ext = 'txt';
							}

						$sa = '<iframe src="'.base_url(PATH.MODULE.'file/'.$d2.'/'.$ext).'" width="100%" height="600"></iframe>';
						if ($d3 != 0)
							{
								$sb = '<iframe src="'.base_url(PATH.MODULE.'file/'.$d2).'" width="100%" height="600"></iframe>';
							} else {

								/* Ext */
								switch($ext)
									{
										case 'txt':
											$sb = 'TXT';
											$mn[PATH.MODULE.'ai/nlp/findTermsCandidates/prepare/'.$d2] = 'Preparar TXT: Unir frases/parágrafos';
											$mn[PATH.MODULE.'ai/nlp/findTermsCandidates/utf8/'.$d2] = 'Preparar TXT: Converter para UTF-8';
											$sb .= menu($mn);
										break;

										default:
											$sb = '<iframe src="'.base_url(PATH.MODULE.'file/'.$d2.'/actions').'" width="100%" height="600">xxx</iframe>';
										break;
									}
						}						
						$sa = bsc($sa,6);
						$sb = bsc($sb,6);
						$sx = bs($sa.$sb);				
						break;
					default:
						$sx .= h(lang('ai.my_files_area'));
						$sa = $MyFiles->list($user,'ai/nlp/findTermsCandidates');
						$sb brapci.content_candidatesTerms= $MyFiles->tools();
						$sx .= bs(bsc($sa,6).bsc($sb,6));
						break;
				}			
			return $sx;
		}


	
}
