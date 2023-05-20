<?php

namespace App\Models\LattesExtrator;

use CodeIgniter\Model;

class LattesNatureza extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'lattes_natureza';
    protected $primaryKey       = 'id_nt';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_nt','nt_name','nt_sign','nt_type'
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

    function natureza($name, $type)
        {
            $name = trim($name);
            if ($name == '') { $name = 'NC'; }
            $dt = $this
            ->where('nt_name',$name)
            ->where('nt_type',$type)
            ->first();

            if ($dt == '')
                {
                    $sign = $this->sign($name);
                    $dt['nt_name'] = $name;
                    $dt['nt_sign'] = $sign;
                    $dt['nt_type'] = $type;
                    return $this->set($dt)->insert();
                } else {
                    return $dt['id_nt'];
                }


        }

        function sign($n)
            {
                $t = ['de','da','o','a'];
                foreach($t as $id=>$tn)
                    {
                        $n = troca($n,$tn,'');
                    }
                while(strpos($n,'  ')) { $n = troca($n,'  ',' '); }
                if (strpos($n,' ') > 0)
                    {
                        $n2 = substr($n,strpos($n,' ')+1,1);
                        $n = substr($n,0,1).$n2;

                    } else {
                        $n = substr($n,0,2);
                    }
                $n = mb_strtoupper($n);
                return $n;

            }
}
