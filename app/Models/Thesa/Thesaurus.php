<?php

namespace App\Models\Thesa;

use CodeIgniter\Model;

class Thesaurus extends Model
{
    protected $DBGroup          = 'thesa';
    protected $table            = 'thesa';
    protected $primaryKey       = 'id_th';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_th', 'th_name', 'th_achronic',
        'th_description', 'th_status', 'th_terms',
        'th_version', 'th_icone', 'th_icone_custom',
        'th_cover', 'th_type', 'th_own'
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

    function getID($id=0)
        {
        $cp = 'id_th as ID, th_name as Thesauros, th_achronic as acronic, th_status as status, ';
        $cp .= 'th_icone_custom as icon, th_cover as cover, ';
        $cp .=  'th_description as description';
        $dt = $this
            ->select($cp)
            ->where('id_th',$id)
            ->first();
        return $dt;
        }

    function list($own = 0)
    {
        $cp = 'id_th as ID, th_name as Thesauros, th_achronic as acronic, th_status as status, th_icone_custom as icon, th_cover as cover, th_type as Type';
        $cpO = 'id_th, th_name, th_achronic, th_status, th_icone_custom, th_cover, th_type';
        $dt = $this
            ->select($cp)
            ->join('thesa_users', 'th_us_th = id_th','left')
            ->groupBy($cpO)
            ->findAll();
        return $dt;
    }
}
