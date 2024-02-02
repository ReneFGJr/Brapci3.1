<?php

namespace App\Models\Dataverse\API;

use CodeIgniter\Model;

class Migration extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'migrations';
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

    //https://guides.dataverse.org/en/latest/developers/dataset-migration-api.html#dataset-migration-api

    // Publication
    // curl -H 'Content-Type: application/ld+json' -H X-Dataverse-key:$API_TOKEN -X POST -d '{"schema:datePublished": "2020-10-26","@context":{ "schema":"http://schema.org/"}}' "$SERVER_URL/api/datasets/{id}/actions/:releasemigrated"
}
