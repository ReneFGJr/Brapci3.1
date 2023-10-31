<?php

namespace App\Models\AI\NLP\Text;

use CodeIgniter\Model;

class NumberVolume extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'numbervolumes';
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
        $c['n.'] = '';
        $c['Especial'] = 'Esp.';
        foreach ($c as $from => $to) {
            $n = trim(troca($n, $from, $to));
        }

        $valid = ['1', '2', '3', '1/2', '1/3', '1/4', '3/4'];
        foreach ($valid as $id => $v) {
            if ($v == $n) {
                return $n;
            }
        }

        return "ERRO";
        echo h('ERRO DE NUMERO / VOLUME');
        var_dump($n);
        pre('[' . $n . ']',true);
        return $n;
    }
}
