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
	var $statistic = array();

	function getTextLanguage($text)
	{
		$this->statistic = array();
		$text = troca($text, '.', ' ');
		$text = troca($text, ',', ' ');
		$text = troca($text, '!', ' ');
		$text = troca($text, '?', ' ');

		$w = explode(' ', $text);
		$rst = array();
		foreach ($w as $word) {
			$this->getTextLanguage_process($text);

			$fda = $this->statistic;
			foreach ($fda as $lang => $total) {
				if (!isset($rst[$lang])) {
					$rst[$lang] = 0;
				}
				$rst[$lang] = $rst[$lang] + $total;
			}
		}
		return $this->decision($rst);
	}

	function train()
	{
		$sx = form_simple('');
		$t = get("text");
		$t = mb_strtolower($t);
		$t = troca($t, ' ', ';');
		$t = troca($t, chr(13), ';');
		$t = troca($t, chr(10), ';');
		$t = troca($t, chr(9), ';');
		$t = troca($t, chr(8), ';');
		$t = troca($t, '*', ';');
		$t = troca($t, '/', ';');
		$t = troca($t, '.', ';');
		$t = troca($t, '!', ';');
		$t = troca($t, ',', ';');
		$t = troca($t, '?', ';');
		$t = troca($t, '(', ';');
		$t = troca($t, ')', ';');
		$t = troca($t, '[', ';');
		$t = troca($t, ']', ';');
		$t = troca($t, '{', ';');
		$t = troca($t, '}', ';');
		$t = troca($t, '&', ';');
		$t = troca($t, ':', ';');
		$t = troca($t, '—', ';');
		$t = troca($t, '-', '');
		$t = troca($t, "'", '');
		$t = troca($t, "´", '');
		$t = troca($t, "`", '');
		$t = explode(';', $t);

		$w = array();

		foreach ($t as $term) {
			$term = trim($term);
			/* RULE: se não vazio e se não tem número */
			if (!empty($term)) {
				$snum = sonumero($term);
				if (empty($snum)) {
					if (!isset($w[$term])) $w[$term] = 0;
					$w[$term] = $w[$term]  + 1;
				}
			}
		}



		/**********************************************/
		$stop_words = array();
		$md = round(count($w) / 100); /* 1% */

		$sx .= 'Treino: http://www.dominiopublico.gov.br/download/texto/bv000255.pdf';

		$sx .= '<li>/* Termos analisados: ' . count($w) . '*/</li>';
		$sx .= '<li>/* Minimo para stopwords: ' . $md . '*/</li>';
		$sx .= '$wordList[\'\'] = array( ' . $this->show_array($w, $md) . ')';


		/* End */
		$end = array();
		foreach ($w as $term => $total) {
			$we = array();
			for ($r = 2; $r <= 4; $r++) {
				$wd = mb_substr($term, strlen($term) - $r, $r);
				if (strlen($wd) == $r) {
					if (!isset($end[$wd])) $end[$wd] = 0;
					$end[$wd] = $end[$wd] + $total;
				}
			}
		}
		$mt = round(count($end) / 50);
		$sx .= '<li>Minimo para terminacoes: ' . $mt . '</li>';
		$sx .= $this->show_array($end, $mt);

		/* Digrafos */
		$dig = array();
		foreach ($w as $term => $total) {
			$we = array();
			for ($r = 0; $r < strlen($term); $r++) {
				$wd = trim(mb_substr($term, $r, 2));
				if (strlen($wd) == 2) {
					if (!isset($dig[$wd])) $dig[$wd] = 0;
					$dig[$wd] = $dig[$wd] + $total;
				}
			}
		}
		$md = round(count($dig) / 20);
		$sx .= '<li>Minimo para digrafos: ' . $md . '</li>';
		$sx .= $this->show_array($dig, $md);

		return $sx;
	}
	function show_array($w, $limit)
	{
		$limit_display_ln = 0;
		$sx = '';
		foreach ($w as $term => $total) {
			if (($total > $limit) and (strlen(trim($term)) > 0)) {
				$sx .= "'" . $term . "', ";
				$limit_display_ln++;
				if ($limit_display_ln > 10) {
					$limit_display_ln = 0;
					$sx .= '<br>';
				}
			}
		}
		return $sx;
	}

	function getTextLanguage_process($text)
	{
		$text = ascii(mb_strtolower($text));
		$supported_languages = array(
			'en',
			'de',
			'es',
			'pt',

		);
		// German Word list
		// from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
		$wordList['de'] = array(
			'der', 'die', 'und', 'in', 'den', 'von',
			'zu', 'das', 'mit', 'sich', 'des', 'auf', 'für', 'ist', 'im',
			'dem', 'nicht', 'ein', 'Die', 'eine'
		);
		// English Word list
		// from http://en.wikipedia.org/wiki/Most_common_words_in_English
		$wordList['en'] = array(
			'by', 'his', 'was', 'an', 'and', 'is', 'to', 'of', 'as', 'a', 'for',
			'the', 'in', 'war', 'are', 'on', 'like', 'one', 'free', 'ebooks', 'at', 'planet',
			'ebook', 'com', 'i', 'were', 'winston', 'into', 'though', 'not', 'from', 'with', 'him',
			'old', 'it', 'had', 'been', 'face', 'more', 'than', 'man', 'about', 'made', 'no',
			'even', 'up', 'who', 'way', 'which', 'so', 'that', 'eyes', 'you', 'when', 'out',
			'do', 'words', 'still', 'could', 'be', 'but', 'there', 'he', 'over', 'party', 'very',
			'down', 'little', 'seemed', 'said', 'own', 'another', 'away', 'again', 'did', 'only', 'thought',
			'back', 'any', 'would', 'long', 'moment', 'or', 'what', 'they', 'all', 'time', 'your',
			'knew', 'can', 'this', 'some', 'always', 'their', 'against', 'its', 'other', 'after', 'three',
			'them', 'never', 'then', 'round', 'himself', 'now', 'thing', 'years', 'past', 'because', 'two',
			'if', 'know', 'must', 'have', 'say', 'might', 'her', 'she', 'almost',
			'same', 'before', '’', 'will', 'me', 'we',
		);
		// from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
		$wordList['pt'] = array(
			'a', 'de', 'do', 'da', 'são', 'o', 'para', 'em', 'e', 'este', 'pode',
			'ser', 'que', 'não', 'as', 'nos', 'se', 'um', 'como', 'é', 'na', 'das',
			'ao', 'mesmo', 'era', 'uma', 'mas', 'dos', 'já', 'os', 'grande', 'à', 'tudo',
			'todos', 'por', 'outro', 'no', 'mais', 'aqui', 'esta', 'há', 'ou', 'outra', 'com',
			'seus', 'outros', 'quando', 'só', 'dia', 'lhe', 'ainda', 'aos', 'foi', 'tempo', 'pela',
			'eu', 'nem', 'assim', 'ele', 'também', 'sem', 'me',
		);
		// from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
		$wordList['es'] = array(
			'cl', 'escuela', 'de', 'filosofía', 'universidad', 'arcis', 'era', 'un', 'y',
			'los', 'las', 'winston', 'con', 'la', 'en', 'el', 'su', 'por', 'se', 'entre',
			'no', 'para', 'que', 'una', 'él', 'a', 'al', 'estaba', 'sólo', 'más', 'unos',
			'años', 'gran', 'esto', 'del', 'tenía', 'sus', 'desde', 'uno', 'le', 'te', 'palabras',
			'algo', 'podía', 'ser', 'pero', 'había', 'todo', 'partido', 'muy', 'incluso', 'parecía', 'lo',
			'otra', 'vez', 'sin', 'eran', 'sobre', 'si', 'todos', 'como', 'es', 'sido', 'siempre',
			'habían', 'hasta', 'mismo', 'nunca', 'ni', 'ya', 'ella', 'ahora', 'o', 'qué',
			'antes', 'dos', 'cuando', 'sí', 'casi', 'dijo',
		);


		/************************************************************************/
		$end['de'] = array();
		$end['en'] = array(
			'ic', 'ur', 'ir', 'air', 'ne', 'ry', 'ary', 'er', 'ter', 'tter', 'wn',
			'own', 'by', 'is', 'his', 'en', 'me', 'ame', 'ge', 'll', 'ell', 'as',
			'was', 'an', 'sh', 'ish', 'st', 'ist', 'nd', 'and', 'rk', 'ed', 'sed',
			'id', 'se', 'ose', 'ng', 'ing', 'ting', 'al', 'ial', 'on', 'ion', 'tion',
			'to', 'ken', 'rt', 'ort', 'of', 'ced', 'try', 'cal', 'ical', 'est', 'or',
			'for', 'he', 'the', 'el', 'een', 'our', 'ks', 'ding', 'ad', 'ce', 'nce',
			'ence', 'ss', 'ass', 'fe', 'ife', 'life', 'in', 'th', 'age', 'ia', 'nt',
			'es', 'ces', 'ring', 'can', 'il', 'ar', 'war', 're', 'are', 'ly', 'lly',
			'ally', 'ted', 'cted', 'ys', 'ays', 'ure', 'ture', 'mes', 'imes', 'ked', 'ong',
			'rs', 'ers', 'ls', 'lls', 'ns', 've', 'ive', 'art', 'ke', 'ike', 'like',
			'ny', 'any', 'ms', 'ch', 'uch', 'such', 'ig', 'big', 'ht', 'ght', 'ught',
			'wo', 'two', 'tes', 'om', 'oom', 'room', 'ory', 'one', 'ee', 'ree', 'free',
			'oks', 'ooks', 'at', 'et', 'net', 'anet', 'ok', 'ook', 'book', 'com', 'ight',
			'ld', 'old', 'ay', 'day', 'ere', 'were', 'king', 'ton', 'ston', 'ith', 'led',
			'nto', 'into', 'ast', 'le', 'ile', 'ind', 'ped', 'pped', 'gh', 'ugh', 'ough',
			'tory', 'ons', 'ions', 'ot', 'not', 'ent', 'ty', 'ust', 'rom', 'from', 'long',
			'with', 'im', 'him', 'way', 'lt', 'ts', 'end', 'it', 'red', 'had', 'been',
			'all', 'us', 'ous', 'ace', 'face', 'ore', 'more', 'han', 'than', 'de', 'ide',
			'man', 'ut', 'out', 'bout', 'ck', 'ack', 'dly', 'ome', 'some', 'res', 'ade',
			'made', 'no', 'use', 'ying', 'ven', 'even', 'ff', 'off', 'my', 'te', 'ate',
			'up', 'ho', 'who', 'ine', 'ove', 'ral', 'ite', 'zed', 'hose', 'ich', 'hich',
			'so', 'ved', 'hat', 'that', 'yes', 'eyes', 'ow', 'low', 'ou', 'you', 'hen',
			'when', 'her', 'ther', 'hing', 'side', 'ity', 'ice', 'oice', 'do', 'came', 'ue',
			'lled', 'med', 'hand', 'ned', 'rned', 'nk', 'what', 'ds', 'rds', 'ords', 'ill',
			'till', 'ble', 'able', 'ment', 'reen', 'uld', 'ould', 'be', 'but', 'here', 'ely',
			'tely', 'ver', 'over', 'ess', 'ness', 'dy', 'ody', 'body', 'rely', 'rty', 'arty',
			'ery', 'very', 'ened', 'just', 'ded', 'nded', 'rld', 'orld', 'down', 'eet', 'tle',
			'ttle', 'ies', 'ling', 'rn', 'per', 'ning', 'emed', 'pt', 'ept', 'ered', 'ner',
			'aid', 'said', 'ep', 'eep', '’s', 'rd', 'ord', 'word', 'ance', 'ant', 'away',
			'ain', 'gain', 'ving', 'did', 'ever', 'nly', 'only', 'back', 'ear', 'sly', 'und',
			'ound', 'ined', 'sion', 'ard', 'eing', 'hed', 'ched', 'how', 'ten', 'em', 'ged',
			'ual', 'ey', 'hey', 'they', 'ime', 'time', 'your', 'nted', 'ct', 'ew', 'new',
			'knew', 'lace', 'this', 'lf', 'elf', 'self', 'ost', 'most', 'ied', 'od', 'ood',
			'ways', 'ses', 'eir', 'heir', 'ber', 'mber', 'ated', 'ps', 'aps', 'ared', 'ger',
			'gs', 'ngs', 'ings', 'nst', 'inst', 'tly', 'ible', 'ak', 'eak', 'peak', 'its',
			'see', 'ect', 'fter', 'ead', 'hree', 'hem', 'them', 'der', 'then', 'nts', 'ents',
			'ards', 'ssed', 'less', 'ave', 'rly', 'ul', 'ful', 'ntly', 'wer', 'ink', 'son',
			'ole', 'hole', 'nger', 'now', 'bly', 'ably', 'ves', 'hy', 'sted', 'kind', 'ars',
			'ears', 'past', 'op', 'go', '’', 'ause', 'ious', 'get', 'ase', 'sing', 'if',
			'shed', 'used', 'act', 'know', 'must', 'mind', 'hink', 'rst', 'have', 'say', 'nds',
			'eat', 'les', 'she', 'come', 'ple', 'ople', 'nes', 'lity', 'haps', 'same', 'hout',
			'fore', 'ower', 'will', 'ake', 'few', '’t', 'we', 'make', 'lia',
		);
		$end['pt'] = array(
			'na', 'ana', 'mana', 'de', 'do', 'ado', 'is', 'to', 'te', 'nte', 'ente',
			'ca', 'al', 'ual', 'ante', 'ro', 'iro', 'eiro', 'la', 'da', 'ade', 'dade',
			'ão', 'são', 'lo', 'ido', 'so', 'as', 'nas', 'ra', 'ara', 'para', 'ns',
			'ais', 'se', 'ase', 'or', 'por', 'sas', 'em', 'ura', 'tura', 'ce', 'ar',
			'ral', 'ta', 'nta', 'ina', 'ste', 'este', 'ode', 'pode', 'er', 'ser', 'ue',
			'que', 'não', 'ja', 'eja', 'rado', 'ma', 'am', 'das', 'es', 'res', 'ores',
			'va', 'os', 'mos', 'amos', 'nos', 'dar', 'ter', 'uma', 'nde', 'ande', 'um',
			'mo', 'omo', 'como', 'sso', 'isso', 'é', 'ira', 'eira', 'ou', 'ei', 'co',
			'uco', 'ouco', 'ois', 'pois', 'has', 'me', 'ao', 'sto', 'osto', 'im', 'smo',
			'esmo', 'era', 'ada', 'mas', 'ome', 'nome', 'á', 'ita', 'go', 'dos', 'ros',
			'zes', 'já', 'sta', 'esta', 'sa', 'usa', 'ousa', 'tes', 'odo', 'ço', 'ama',
			'eta', 're', 'tre', 'ntre', 'us', 'ous', 'dous', 'tos', 'ia', 'ões', 'ora',
			'ndo', 'endo', 'ato', 'à', 'udo', 'tudo', 'rta', 'hos', 'lhos', 'ses', 'odos',
			'ias', 'dias', 'ua', 'dem', 'osa', 'rra', 'erra', 'ai', 'tro', 'utro', 'ha',
			'nha', 'inha', 'nal', 'ava', 'tava', 'vam', 'avam', 'ez', 'iam', 'no', 'adas',
			'nto', 'dor', 'ador', 'às', 'í', 'aí', 'ir', 'mais', 'ntes', 'ui', 'qui',
			'aqui', 'tá', 'stá', 'emos', 'há', 'le', 'ele', 'sse', 'esse', 'tra', 'utra',
			'les', 'vo', 'ras', 'tal', 'bem', 'ados', 'tão', 'oas', 'lar', 'om', 'com',
			'eus', 'seus', 'iros', 'rto', 'erto', 'ssa', 'ossa', 'eles', 'tros', 'ram', 'eram',
			'tas', 'eu', 'gar', 'el', 'ria', 'ando', 'iu', 'rte', 'orte', 'ó', 'só',
			'tado', 'tem', 'isto', 'dia', 'gos', 'ito', 'uito', 'he', 'lhe', 'nda', 'inda',
			'elo', 'igo', 'migo', 'tor', 'gem', 'itos', 'usas', 'vi', 'aos', 'ens', 'mens',
			'rá', 'ará', 'odas', 'oi', 'foi', 'anto', 'rar', 'po', 'mpo', 'empo', 'ém',
			'ela', 'pela', 'ião', 'ista', 'aria', 'té', 'até', 'der', 'arte', 'anos', 'az',
			'ça', 'ento', 'fim', 'vez', 'las', 'elas', 'bre', 'obre', 'meu', 'uem', 'quem',
			'iz', 'lha', 'seu', 'erá', 'pelo', 'tou', 'lá', 'za', 'eza', 'aram', 'oa',
			'li', 'ês', 'enos', 'zer', 'izer', 'nça', 'uns', 'io', 'eio', 'uele', 'sei',
			'tar', 'rua', 'ovo', 've', 'eve', 'nem', 'rque', 'ho', 'nho', 'ntos', 'stas',
			'stes', 'ida', 'vida', 'cer', 'hor', 'eito', 'nada', 'car', 'sim', 'ssim', 'ver',
			'éia', 'eria', 'sem', 'des', 'bém', 'ior', 'rem', 'azer', 'ós', 'sos', 'mem',
			'omem', 'ga', 'onde', 'pre', 'mpre', 'rão', 'umas', 'rei', 'undo', 'ano', 'via',
			'uém', 'lho', 'elho', 'osso', 'uas', 'gora', 'idos', 'ças', 'ece', 'essa', 'vos',
			'rro', 'cos', 'ne', 'rer', 'tras', 'deu', 'los', 'mal', 'éu', 'eis',
		);
		$end['es'] = array(
			'ia', 'hia', 'phia', 'cl', 'la', 'ela', 'uela', 'de', 'ía',
			'fía', 'ad', 'dad', 'idad', 'is', 'cis', 'rcis', 'ón', 'ión', 'te', 'rte',
			'arte', 'lo', 'ra', 'era', 'un', 'día', 'so', 'oso', 'il', 'os', 'los',
			'es', 'an', 'ban', 'aban', 'as', 'las', 'ce', 'on', 'ton', 'ston', 'con',
			'lla', 'da', 'ada', 'en', 'el', 'ho', 'cho', 'echo', 'su', 'zo', 'or',
			'por', 'ar', 'lar', 'to', 'nto', 'ento', 'se', 'ó', 'zó', 're', 'tre',
			'ntre', 'tas', 'al', 'tal', 'sas', 'ria', 'oria', 'ue', 'que', 'nque', 'no',
			'nte', 'ente', 'ez', 'ara', 'para', 'tar', 'itar', 'na', 'una', 'ta', 'nta',
			'enta', 'él', 'res', 'bres', 'das', 'ras', 'do', 'ndo', 'ores', 'ado', 'iado',
			'nde', 'rse', 'arse', 'ior', 'rior', 'ba', 'aba', 'taba', 'ed', 'ólo', 'me',
			'ro', 'tro', 'stro', 'ás', 'más', 'ura', 'cara', 'bre', 'mbre', 'nos', 'unos',
			'co', 'nco', 'ños', 'ran', 'gran', 'nes', 'ones', 'osas', 'ió', 'cia', 'acia',
			'ir', 'ncia', 'sta', 'esta', 'ante', 'sto', 'esto', 'raba', 'ana', 'del', 'io',
			'dio', 'nía', 'us', 'sus', 'inta', 've', 'ces', 'ma', 'llo', 'illo', 'ando',
			'ias', 'eces', 'cada', 'rta', 'sde', 'esde', 'uro', 'uno', 'sos', 'esos', 'jos',
			'dos', 'ados', 'ojos', 'le', 'iera', 'é', 'ano', 'mano', 'ían', 'bras', 'ie',
			'ntro', 'oz', 'voz', 'ena', 'go', 'lgo', 'algo', 'er', 'ver', 'tes', 'ca',
			'ga', 'jo', 'ha', 'cha', 'nar', 'dor', 'ser', 'ero', 'pero', 'bía', 'rlo',
			'arlo', 'odo', 'todo', 'fue', 'ya', 'ido', 'tido', 'ello', 'uy', 'muy', 'ida',
			'mas', 'uera', 'uso', 'luso', 'és', 'les', 'ales', 'undo', 'cía', 'ajo', 'bajo',
			'vo', 'tos', 'elo', 'nada', 'ner', 'ener', 'lor', 'olor', 'odas', 'ros', 'nas',
			'sa', 'stos', 'des', 'tras', 'otro', 'tado', 'endo', 'bra', 'abra', 'saba', 'daba',
			'ego', 'uego', 'tra', 'otra', 'vez', 'in', 'sin', 'rgo', 'argo', 'llas', 'eran',
			'enos', 'alla', 'uía', 'obre', 'nal', 'tía', 'nido', 'ído', 'ber', 'aber', 'si',
			'dado', 'ble', 'ible', 'odos', 'ría', 'rado', 'ien', 'ntos', 'í', 'sí', 'así',
			'mo', 'omo', 'como', 'bien', 'ora', 'rio', 'erio', 'rdad', 'onde', 'je', '»',
			'só', 'ja', 'cias', 'adas', 'tó', 'ún', 'gún', 'sido', 'pre', 'mpre', 'stas',
			'idos', 'has', 'lado', 'eso', 'nado', 'rto', 'erto', 'llos', 'dar', 'bles', 'ua',
			'gua', 'ngua', 'ial', 'asta', 'eto', 'tura', 'za', 'tros', 'laba', 'erse', 'nca',
			'tres', 'rra', 'erra', 'tad', 'ntes', 'ios', 'smo', 'ismo', 'cto', 'ño', 'rios',
			'ntal', 'unca', 'ni', 'cado', 'rar', 'lí', 'llí', 'unto', 'ese', 'aso', 'sar',
			'vió', 'onto', 'rido', 'ina', 'hora', 'ella', 'ino', 'ró', 'ron', 'aron', 'rle',
			'ste', 'este', 'ués', 'ual', 'ntó', 'bro', 'guna', 'zón', 'oda', 'toda', 'cio',
			'uel', 'quel', 'anto', 'cer', 'acer', 'én', 'ién', 'oco', 'poco', 'po', 'mpo',
			'empo', 'able', 'ué', 'qué', 'bros', 'rque', 'ario', 'erte', 'cido', 'ivo', 'rás',
			'eza', 'va', 'nsar', 'nces', 'osa', 'dido', 'sino', 'esa', 'ea', 'vía', 'he',
			'eron', 'ucho', 'anos', 'ías', 'edad', 'der', 'rada', 'yo', 'tio', 'unas', 'ral',
			'ne', 'asi', 'casi', 'eres', 'tan', 'modo', 'á', 'ntra', 'ase', 'gar', 'mos',
			'amos', 'sia', 'asia', 'vida', 'oder', 'sado', 'án', 'cir', 'ijo', 'dijo', 'son',
			'rde', 'rá', 'emos', 'ay', 'hay', 'lia', 'rpo', 'erpo', 'ulia',
		);

		$end['de'] = array();
		$end['de'] = array();
		$end['de'] = array();
		$end['de'] = array();

		$middle['de'] = array();
		$middle['en'] = array(
			'er', 'ri', 'ic', 'ar', 'rt', 'th', 'hu', 'ur', 'bl', 'la', 'ai',
			'ir', 'ju', 'un', 'ne', 'an', 'nu', 'ua', 'ry', 'be', 'et', 'tt',
			'te', 'kn', 'no', 'ow', 'wn', 'by', 'hi', 'is', 'pe', 'en', 'na',
			'am', 'me', 'ge', 'eo', 'or', 'rg', 'rw', 'we', 'el', 'll', 'wa',
			'as', 'ng', 'gl', 'li', 'sh', 'ov', 've', 'st', 'nd', 'es', 'ss',
			'sa', 'ay', 'yi', 'jo', 'ou', 'rn', 'al', 'cr', 'it', 'ti', 'wo',
			'rk', 'ch', 'ha', 'ra', 'ac', 'ct', 'se', 'ed', 'lu', 'uc', 'ci',
			'id', 'pr', 'ro', 'os', 'bi', 'in', 'so', 'oc', 'ia', 'sm', 'op',
			'pp', 'po', 'si', 'io', 'on', 'to', 'ot', 'ta', 'ni', 'ut', 'ts',
			'sp', 'ok', 'ke', 'su', 'up', 'of', 'de', 'em', 'mo', 'at', 'wr',
			'od', 'du', 'ce', 'oe', 'tr', 'fi', 'ol', 'le', 'mi', 'ca', 'fo',
			'he', 'eg', 'go', 'im', 'ma', 'fa', 'rm', 'dy', 'ys', 'pi', 'ee',
			'ei', 'ig', 'gh', 'ht', 'ty', 'yf', 'nf', 'ks', 'nc', 'cl', 'ud',
			'di', 'oa', 'ad', 'wi', 'ga', 'ie', 'do', 'cu', 'um', 'nt', 'ex',
			'xp', 'ki', 'if', 'fe', 'ho', 'om', 'ag', 'lo', 'cc', 'co', 'ld',
			're', 'ep', 'pu', 'ub', 'pa', 'iv', 'vi', 'il', 'ly', 'ec', 'tu',
			'gu', 'ul', 'lt', 'nk', 'gr', 'ea', 'br', 'rs', 'ls', 'ns', 'fl',
			'ue', 'je', 'sc', 'ib', 'au', 'ik', 'ny', 'og', 'gi', 'ms', 'ug',
			'tw', 'oo', 'ew', 'ws', 'ak', 'tc', 'fr', 'eb', 'bo', 'pl', 'ap',
			'pt', 'da', 'ck', 'ef', 'ff', 'sl', 'ip', 'qu', 'ui', 'kl', 'hr',
			'ev', 'sw', 'rl', 'us', 'lw', 'oi', 'ab', 'bb', 'ba', 'mp', 'av',
			'vy', 'ru', 'gg', 'dl', 'ds', 'ft', 'rr', 'yl', 'my', 'dr', 'ek',
			'wh', 'va', 'lc', 'wl', 'af', 'az', 'ze', 'ey', 'ye', 'yo', 'vo',
			'ob', 'rf', 'rd', 'mm', 'bu', 'ph', 'iz', 'sk', 'tl', 'dd', 'ky',
			'yt', 'xc', 'yw', 'fu', 'gs', 'aw', 'rv', 'sn', 'nl', 'eh', 'lf',
			'lm', 'mu', 'sy', 'rh', 'sq', 'lk', 'mb', 'rb', 'ps', 'bs', 'ym',
			'gy', 'ix', 'bj', 'py', 'yr', 'gn', 'gt', 'rc', 'nm', 'dv', 'lp',
			'uf', 'xt', 'eu', 'sf', 'lv', 'hy', 'rp', 'kw', 'bt', 'zi', 'nn',
			'oy', 'dn', 'yp', 'eq', 'tm', 'hs', 'ox', 'xe', 'dg', 'mn', 'xa',
			'nv', 'xi', 'za', 'ka', 'lr', 'uo', 'mr', 'nq', 'cy', 'bv', 'ya',
			'wd',
		);
		$middle['pt'] = array(
			'se', 'em', 'ma', 'an', 'na', 'de', 'ac', 'ch', 'ha', 'ad', 'do',
			'as', 'ss', 'si', 'is', 'te', 'ex', 'xt', 'to', 'pr', 'ro', 'ov',
			've', 'en', 'ni', 'ie', 'nt', 'bi', 'ib', 'bl', 'li', 'io', 'ot',
			'ec', 'ca', 'vi', 'ir', 'rt', 'tu', 'ua', 'al', 'es', 'st', 'ud',
			'da', 'br', 'ra', 'il', 'le', 'ei', 'fu', 'ut', 'ur', 'us', 'sp',
			'sc', 'co', 'ol', 'la', 'un', 'iv', 'er', 'rs', 'id', 'pa', 'au',
			'ul', 'lo', 'pe', 'rm', 'mi', 'it', 'ti', 'so', 'ap', 'ar', 'fi',
			'in', 'ns', 'ed', 'du', 'uc', 'ci', 'on', 'ai', 'ob', 'ba', 'di',
			'ig', 'gi', 'ta', 'iz', 'za', 'po', 'or', 'nu', 'up', 'pi', 'll',
			'cl', 'eo', 'sq', 'qu', 'ui', 'sa', 'nf', 'fo', 'ic', 'at', 'ng',
			'ce', 'uf', 'lc', 'fe', 'ri', 'ia', 'od', 're', 'tr', 'bu', 'vr',
			'me', 'sd', 'ue', 'ej', 'ja', 'lt', 'im', 'am', 'cr', 'ev', 'va',
			'mo', 'os', 'oc', 'vo', 'lu', 'no', 'aj', 'ju', 'oj', 'je', 'et',
			'ê', 'lg', 'gu', 'um', 'nd', 'om', 'é', 'el', 'ab', 'eg', 'af',
			'ou', 'rd', 'ep', 'oi', 'ga', 'nh', 'ao', 'go', 'op', 'sm', 'á',
			'gn', 'xc', 'og', 'ze', 'fa', 'az', 'be', 'ne', 'ef', 'lh', 'he',
			'dr', 'oe', 'mp', 'gr', 'of', 'à', 'ho', 'zi', 'su', 'av', 'rv',
			'rr', 'ag', 'bo', 'jo', 'xa', 'ez', 'ms', '"c', '"p', '"a', 'í',
			'ge', 'aq', 'xe', 'pl', 'lf', 'oa', 'pu', 'nc', 'ip', 'eu', 'rc',
			'gl', 'sr', 'ae', 'iu', 'mu', 'ó', 'cu', 'eb', 'bs', 'rg', 'nj',
			'hi', 'rn', 'ea', 'nv', 'lv', 'fl', 'lm', 'uv', 's"', 'dm', 'hu',
			'ru', 'nr', 'rq', 'mb', 'xp', 'ub', '"e', 'xi', 'o"', 'if', 'eq',
			'rl', 'sg', 'fr', 'lq', 'ix', 'ux', 'zo', 'nq', 'sf', 'ee', 'rp',
			'rb', 'uz', 'ug', 'mm', 'ã', '"o', 'ct', 'sl', 'oh', 'ax', 'ii',
			'ls', 'lp', 'ah', 'zm', 'nz', 'xo', 'iq', 'ld', 'oq', 'oz', 'a"',
			'uo', 'ds', 'uj', 'rf', 'vu', 'dv', 'ij',
		);
		$middle['es'] = array(
			'ph', 'hi', 'il', 'lo', 'os', 'so', 'op', 'ia', 'cl', 'es',
			'sc', 'cu', 'ue', 'el', 'la', 'de', 'fi', 'of', 'un', 'ni', 'iv',
			've', 'er', 'rs', 'si', 'id', 'da', 'ad', 'ar', 'rc', 'ci', 'is',
			'ge', 'eo', 'or', 'rg', 'll', 'ed', 'di', 'ic', 'le', 'ec', 'ct',
			'tr', 'ca', 'pa', 'rt', 'te', 'ap', 'pi', 'it', 'tu', 'ul', 'ra',
			'lu', 'um', 'mi', 'in', 'no', 'fr', 'ab', 'br', 'ri', 're', 'oj',
			'je', 'ba', 'an', 'as', 'ce', 'wi', 'ns', 'st', 'to', 'on', 'sm',
			'th', 'co', 'rb', 'bi', 'av', 'va', 'en', 'pe', 'ch', 'ho', 'su',
			'sf', 'fu', 'rz', 'zo', 'po', 'bu', 'ur', 'rl', 'mo', 'ol', 'im',
			'vi', 'ie', 'nt', 'se', 'sl', 'li', 'iz', 'ó', 'am', 'me', 'pu',
			'ta', 'cr', 'al', 'sa', 'au', 'nq', 'qu', 'uf', 'ez', 'ev', 'na',
			'fa', 'ag', 'ga', 'lv', 'vo', 'eg', 'gu', 'mb', 'oc', 'ej', 'ja',
			'fo', 'nd', 'do', 'em', 'ma', 'gr', 'ha', 'io', 'ep', 'pr', 'rm',
			'ro', 'et', 'nc', 'hu', 'om', 'ua', 'ig', 'go', 'ot', 'ne', 'ac',
			'cc', 'he', 'du', 'ir', 'gi', 'ti', 'ub', 'rr', 'od', 'pt', 'us',
			'ei', 'nu', 'lc', 'ob', 'sd', 'mu', 'ib', 'uj', 'jo', 'ea', 'za',
			'eq', 'ui', 'é', 'oz', 'lg', 'uc', 'ng', 'pl', 'bl', 'sp', 'mp',
			'up', 'rf', 'uy', 'ru', 'at', 'ya', 'lt', '»', 'az', 'zu', 'if',
			'be', 'af', 'fe', 'nv', 'rn', 'aj', 'sq', 'nf', 'ij', 'aq', 'fl',
			'gs', 'ut', 'og', 'ai', 'nz', 'vu', 'rv', 'rd', 'ld', 'ov', 'lq',
			'eb', 'ex', 'xc', 'bs', 'uv', 'í', 'nm', 'dr', 'sg', 'ip', 'iu',
			'ud', 'nj', 'xp', 'bo', 'uo', 'ug', 'yo', 'ye', 'ay', 'xt', 'bj',
			'ee', 'gn', 'rq', 'ey', 'iq', 'uz', 'lm', 'yu', 'lp', 'xi', 'ah',
			'oy', 'pc', 'xa', 'oh', 'ox', 'ls', 'ef', 'ju', 'xo', 'zc', 'rp',
			'gl', 'ji', 'ds', 'á', 'sv', 'eu', 'mn', 'xu', 'ii', 'dm', 'oi',
			'ae', 'sh', 'nr', 'ú', 'sy', 'ym', 'cn', 'gt', 'ñ'
		);

		// clean out the input string - note we don't have any non-ASCII
		// characters in the Word lists... change this if it is not the
		// case in your language wordlists!
		$text = preg_replace("/[^A-Za-z]/", ' ', $text);
		$text = ' ' . $text . ' ';
		// count the occurrences of the most frequent words

		/****************************** Zera contador */
		foreach ($supported_languages as $language) {
			$counter[$language] = 0;
		}

		// split the text into words
		foreach ($supported_languages as $language) {
			$terms = $wordList[$language];
			for ($r = 0; $r < count($terms); $r++) {
				$total = substr_count($text, ' ' . $terms[$r] . ' ');
				$counter[$language] = $counter[$language] + $total * 5;
			}

			$terms = $end[$language];
			for ($r = 0; $r < count($terms); $r++) {
				$total = substr_count($text, $terms[$r] . ' ');
				$counter[$language] = $counter[$language] + $total;
			}

			$terms = $middle[$language];
			for ($r = 0; $r < count($terms); $r++) {
				$total = substr_count($text, $terms[$r]);
				$counter[$language] = $counter[$language] + $total;
			}
		}
		$this->statistic = $counter;
		return $this->decision($counter);
	}

	function decision($counter)
	{
		$lang = 'NaN';
		$max = 1;
		foreach ($counter as $key => $value) {
			if ($value >= $max) {
				$lang = $key;
				$max = $value;
			}
		}
		if ($lang == 'pt') {
			$lang = 'pt-BR';
		}
		if ($lang == 'NaN') {
			//echo '<h1>Language: '.$text.'<br>==>'.$lang;
		}
		return $lang;
	}
}
