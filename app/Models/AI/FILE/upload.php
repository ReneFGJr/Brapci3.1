<?php

namespace App\Models\AI\FILE;

use CodeIgniter\Model;

class upload extends Model
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

	function upload()
		{
			$sx = '';
			$sx .= form_open_multipart();
			$sx .= form_upload('file');
			$sx .= form_submit(array('name'=>'submit','value'=>'Upload'));
			$sx .= form_close();

			if (isset($_FILES['file']['tmp_name']))
			{
				$fileO = $_FILES['file']['tmp_name'];

				/*************************************** FILE */
				$fileD = '../.tmp/';
				dircheck($fileD);
				$fileD = '../.tmp/';
				dircheck($fileD);
				$fileD = '../.tmp/AI/';
				dircheck($fileD);
				$fileD .= 'test.pdf';
				/********************************** MOVE FILE */
				move_uploaded_file($fileO,$fileD);
				$sx = 'Saved';
				$sx .= metarefresh(PATH.COLLECTION.'/file/process');
			}

			$sx = bs(bsc($sx));
			return $sx;
		}

}
