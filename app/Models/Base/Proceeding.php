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

    function showHTML($dt)
    {
        $Issues = new \App\Models\Base\Issues();
        $dt['header'] = $Issues->header_issue($dt);
        $sx = view('RDF/proceeding', $dt);
    }

    function show($dt)
    {
        $sx = '';
        $da = array();
        $RDF = new \App\Models\Rdf\RDF();
        $dd = $dt['data'];

        $dc = $dt['concept'];
        $idc = $dc['id_cc'];
        $class = $dc['c_class'];

        $da['authors'] = array();
        $da['keywords'] = array();

        for ($r = 0; $r < count($dd); $r++) {
            $line = $dd[$r];
            $lang = $line['n_lang'];
            $lang2 = $line['n_lang2'];
            $class = $line['c_class'];
            switch ($class) {
                case 'hasIssueProceedingOf':
                    //pre($line);
                    break;
                case 'hasSectionOf':
                    if (isset($da['Section'][$lang]))
                    {
                        $da['Section'][$lang] .= ' - '.$line['n_name2'];
                    } else {
                        $da['Section'][$lang] = $line['n_name2'];
                    }                    
                    break;
                case 'hasFileStorage':
                    $da['PDF'] = $line['n_name2'];
                    break;
                case 'hasTitle':
                    $da['Title'][$lang] = '<p class="abstract">'.$line['n_name'].'</p>';
                    break;
                case 'hasAbstract':
                    $da['Abstract'][$lang] = '<p class="abstract">'.$line['n_name'].'</p>';
                    break;
                case 'hasAuthor':
                    $name = '<a href="'.URL.COLLECTION.'/v/'.$line['d_r2'].'">'.$line['n_name2'].'</a>';
                    array_push($da['authors'],$name);
                    break;
                case 'hasSubject':
                    $name = '<a href="'.URL.COLLECTION.'/v/'.$line['d_r2'].'">'.$line['n_name2'].'</a>';
                    if (!isset($da['keywords'][$lang2])) 
                        { $da['keywords'][$lang2] = array(); }
                    array_push($da['keywords'][$lang2],$name);
                    break;
                default:
                    echo '<br>==>' . $class;
                    break;
            }
        }
        $sx = view('Benancib/Base/Work',$da);
        return $sx;
    }

    function show_reference($id)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $sx = $RDF->c($id);
        return $sx;
    }
}
