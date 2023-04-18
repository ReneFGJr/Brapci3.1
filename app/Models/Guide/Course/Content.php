<?php

namespace App\Models\Guide\Course;

use CodeIgniter\Model;

class Content extends Model
{
    protected $DBGroup          = 'guide';
    protected $table            = 'curso_content';
    protected $primaryKey       = 'id_ct';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ct', 'ct_course', 'ct_module',
        'ct_name', 'ct_text', 'ct_plano',
        'ct_descricao', 'ct_time'
    ];
    protected $typeFields    = [
        'hidden', 'sql:id_c:c_nome:curso', 'sql:id_cm:cm_name:curso_module',
        'string', 'text', 'ql:id_pl:pl_nome:plano',
        'text', 'string'
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

    var $path = '';
    var $id = '';

    function edit($id=0)
        {
            $this->id=$id;
            $this->path = PATH. '/popup/content/'.$id;
            $sx = form($this);
            return $sx;
        }
}
