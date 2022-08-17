<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Language extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'languages';
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

function getTextLanguage($text) {
	  $text = ascii(mb_strtolower($text));
      $supported_languages = array(
          'en',
          'de',
		  'pt',
		  'es',
      );
      // German Word list
      // from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
      $wordList['de'] = array ('der', 'die', 'und', 'in', 'den', 'von',
          'zu', 'das', 'mit', 'sich', 'des', 'auf', 'für', 'ist', 'im',
          'dem', 'nicht', 'ein', 'Die', 'eine');
      // English Word list
      // from http://en.wikipedia.org/wiki/Most_common_words_in_English
      $wordList['en'] = array ('the', 'be', 'to', 'of', 'and', 'a', 'in',
          'that', 'have', 'I', 'it', 'for', 'not', 'on', 'with', 'he',
          'as', 'you', 'do', 'at');
      // from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
      $wordList['pt'] = array ('de', 'da', 'em', 'ção', 'cao', 'vel',
          'ico', 'das', 'mit', 'sich', 'des', 'auf', 'für', 'ist', 'im',
          'dem', 'nicht', 'ein', 'Die', 'eine');
      // from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
      $wordList['es'] = array ('der', 'die', 'und', 'in', 'den', 'von',
          'zu', 'das', 'mit', 'sich', 'des', 'auf', 'für', 'ist', 'im',
          'dem', 'nicht', 'ein', 'die', 'eine');


	   /************************************************************************/
	   $end['de'] = array();
	   $end['en'] = array('at','ed','gy','on','nce','ons','ion','fic','ment','rch','ate');
	   $end['pt'] = array('ao','em','ia','cos','dos','iro','je','por','ena','nto','lho','iva','ual');
	   $end['es'] = array();

      // clean out the input string - note we don't have any non-ASCII
      // characters in the Word lists... change this if it is not the
      // case in your language wordlists!
      $text = preg_replace("/[^A-Za-z]/", ' ', $text);
	  $text = ' ' .$text. ' ';
      // count the occurrences of the most frequent words

	  /****************************** Zera contador */
      foreach ($supported_languages as $language)
	  {
        $counter[$language]=0;
      }

	  // split the text into words
      foreach ($supported_languages as $language)
	  	{
		  $terms = $wordList[$language];
		  for ($r=0;$r < count($terms);$r++)
		  {
			  $total = substr_count($text, ' ' .$terms[$r] . ' ');
			  $counter[$language] = $counter[$language]+ $total;
		  }

		  $terms = $end[$language];
		  for ($r=0;$r < count($terms);$r++)
		  {
			  $total = substr_count($text, $terms[$r] . ' ');
			  $counter[$language] = $counter[$language] + $total;
		  }
		}
		$lang = 'NaN';
		$max = 1;
		foreach($counter as $key => $value)
		{
			if ($value >= $max) { $lang = $key; $max = $value; }
		}
		if ($lang == 'pt') { $lang = 'pt-BR'; }
		if ($lang == 'NaN')
			{
				//echo '<h1>Language: '.$text.'<br>==>'.$lang;
			}
      return $lang;
    }

}
