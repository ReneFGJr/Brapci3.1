<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Authors extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'authors';
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

    function index_auths($auth = array(), $id = '')
    {
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        $file = $dir . 'Authors.json';
        if (file_exists($file)) {
            $dt = file_get_contents($file);
            $dt = json_decode($dt);

            foreach($dt as $id=>$name)
                {
                if (strlen($name) > 0) {
                    $term = trim($name);
                    $term .= ';' . $id;
                    if (isset($auth[$term])) {
                        $auth[$term]++;
                    } else {
                        $auth[$term] = 1;
                    }
                }
            }
        }
        return $auth;
    }

    function search($txt)
        {
            $sx = '';
            if ($txt != '')
                {
                    $RDF = new \App\Models\RDF2\RDF();
                    $RDFclass = new \App\Models\RDF2\RDFclass();
                    $RDFconcept = new \App\Models\RDF2\RDFconcept();
                    $idc = $RDFclass->getClass('Person');

                    $sx = h($txt);
                    $dt = $RDFconcept
                        ->join('brapci_rdf.rdf_literal', 'id_n = cc_pref_term')
                        ->like('n_name',$txt)
                        ->findAll(100);
                    $sx .= $RDFconcept->getlastquery();

                    foreach($dt as $id=>$line)
                        {
                            $link = '<a href="'.PATH.'admin/alias/'.$line['cc_use'].'">';
                            $linka = '</a>';
                            if ($line['id_cc'] == $line['cc_use'])
                                {
                                    $link .= '<b>';
                                    $linka = '</b>'.$linka;
                                }
                            $sx .= '<li>'.$link.$line['n_name'].' ('.$line['id_cc'].'-'.$line['cc_use'].')'.$linka.'</li>';
                        }
                }
            return $sx;
        }

    function form_search()
        {
            $sx = '';
            $sx .= form_open();
            $sx .= form_label('Nome do autor');
            $sx .= form_input('text',get('text'),['class'=>'border border-secondary full']);
            $sx .= form_submit('action','Pesquisar',['class'=>'mt-2']);
            $sx .= form_close();
            return $sx;
        }
}