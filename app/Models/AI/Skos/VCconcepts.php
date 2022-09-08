<?php

namespace App\Models\AI\Skos;

use CodeIgniter\Model;

class VCconcepts extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'brapci_chatbot.vc_concepts';
    protected $primaryKey           = 'id_vc';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [
        'id_c', 'c_name', 'c_th', 'c_id'
    ];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    function directory($term = '', $create = false)
    {
        $chars = array('!', '?', '.', ',', ';', '-', ':', '(', ')', '[', ']', '{', '}', '"', '\'', '/', '\\', '|','+', '=', '*', '&', '^', '%', '$', '#', '@', '~', '`', '<', '>', ',');
        for ($r = 0; $r < count($chars); $r++) {
            $term = troca($term, $chars[$r], '_');
        }
        while(strpos($term,'__') > 0)
            {
                $term = troca($term,'__','_');
            }

        /******************************** DEFAULT DIRECTORY */
        $dir = '../.tmp/';
        if ($create == true) {
            dircheck($dir);
        }
        $dir .= 'skosConcept/';
        if ($create == true) {
            dircheck($dir);
        }
        /******************************* WORD */
        $term = substr($term,0,1).'_'.$term;
        $w = explode('_', $term);
        for ($i = 0; $i < count($w); $i++) {
            $dir .= $w[$i] . '/';
            if ($create == true) {
                dircheck($dir);
            }
        }
        return $dir;
    }
    function export_all($id)
    {
        $VCterms = new \App\Models\AI\Skos\VCterms();
        $dt = $this
            ->join('brapci_chatbot.vc_concepts_th', 'cth_c = id_c')
            ->join('brapci_chatbot.skos', 'cth_th = id_sk')
            ->where('cth_th', $id)
            ->FindAll();

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $term = trim($line['c_name']);
            $id_th = $line['cth_th'];
            $uri = $line['cth_uri'];

            $this->term($term, $id_th, $uri);
            $dir = $this->directory($term, true);

            $class['class'] = $line['sk_name'];
            $class['uri'] = $line['cth_uri'];
            $class['label'] = troca($line['c_name'],'_',' ');
            $class['id'] = $line['id_c'];
            $class['th'] = $line['cth_th'];
            file_put_contents($dir . 'class.json', json_encode($class));
        }
        return 'Exported';
    }


    function term($term,$th,$uri)
    {
        $VCconcepts_th = new \App\Models\AI\Skos\VCconcepts_th();
        $dt = $this->where('c_name', $term)->findAll();
        if (count($dt) == 0) {
            $dd['c_name'] = $term;
            $idt = $this->insert($dd);
        } else {
            $idt = $dt[0]['id_c'];
        }

        $VCconcepts_th->link($idt, $th, $uri);
    }
}
