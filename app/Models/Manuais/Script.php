<?php

namespace App\Models\Manuais;

use CodeIgniter\Model;

class Script extends Model
{
    protected $DBGroup          = 'manuais';
    protected $table            = 'scripts';
    protected $primaryKey       = 'id_s';
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

    function show_manual($id,$version='')
        {
            $sx = '';
            $dt = $this->where('s_produto',$id)->findAll();
            foreach($dt as $id=>$line)
                {
                    $cmd = $line['s_command'];
                    if (substr($cmd,0,1) == '[')
                        {
                            $name = $cmd;
                            $idP = $line['s_sub_produto'];
                            $link = '<a href="'.PATH.'manual/view/'.$idP.'">a';
                            $linka = '</a>';
                            $name = troca($name,'[','');
                            $name = troca($name, ']', '');
                            $sx .= bsc($link.$name.$linka,12);
                        } else {
                            $sx .= bsc('<pre>' . $line['s_command'] . '</pre>', 6);
                            $sx .= bsc($line['s_description_pt'], 6);
                        }
                }
            return $sx;
        }
}
