<?php

namespace App\Models\AI\NLP\Text;

use CodeIgniter\Model;

class Volume extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'volumes';
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

    function normalize($n)
        {
            $c = [];
            $c['v.'] = '';
            foreach($c as $from=>$to)
                {
                    $n = troca($n,$from,$to);
                }
            if (sonumero($n) != $n)
                {
                    echo h('ERRO DE VOLUME');
                    //pre($n);
                    $n = '::Erro Volume:: '.$n;
                }
            return $n;
        }
}
