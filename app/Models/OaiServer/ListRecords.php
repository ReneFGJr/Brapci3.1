<?php

namespace App\Models\OaiServer;

use CodeIgniter\Model;

class ListRecords extends Model
{
    protected $DBGroup          = 'oaiserver';
    protected $table            = 'ListRecords';
    protected $primaryKey       = 'id_rec';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rec_setSpec',
        'rdf_identifier',
        'rec_source',
        'rec_data',
        'red_deleted'
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

    function list($id)
        {
            $dt = $this
                ->join('brapci_oaipmh_editor.ListSets', 'rec_setSpec = id_ss')
                ->where('rec_source',$id)
                ->findAll();
            return $dt;
        }
}
