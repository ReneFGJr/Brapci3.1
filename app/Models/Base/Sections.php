<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Sections extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sections';
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

    function showHTML($dt)
    {
        $sx = view('RDF/sections', $dt);
        return $sx;
    }

    function normalize($sec,$idj)
        {
            //echo h($sec.'=='.$idj);
            switch($idj)
                {
                    case 75:
                        $sec = explode(':',trim($sec));
                        $sec = $sec[count($sec)-1];
                        return $sec;
                    default:
                        return $sec;
                        break;
                }
            exit;
        }


    function index_sections($sect = array(), $id = '')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        $file = $dir . 'Sections.json';
        if (file_exists($file)) {
            $dt = file_get_contents($file);
            $dt = json_decode($dt);

            foreach ($dt as $id => $term) {
                if (strlen($term) > 0) {
                    if (isset($sect[$term])) {
                        $sect[$term]++;
                    } else {
                        $sect[$term] = 1;
                    }
                }
            }
        }
        return $sect;
    }
}