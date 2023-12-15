<?php

namespace App\Models\AI;

use CodeIgniter\Model;

class Forms extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'forms';
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

    function textarea($url='',$sp='')
        {
            $sx = '';
            $sx .= form_open();
            if ($sp == '')
                {
                    $sx .= form_textarea(array('name' => 'text', 'id' => 'text', 'class' => 'form-control border border-secondary', 'rows' => 10, 'value' => get("text")));
                    $sx .= form_submit(array('name' => 'submit', 'id' => 'submit', 'class' => 'btn btn-primary'), lang('tools.Process'));
                } else {
                    $sa = '';
                    $sa .= form_textarea(array('name' => 'text', 'id' => 'text', 'class' => 'form-control border border-secondary', 'rows' => 10, 'value' => get("text")));
                    $sa .= form_submit(array('name' => 'submit', 'id' => 'submit', 'class' => 'btn btn-primary'), lang('tools.Process'));
                    $sx .= bsc($sa,8).bsc($sp,4);
                }
            $sx .= form_close();
            return $sx;
        }
}
