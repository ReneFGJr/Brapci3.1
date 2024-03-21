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

    function access_point()
        {
            $sx = '';
            $sx .= h('Ponto de acesso (Autoridade Pessoa');
            return $sx;
        }

    function show_index($tp='',$lt='')
        {
            $sx = '';
            $dir = '../.tmp/indexes/'.$tp;
            if (is_dir($dir))
                {
                    $files = scandir($dir);
                    foreach($files as $id=>$file)
                    {
                    if (($file != '.') and ($file != '..') and ($file != 'index.php'))
                        {
                            $file = troca($file,'index_','');
                            $file = troca($file,'.php','');
                            $sx .= '<a href="'.PATH.'/indexes/'.$tp.'/'.$file.'" class="me-2">';
                            $sx .= $file;
                            $sx .= '</a>';
                            if ($lt == '') {
                                $lt = $file;
                            }
                        }
                    }
                    $sx = bsc($sx,12);
                }
                if ($lt != '')
                    {
                        $sx .= bsc(file_get_contents($dir.'/index_'.$lt.'.php'),12);
                    }
            return bs($sx);
        }

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
                            $indexes[$file] = troca($indexes[$file],PATH.'/v/',PATH.COLLECTION.'/v/');
                        }
                }
            if (count($indexes) == 0)
                {
                    $sx .= view('Brapci/Pages/under_construction');
                } else {
                    foreach($indexes as $label=>$content)
                        {
                            $sx .= h(lang('brapci.'.$label));
                            $sx .= $content;
                        }
                    $sx = bs(bsc($sx,12));
                }
            return $sx;
        }

}
