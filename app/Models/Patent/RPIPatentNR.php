<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIPatentNR extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_patent_nr';
    protected $primaryKey       = 'id_p';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_p', 'p_nr', 'p_use'
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

    function register($pat_nr)
    {
        $dt = $this->where('p_nr', $pat_nr)->findAll();
        if (count($dt) > 0) {
            return $dt[0]['id_p'];
        } else {
            $data['p_nr'] = $pat_nr;
            $data['p_use'] = 0;
            return $this->insert($data);
        }
    }

    function ai_patent_number($nr,$data)
        {
            $sx = '';
            $in = substr($nr,0,2);
            $size = strlen($nr);
            switch($in)
                {
                    case 'BR':
                        $nr = substr($nr,2,$size);
                        $nr = 'BR'.$nr;
                        break;
                    case 'PI':
                        $nr = substr($nr,2,$size);
                        $nr = 'PI'.$nr;
                        break;
                    default:
                        $sx .= 'Not rule for '.$nr;
                        break;
                }
            return $sx;
        }
}
