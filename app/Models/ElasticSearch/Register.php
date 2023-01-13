<?php

namespace App\Models\ElasticSearch;

use CodeIgniter\Model;

class Register extends Model
{
    protected $DBGroup          = 'elastic';
    protected $table            = 'dataset';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id','article_id','id_jnl','collection',
        'title','authors', 'keywords','type',
        'fulltext','pdf','updated_at'
    ];

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


    function resume()
        {
            $tot = 0;
            $sx = h(lang('brapci.ElasticSearch'),4);
            $dt = $this
                ->select('count(*) as total, type')
                ->findAll();
            $sx .= '<ul style="font-size: 0.7em;">';
            foreach($dt as $line)
                {
                    $sx .= '<li>'.lang('brapci.'.$line['type']).' ('.$line['total'].')</li>';
                    $tot = $tot + $line['total'];
                }
            $sx .= '</ul>';

        $sx .= '<ul style="font-size: 0.7em;">';

        /***************************************** PDF */
        $dt = $this
            ->select('count(*) as total, pdf')
            ->where('pdf',0)
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.pdf.' . $line['pdf']) . ' (' . $line['total'] . ')</li>';
        }


        /***************************************** KEYWORDS */
        $dt = $this
            ->select('count(*) as total')
            ->where('keywords is NULL')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.keywords_without') . ' (' . $line['total'] . ')</li>';
        }

        /***************************************** ABSTRACT */
        $dt = $this
            ->select('count(*) as total')
            ->where('abstract is NULL')
            ->Orwhere('abstract','')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.abstract_without') . ' (' . $line['total'] . ')</li>';
        }

        /***************************************** YEAR */
        $dt = $this
            ->select('count(*) as total')
            ->where('year is NULL')
            ->Orwhere('year', '')
            ->Orwhere('year < 1950')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.year_without') . ' (' . $line['total'] . ')</li>';
        }

        $sx .= '</ul>';


            return $sx;

        }


    function data($id,$data)
        {
            //pre($data,false);
            $dt = $this->where('article_id',round($id))->findAll();

            if (count($dt) == 0)
                {
                    $this->set($data)->insert();
                    $sx = lang('brapci.inserted');
                } else {
                    $this->set($data)
                        ->where('article_id', $id)
                        ->update();
                    $sx = lang('brapci.updated');
                }
            return $sx;

        }

    function register($id, $type='')
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        //$file = $dir . 'article.json';
        $file = $dir . 'name.json';
        if (file_exists($file)) {
            $API = new \App\Models\ElasticSearch\API();
            $dt = file_get_contents($file);
            $dt = (array)json_decode($dt);
            $dt['id'] = $id;
            $dt['id_jnl'] = 75;
            $rst = $API->call('brapci3.1/' . $type . '/' . $id, 'POST', $dt);
            jslog("Elastic Export: " . $id);
        } else {
            $sx .= "File not found " . $file . '<br>';
        }
        return bs(bsc($sx,12));
    }
}