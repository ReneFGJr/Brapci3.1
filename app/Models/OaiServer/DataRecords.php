<?php

namespace App\Models\OaiServer;

use CodeIgniter\Model;

class DataRecords extends Model
{
    protected $DBGroup          = 'oaiserver';
    protected $table            = 'Records_data';
    protected $primaryKey       = 'id_r ';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_r',
        'r_record',
        'r_metadata',
        'r_lang',
        'r_content'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function le($id)
        {
            $dt = $this
                ->where('id_r',$id)
                ->first($id);
            return $dt;
        }

    function register($id,$dt)
        {
            $this->set($dt)->where('id_r',$id)->update();
            return [];
        }

    function list($id)
        {
            $dt =
                $this
                ->join('brapci_oaipmh_editor.metadata', 'r_metadata = id_mt')
                ->where('r_record',$id)
                ->findAll();
            return $dt;
        }
}
