<?php

namespace App\Models\Guide\Software;

use CodeIgniter\Model;

class SoftwareSteps extends Model
{
    protected $DBGroup          = 'software';
    protected $table            = 'software_steps';
    protected $primaryKey       = 'id_st';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_st ',
        'st_software',
        'st_order',
        'st_description',
        'st_answer',
        'st_code',
        'st_so',
        'st_created_at'
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

    function viewSteps($id = '')
    {
        $d = $this->select('id_st, st_software, st_user, st_order, st_description, st_answer, st_code, st_so, st_created_at');
        $d->where('st_software', $id);
        $d->orderBy('st_order', 'ASC');
        $dt = [];
        $dt['steps'] = $this->findAll();
        $dt['softwareName'] = 'Software';
        $sx = view('Software/SoftwareSteps', $dt);
        return $sx;
    }
}
