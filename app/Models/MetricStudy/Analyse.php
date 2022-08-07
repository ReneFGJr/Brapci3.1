<?php

namespace App\Models\MetricStudy;

use CodeIgniter\Model;

class Analyse extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'analyses';
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

    function index()
    {
        $Keywords = new \App\Models\Base\Keywords();
        $Authors = new \App\Models\Base\Authors();
        $Sections = new \App\Models\Base\Sections();
        $Indexshow = new \App\Models\Base\IndexShow();
        $Work = new \App\Models\Base\Work();

        $sel = $Work->getWorkMark();
        $dd['works'] = count($sel);

        /* Index */
        $auth = array();
        $keys = array();
        $sect = array();


        foreach ($sel as $id => $selected) {
            if ($selected == 1) {
                if (substr($id, 0, 1) == 'w') {
                    $id = round(substr($id, 1, 20));
                    $keys = $Keywords->index_keys($keys, $id);
                    $auth = $Authors->index_auths($auth, $id);
                    $sect = $Sections->index_sections($sect, $id);
                }
            }
        }

        /********************************************************** TYPE */
        /******************************************************* AUTHORS */
        /******************************************************* SOURCES */
        /******************************************************* SUBJECT */
        $dd['authors_total'] = count($auth);
        $dd['keys_total'] = count($keys);
        $dd['sections_total'] = count($sect);
        $sx = view('MetricStudy/DrashBoard', $dd);

        return $sx;
    }

    function a()
    {

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sx .= '<p>' . $Work->show_reference($id) . '</p>';

            $keys = $Keywords->index_keys($keys, $id);
            $auth = $Authors->index_auths($auth, $id);
            $sect = $Sections->index_sections($sect, $id);
        }
        $key_index = $Indexshow->show_index($auth, 'authors');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($sect, 'sections');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($keys, 'keyword');
    }
}
