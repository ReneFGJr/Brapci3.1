<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class Bibtex extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'bibtices';
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

	function export($d,$type='')
		{
			switch($type)
				{
					case 'article':
						$this->BibtexArticle($d);
						break;
					case 'ris':
						$this->exportRis($d);
						break;
					default:
						echo "Informe o tipo. ex: article";
						exit;
				}
		}

	function short($ln)
	{
		$bibtex = "@article{";

		// Define a citation key (e.g., authorYear)
		$citationKey = isset($ln['Authors'][0]) ? strtolower(explode(' ', $ln['Authors'][0])[0]) : 'unknown';
		$citationKey .= isset($ln['YEAR']) ? $ln['YEAR'] : '';
		$bibtex .= $citationKey . ",\n";

		// Authors
		if (isset($ln['Authors'])) {
			$authors = array_map(function ($author) {
				return nbr_author($author, 2);
			}, $ln['Authors']);
			$bibtex .= "  author = {" . implode('; ', $authors) . "},\n";
		}

		// Title
		$Title = (array)$ln['Title'];
		$title = $Title['pt'][0] ?? $Title['es'][0] ?? $Title['en'][0] ?? $Title['fr'][0] ?? '';
		$bibtex .= "  title = {" . $title . "},\n";

		// Year
		if (isset($ln['YEAR'])) {
			$bibtex .= "  year = {" . $ln['YEAR'] . "},\n";
		}

		// Journal Information
		if (isset($ln['Issue'])) {
			$Issue = (array)$ln['Issue'];

			if (isset($Issue['journal'])) {
				$bibtex .= "  journal = {" . $Issue['journal'] . "},\n";
			}
			if (!empty($Issue['vol'])) {
				$bibtex .= "  volume = {" . sonumero($Issue['vol']) . "},\n";
			}
			if (!empty($Issue['nr'])) {
				$bibtex .= "  number = {" . sonumero($Issue['nr']) . "},\n";
			}
		}

		// DOI (if exists)
		if (isset($ln['DOI'])) {
			$bibtex .= "  doi = {" . $ln['DOI'] . "},\n";
		}

		// URL
		if (isset($ln['ID'])) {
			$bibtex .= "  url = {https://hdl.handle.net/20.500.11959/brapci/" . $ln['ID'] . "},\n";
		}

		// Abstract
		if (isset($ln['Abstract'])) {
			$Abstract = (array)$ln['Abstract'];
			$abstract = $Abstract['pt'][0] ?? $Abstract['es'][0] ?? $Abstract['en'][0] ?? $Abstract['fr'][0] ?? '';
			$abstract = str_replace([chr(13), chr(10)], ' ', $abstract);
			$bibtex .= "  abstract = {" . $abstract . "},\n";
		}

		// Keywords
		if (isset($ln['Subject'])) {
			$keywords = [];
			foreach ($ln['Subject'] as $line) {
				if (is_array($line)) {
					foreach ($line as $word) {
						$keywords[] = $word;
					}
				} else {
					$keywords[] = $line;
				}
			}
			$bibtex .= "  keywords = {" . implode(', ', $keywords) . "},\n";
		}

		// Database and Language
		$bibtex .= "  note = {Database: BRAPCI, Language: Portuguese},\n";

		// End of entry
		$bibtex .= "}\n";

		return ascii($bibtex);
	}



	function BibtexArticle($d)
		{
			$fld = array(
				'author','title','journal',
				'year','volume','number',
				'pages','doi','issn',
				'month','note','eprint',
				'keyword'
			);

			$sx = '';
			$sx .= 'Brapci'.cr();
			$sx .= 'EXPORT DATE: '.date("D M Y").cr();
			$sx .= '@article{'.$d['id'].','.cr();
			for ($r=0;$r < count($fld);$r++)
				{
					$field = $fld[$r];
					if (isset($d[$field]))
						{
							$sx .= $field.' = {'.$d[$fld[$r]].'},'.cr();
						}
				}
			$sx .= 'source = {Brapci},'.cr();

			$sx .= 'post = {'.date("Y-m-d").'}'.cr();
			$sx .= '}'.cr();
			return $sx;
		}
}
