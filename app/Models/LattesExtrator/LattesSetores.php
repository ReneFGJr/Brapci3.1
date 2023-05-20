<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesSetores extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattes_setor_atividade';
    protected $primaryKey       = 'id_sa';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_sa', 'sa_name', 'sa_sign', 'sa_type'
    ];

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

    var $setores = [];

    function setores()
        {
            if ($this->setores == []) {
            $dt = $this->findAll();
            $st = [];
            foreach($dt as $id=>$line)
                {
                    $st[$id] = $line['sa_name'];
                }
                $this->setores = $st;
            }
            return $this->setores;
        }

    function show($t)
        {
            if ($t == '')
                {
                    return "";
                }
            /************************* Setores */
            $setotes = $this->setores();

            $s = explode(';',$t);
            $sx = '';
            foreach($s as $ids)
                {
                    if (isset($setores[$ids]))
                        {
                            if ($sx != '') { $sx .= ';'; }
                            $sx .= $setores[$ids];
                        }
                }
            return $sx;
        }

    function setor($name, $type='0')
    {
        $dt = $this
            ->where('sa_name', $name)
            ->where('sa_type', $type)
            ->first();

        if ($dt == '') {
            $sign = $this->sign($name);
            $dt['sa_name'] = $name;
            $dt['sa_sign'] = $sign;
            $dt['sa_type'] = $type;
            return $this->set($dt)->insert();
        } else {
            return $dt['id_sa'];
        }
    }

    function sign($n)
    {
        $t = ['de', 'da', 'o', 'a'];
        foreach ($t as $id => $tn) {
            $n = troca($n, $tn, '');
        }
        while (strpos($n, '  ')) {
            $n = troca($n, '  ', ' ');
        }
        if (strpos($n, ' ') > 0) {
            $n2 = substr($n, strpos($n, ' ') + 1, 1);
            $n = substr($n, 0, 1) . $n2;
        } else {
            $n = substr($n, 0, 2);
        }
        $n = mb_strtoupper($n);
        return $n;
    }
}
