<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Journals extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'journals';
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

    function v($dt)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $Cover = new \App\Models\Base\Cover();
        $Source = new \App\Models\Base\Sources();
        $Metadata = new \App\Models\Base\Metadata();
        if (!is_array($dt)) {
            $dt = round($dt);
            $dt = $RDF->le($dt);
        }

        $mt = $Metadata->metadata($dt,true);
        $mt['source'] = $Source->where('jnl_frbr',$mt['ID'])->first();
        $mt['cover'] = $Cover->image($mt['source']['id_jnl']);
        //pre($mt,false);
        $sx = view('Brapci/Base/Journal',$mt);
        return $sx;
    }
}
