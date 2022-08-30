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

	function show_files()
	{
		$sx = '';
		/*************************************** FILE */
		$fileD = '../.tmp/AI/';
		$fileD .= 'test.pdf';

		if (file_exists($fileD))
			{
				$sx .= $this->fileInfo($fileD);
			} else {
				$sx = bsmessage('File not found',2);
			}
		$sx = bs(bsc($sx,12));
		return $sx;
	}

	function pdf_to_html($file)
		{
			$cmd = 'pdftohtml -v';
			$rst = shell_exec($cmd);
			pre($rst);
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
