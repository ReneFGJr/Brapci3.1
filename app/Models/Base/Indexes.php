<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Indexes extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indexes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function show($id,$ac)
        {
            $sx = '';
            $dir = ".c/indexes/";
            $files = scandir($dir);
            $base = troca(COLLECTION,'/','');
            $indexes = [];
            foreach($files as $id=>$file)
                {
                    if (substr($file,0,strlen($base)) == $base)
                        {
                            $filename = $dir.$file;
                            $indexes[$file] = '<div style="column-count: 3;">'.file_get_contents($filename). '</div>';
                        }
                }
            if (count($indexes) == 0)
                {
                    $sx .= view('Brapci/Pages/under_construction');
                } else {
                    foreach($indexes as $label=>$content)
                        {
                            $sx .= h($label);
                            $sx .= $content;
                        }
                    $sx = bs(bsc($sx,12));
                }
            return $sx;
        }

}
