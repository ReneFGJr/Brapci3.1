<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Keywords extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'keywords';
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
        $sx = view('RDF/subject', $dt);
        return $sx;
    }

    function index_keys($key = array(), $id = '')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        $file = $dir . 'keywords.json';
        if (file_exists($file)) {
            $dt = file_get_contents($file);
            $dt = json_decode($dt);

            for ($r = 0; $r < count($dt); $r++) {
                $tt = (array)$dt[$r];
                if (isset($tt['term'])) {
                    $t = (string)$tt['term'];
                } else {
                    $tt = $dt[$r];
                    $t = trim($tt);
                }

                if (strlen($t) > 0) {
                    $term = strip_tags($t);
                    $id = substr($t, strpos($t, '/v/') + 2, strlen($t));
                    $id = sonumero(substr($id, 0, strpos($id, '"')));
                    $term .= ';' . $id;
                    if (isset($key[$term])) {
                        $key[$term]++;
                    } else {
                        $key[$term] = 1;
                    }
                }
            }
        }
        return $key;
    }
}