<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class V extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'vs';
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
            $sx = '';
            $RDF = new \App\Models\Rdf\RDF();

            if (!is_array($dt)) {
                $dt = round($dt);
                $dt = $RDF->le($dt);
            }

            $idc = $dt['concept']['id_cc'];
            $class = $dt['concept']['c_class'];
            $mod = COLLECTION;

            switch($class)
                {
                    case 'Journal':
                        if ($mod != '')
                            {
                                $sx = metarefresh(PATH.'/v/'.$idc);
                                return $sx;
                            }
                        $Journals = new \App\Models\Base\Journals();
                        $sx .= $Journals->v($dt);
                        break;
                }
            return $sx;
        }
}
