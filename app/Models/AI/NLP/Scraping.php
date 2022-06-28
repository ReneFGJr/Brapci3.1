<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Scraping extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'Scraping';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id','text','words','analyse'
	];
	protected $typeFields        = [
		'hidden','text','string:100','select:http:email:word'
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

	function index($d1,$d2,$d3,$d4)
		{
			switch($d2)
				{
					default:
						$sx = $this->form();
						break;
				}
			return $sx;
		}

	function form()
		{
			$sx = '';
			$type = get("analyse");
			$words = get("words");
			if (strlen($type) > 0)
				{
					$txt = get("text");
					$sx .= $this->analyse($type,$txt,$words);
				} else {
					$this->path = PATH.MODULE.'research/scraping';
					$sx = form($this);
				}
			return $sx;
		}


	function analyse($type,$txt,$words='')
		{
			switch($type)
				{
					case 0: /* HTTP */
						$links = $this->get_link($txt);
						$sx = show_array($links);
						break;
					case 1: /* EMAIL */
						$links = $this->get_email($txt);
						$sx = show_array($links);
						break;		
					case 2: /* WORD */
						$links = $this->get_word($txt,$words);
						$sx = show_array($links);
						break;										
					default:
						$sx = bsmessage('Type not found - '.$type);
				}
			return($sx);
		}

	function get_link($txt)
	{
		$url = array();
		$loop = 0;
		$sx = '';
		$tps = array('https','http');
		for ($l = 0;$l < count($tps);$l++)
			{
				$find = $tps[$l].':';				
				$txt1 = $txt;
				while ($pos = strpos($txt1,$find))
				{
					$http = substr($txt1,$pos,strpos($txt1,' ',$pos)-$pos);
					$http = trim(troca($http,'"',''));
					array_push($url,$http);
					$txt1 = substr($txt1,$pos+strlen($http));
					if ($loop++ > 1000) { break; }
				}
			}
		return $url;
	}

	function get_email($txt)
	{
			$list = preg_match_all( 
				'/([\w\d\.\-\_]+)@([\w\d\.\_\-]+)/mi', 
				$txt, 
				$matches 
			);
			$email = $matches[0];
		return $email;
	}

	function get_word($txt,$word)
	{
		$txt = ascii(mb_strtolower($txt));
		$word = ascii(mb_strtolower($word));
		preg_match_all('/('.$word.'.*[,;.])\s(.*)/', $txt, $matches);
		//echo '===>'.$txt;
		$words = $matches[0];
		return $words;
	}		
}