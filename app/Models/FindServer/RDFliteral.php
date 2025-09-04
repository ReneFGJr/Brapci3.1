<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class RDFliteral extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'rdfliterals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_n','n_name','n_lang','n_lock','n_delete'
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

    function getLiteral($name, $lang = 'pt_BR', $create = false)
    {
        $r = $this
            ->where('n_name', $name)
            ->where('n_lang', $lang)
            ->first();
        if (!isset($r['id']) && ($create == true)) {
            $data = [
                'n_name' => $name,
                'n_lang'   => $lang,
                'n_lock'   => '0',
                'n_delete' => '0'

            ];
            $this->insert($data);
            $r = $this
                ->where('literal', $name)
                ->where('lang', $lang)
                ->first();
        }
        return $r;
    }
}
