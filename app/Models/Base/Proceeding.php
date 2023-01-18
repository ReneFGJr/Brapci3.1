<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Proceeding extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work';
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

    private function v($id = '')
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $class = $dt['concept']['c_class'];

        $sx .= h($class);

        switch ($class) {
            case 'Subject':
                $Keywords = new \App\Models\Base\Keywords();
                $sx .= $Keywords->showHTML($dt);
                break;

            case 'Proceeding':
                $Work = new \App\Models\Base\Work();
                $sx .= $Work->show($dt);
                break;

            case 'ProceedingSection':
                $ProceedingSection = new \App\Models\Base\ProceedingSection();
                $sx .= $ProceedingSection->show($dt);
                break;

            case 'Work':
                $Work = new \App\Models\Base\Work();
                $sx .= $Work->show($id);
                break;
            default:
                $sx .= h($class, 1);
                $sx = bs(bsc($sx));
                break;
        }
        return $sx;
    }

    function showHTML($dt)
    {
        $Issues = new \App\Models\Base\Issues();
        $dt['header'] = $Issues->header_issue($dt);
        $sx = view('RDF/proceeding', $dt);
    }

    function show_reference($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $sx = $RDF->c($id);
        return $sx;
    }
}