<?php

namespace App\Models\Dataverse;

use CodeIgniter\Model;

class Embargo extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'embargos';
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

    /****************************
        export API_TOKEN=48b5d6f4-17bd-4ade-a94d-2ce36abaeab0
        export SERVER_URL=https://dadosdepesquisa.fiocruz.br/
        export PERSISTENT_IDENTIFIER=doi:10.35078/OUDIKC
        export JSON='{"fileIds":[201931]}'
        curl -H "X-Dataverse-key: $API_TOKEN" -H "Content-Type:application/json" "$SERVER_URL/api/dat>
     */
}
