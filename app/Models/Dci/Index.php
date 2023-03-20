<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'persistent_indicador';
    protected $table            = 'persistent_id';
    protected $primaryKey       = 'id_pi';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pi ', 'pi_id', 'pi_url',
        'pi_json', 'pi_active', 'pi_status',
        'pi_citation', 'pi_creators', 'pi_title',
        'updated_at'
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

    function index($d1='',$d2='',$d3='',$d4='')
        {
            $sx = '';
            $Docentes = new \App\Models\Dci\Docentes;
            $sx .= $Docentes->index($d2,$d3,$d4);
            return $sx;
        }
}
