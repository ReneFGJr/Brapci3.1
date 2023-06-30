<?php

namespace App\Models\Find\Books\Db;

use CodeIgniter\Model;

class Item extends Model
{
    protected $DBGroup          = 'find';
    protected $table            = 'book_library';
    protected $primaryKey       = 'id_bl';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bl', 'bl_library', 'bl_item',
        'bl_tombo', 'bl_catalogador', 'bl_status',
        'bl_emprestimo', 'bl_renovacao', 'bl_usuario'
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

    function register($dd)
        {
            $tb = $this
                ->select('max(bl_tombo) as bl_tombo')
                ->where('bl_library',$dd['library'])
                ->first();
            $tombo = $tb['bl_tombo'];
            if ($tombo == '')
                {
                    $tombo = 0;
                }
            $dt = [];
            $dt['bl_tombo'] = ($tombo+1);
            $dt['bl_status'] = 1;
            $dt['bl_catalogador'] = $dd['user'];
            $dt['bl_item'] = $dd['expressao'];
            $dt['bl_usuario'] = 0;
            $dt['bl_library'] = $dd['library'];
            $id = $this->set($dt)->insert();

            return $id;
        }
}
