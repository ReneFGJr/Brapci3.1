<?php

namespace App\Models\AI\Skos;

use CodeIgniter\Model;

class VCconcepts extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'brapci_chatbot.vc_concepts';
    protected $primaryKey           = 'id_vc';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [
        'id_c', 'c_name','c_th','c_id'
    ];

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

    function term($term)
    {
        $VCconcepts_th = new \App\Models\AI\Skos\VCconcepts_th();
        $dt = $this->where('c_name', $term)->findAll();
        if (count($dt) == 0)
            {
                $dd['c_name'] = $term;
                $idt = $this->insert($dd);
            } else {
                $idt = $dt[0]['id_c'];
            }
        $uri = '';
        $VCconcepts_th->link($idt,$term,$uri);
    }
}
