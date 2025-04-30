<?php

namespace App\Models\CDU;

use CodeIgniter\Model;

class Avaliation extends Model
{
    protected $DBGroup          = 'CDU';
    protected $table            = 'avaliations';
    protected $primaryKey       = 'id_av';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_av',
        'av_student',
        'av_q1',
        'av_q2',
        'av_q3',
        'av_q4',
        'av_q5',
        'av_q6',
        'av_q7',
        'av_q8',
        'av_q9',
        'av_q10',
        'av_q11',
        'av_q12',
        'av_q13',
        'av_q14',
        'av_q15',
        'av_q16'
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
}
