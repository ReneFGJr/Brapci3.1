<?php

namespace App\Models\AI\FILE;

use CodeIgniter\Model;

class pdf extends Model
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

	function file()
		{
		$fileD = '../.tmp/AI/';
		$fileD .= 'test.pdf';
		return $fileD;
		}

	function show_files()
	{
		$sx = '';
		/*************************************** FILE */

		$fileD = $this->file();
		if (file_exists($fileD))
			{
				$sx .= $this->fileInfo($fileD);
			} else {
				$sx = bsmessage('File not found',2);
			}
		$sx = bs(bsc($sx,12));
		return $sx;
	}

	function pdf_to_txt($file,$fileD)
	{
		$RDF = new \App\Models\Rdf\RDF();

		$dir = 'E:/Projeto/Bin/poppler/bin/';
		$cmd = $dir . 'pdftotext.exe ' . $file;

		if (!file_exists($file))
			{
				echo "ERRO $file";
				exit;
			}

		$rst = shell_exec($cmd);
		$txt = file_get_contents($fileD);

		$NLP = new \App\Models\AI\NLP\TextPrepare();
		$txt2 = $NLP->JoinSentences($fileD);

		file_put_contents($fileD,$txt2);
		return true;
	}

	function pdf_to_html($file)
		{
			$fileD = $this->file();
			$fileD_text = troca($fileD,'.pdf','.txt');

			$dir = 'D:/Projeto/Bin/poppler/bin/';
			$cmd = $dir.'pdftohtml.exe '.$fileD.' -xml';
			$cmd = $dir . 'pdftotext.exe ' . $fileD;

			$rst = shell_exec($cmd);
			echo '<tt>===<br>'.$cmd. '<br>===</tt>';
			$txt = file_get_contents($fileD_text);


			$NLP = new \App\Models\AI\NLP\TextPrepare();
			$txt2 = $NLP->JoinSentences($fileD_text);
			echo '<pre>' . $txt2 . '</pre>';
		}

	function fileInfo($fileD)
		{
			$sx = '';
			$sx .= h($fileD,3);
			$sx .= h('file size: '.number_format(filesize($fileD)/1024/1024,1,'.',',').' Mega Bytes',6);
			$sx .= '<hr>';
			return $sx;
		}
}
