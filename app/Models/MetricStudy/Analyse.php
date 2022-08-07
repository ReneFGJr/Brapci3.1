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
        $Work = new \App\Models\Base\Work();
        $sel = $Work->getWorkMark();
        $dd['works'] = count($sel);

        /********************************************************** TYPE */
        /******************************************************* AUTHORS */
        /******************************************************* SOURCES */
        /******************************************************* SUBJECT */

        $sx = view('MetricStudy/DrashBoard', $dd);
        return $sx;
    }

    function a()
        {
        $Keywords = new \App\Models\Base\Keywords();
        $Authors = new \App\Models\Base\Authors();
        $Sections = new \App\Models\Base\Sections();
        $Indexshow = new \App\Models\Base\IndexShow();

        $dt = $this->issueWorks($id_rdf);
        $sx = '';

        /******************************* */
        /* Index */
        $auth = array();
        $keys = array();
        $sect = array();

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $sx .= '<p>' . $Work->show_reference($line['siw_work_rdf']) . '</p>';

            $keys = $Keywords->index_keys($keys, $line['siw_work_rdf']);
            $auth = $Authors->index_auths($auth, $line['siw_work_rdf']);
            $sect = $Sections->index_sections($sect, $line['siw_work_rdf']);
        }
        $key_index = $Indexshow->show_index($auth, 'authors');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($sect,'sections');
        $key_index .= '<br>';
        $key_index .= $Indexshow->show_index($keys,'keyword');
        }
}
