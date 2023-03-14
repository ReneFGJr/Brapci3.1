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
        'id','article_id','id_jnl','collection','year',
        'title','authors', 'keywords','type', 'abstract',
        'fulltext', 'pdf','updated_at', 'section'
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

    function update_index()
        {
            $dt = $this->FindAll();
            $type = 'prod';
            $dir = '../.tmp/';
            dircheck($dir);
            $file = $dir . 'metadata.json';

            $API = new \App\Models\ElasticSearch\API();
            $sx = '';
            foreach($dt as $id=>$line)
                {
                    $dt = json_encode($line);
                    $dt = $line;
                    $dt['id'] = $line['article_id'];
                    $id = $dt['id'];
                    $rst = $API->call('brapci3.1/'.$type.'/'. $id, 'POST', $dt);
                    $sx .= $id .= ' => '.$rst['result'].'<br>';
                }
            return $sx;
        }

    function register($id, $type = '')
    {
        $sx = '';
        $RDF = new \App\Models\Rdf\RDF();
        $dir = $RDF->directory($id);
        //$file = $dir . 'article.json';
        $file = $dir . 'metadata.json';
        if (file_exists($file)) {
            $API = new \App\Models\ElasticSearch\API();
            $dt = file_get_contents($file);
            $dt = (array)json_decode($dt);
            $dt['id'] = $id;
            $sx .= 'URL: ' . 'brapci3.1/' . $type . '/' . $id;
            $rst = $API->call('brapci3.1/' . $type . '/' . $id, 'POST', $dt);
        } else {
            $sx .= "File not found " . $file . '<br>';
        }
        return bs(bsc($sx, 12));
    }


    function resume()
        {
            $tot = 0;
            $sx = h(lang('brapci.ElasticSearch'),4);
            $sa = '';

            $dt = $this
                ->select('count(*) as total, type')
                ->groupBy('type')
                ->findAll();

            $sa .= '<ul style="font-size: 0.7em;">';
            foreach($dt as $line)
                {
                    $sa .= '<li>'.lang('brapci.'.$line['type']).' ('. number_format($line['total'], 0, ',', '.').')</li>';
                    $tot = $tot + $line['total'];
                }
            $sa .= '</ul>';
            /********* Total */
            $sx .= '<b style="font-size: 0.7em;">Total '. number_format($tot, 0, ',', '.').'</b>';
            /********* Result (alterar ordem) */
            $sx .= $sa;

        $sx .= '<ul style="font-size: 0.7em;">';

        /***************************************** PDF */
        $dt = $this
            ->select('count(*) as total, pdf')
            ->where('pdf',0)
            ->groupBy('pdf')
            ->findAll();

        foreach ($dt as $line) {
            $link = '<a href="'.PATH.'/admin/dataset/erros/pdf'.'">';
            $linka = '</a>';
            $sx .= '<li>' . $link.lang('brapci.pdf.' . $line['pdf']) . $linka.' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }


        /***************************************** KEYWORDS */
        $dt = $this
            ->select('count(*) as total, keywords')
            ->where('keywords is NULL')
            ->groupBy('keywords')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.keywords_without') . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }

        /***************************************** ABSTRACT */
        $dt = $this
            ->select('count(*) as total, abstract')
            ->where('abstract is NULL')
            ->Orwhere('abstract','')
            ->groupBy('abstract')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.abstract_without') . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }

        /***************************************** YEAR */
        $dt = $this
            ->select('count(*) as total')
            ->where('year is NULL')
            ->Orwhere('year', '')
            ->Orwhere('year < 1950')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.year_without') . ' (' . number_format($line['total'],0,',','.') . ')</li>';
        }

        $sx .= '</ul>';


            return $sx;

        }

    function data_convert_elastic($data)
        {
            $da = array();
            if (isset($data['Title']))
                {
                    $da['title'] = '';
                    foreach($data['Title'] as $id=>$title)
                        {
                            $title = ' '.strtolower(ascii($title));
                            $da['title'] .= $title;
                        }
                }

            if (isset($data['ID'])) { $da['article_id'] = $data['ID']; }

        if (isset($data['id_jnl'])) {
            $da['id_jnl'] = $data['id_jnl'][0];
        }

        if (isset($data['Class']))
            {
                switch($data['Class'])
                    {
                        case 'Proceeding':
                            $da['collection'] = 'EV';
                            $da['type'] = $data['Class'];
                            break;
                        case 'Article':
                            $da['collection'] = 'AR';
                            $da['type'] = $data['Class'];
                            break;
                        case 'Book':
                            $da['collection'] = 'BK';
                            $da['type'] = $data['Class'];
                            break;
                        case 'BookChapter':
                            $da['collection'] = 'BK';
                            $da['type'] = $data['Class'];
                            break;
                        default:
                            echo "OPS REGISTER NOT EXISTE ".$data['Class'];
                            exit;
                    }
            }

        if (isset($data['Sections'])) {
            $da['section'] = '';
            foreach ($data['Sections'] as $id => $title) {
                $title = ' ' . strtolower(ascii($title));
                $title = troca($title,';',' ');
                $da['section'] .= $title;
            }
        }

        if (isset($data['Authors'])) {
            $da['authors'] = '';
            foreach ($data['Authors'] as $id => $title) {
                $title = ' ' . strtolower(ascii($title));
                $title = troca($title, ';', ' ');
                $da['authors'] .= $title;
            }
        }

        if (isset($data['Abstract'])) {
            $da['abstract'] = '';
            foreach ($data['Abstract'] as $id => $title) {
                    $title = ' ' . strtolower(ascii($title));
                    $title = troca($title, ';', ' ');
                    $da['abstract'] .= $title;
            }
        }

        if (isset($data['Keywords'])) {
            $da['keywords'] = '';
            foreach ($data['Keywords'] as $title => $lang) {
                $title = ' ' . strtolower(ascii($title));
                $title = troca($title, ';', ' ');
                $da['keywords'] .= $title;
            }
        }

        if (isset($data['Fulltext']))
            {

            }

        if ((isset($data['Year'])) and ($data['Year'] != ''))
            {
                $da['year'] = $data['Year'];
            }

        if ((isset($data['Issue']['Year'])) and ($data['Issue']['Year'] != '')) {
            $da['year'] = $data['Issue']['Year'];
        }

        if (!isset($da['year'])) { $da['year'] = '????'; }

        if (isset($data['PDF'])) { $da['pdf'] = 1; }

        $da['updated_at'] = date("Y-m-d H:i:s");
        return $da;
    }


    function data($id,$data)
        {
            $dt = $this->where('article_id',round($id))->findAll();
            $data = $this->data_convert_elastic($data);

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


}