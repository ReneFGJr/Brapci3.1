<?php

namespace App\Models\AI;

use CodeIgniter\Model;

class Index extends Model
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

	function index($act='',$subact='',$d1='',$d2='')
		{
			$sx = h(trim(trim('AI '.$act).' '.$subact));

			switch($act)
				{
					case 'wiki':
						$wiki = new \App\Models\AI\Wiki\Index();
						$sx .= $wiki->index($subact, $d1, $d2);
						break;
					case 'nlp':
						$NLP = new \App\Models\AI\NLP\Index();
						$sx .= $NLP->index($subact,$d1,$d2);
						break;
					case 'file':
						switch($subact)
							{
								case 'process':
									$API = new \App\Models\AI\FILE\pdf;
									$sx = $API->show_files();
									return $sx;

								case 'upload':
									$API = new \App\Models\AI\FILE\upload;
									$sx = $API->upload($d1, $d2);
									return $sx;

								case 'pdf_to_text':
									$API = new \App\Models\AI\FILE\pdf;
									$sx = $API->pdf_to_html($d1,$d2);
									break;

								default:
								break;
							}
					break;

					default:
					$sx = $this->menu();
					break;
				}
			$sx = bs(bsc($sx,12));
			return $sx;
		}

	function menu()
		{

		$menu['#AI - TERMS'] = 'AI (Terms)';
		$menu[PATH . COLLECTION . '/wiki/'] = lang('ai.wiki');

		$menu['#AI'] = 'AI';
		$menu[PATH . COLLECTION . '/file/upload'] = lang('ai.files_upload');
		$menu[PATH . COLLECTION . '/file/pdf_to_text'] = lang('ai.files_pdf_to_text');
		$menu[PATH . COLLECTION . '/nlp/book_sumary'] = lang('ai.book_sumary_identify');

		$menu['#AI'] = 'AI & NLP';
		$menu[PATH . COLLECTION . '/nlp/language'] = lang('ai.language');

		$menu['#CHARBOT'] = lang('ai.chat_bot');
		$menu[PATH . COLLECTION . '/chat/analyse'] = lang('ai.chat_analyse');
		$menu[PATH . COLLECTION . '/skos'] = lang('ai.skos');

		$sx = '';

		$sx .= MENU($menu);
		$img = '<img src="'.URL. '/img/chat/chat_boot.png" style="height: 50px;">';

		$sx .= onclick(PATH . COLLECTION . '/chat', 800, 700,'btn btn-outline-primary') . $img. 'ChatBot</span>';

		return $sx;
		}

}
