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
        'year', 'ldl_journal', 'ldl_lang',
        'title','authors', 'keywords','type', 'abstract',
        'fulltext', 'pdf','updated_at', 'section',
        'ldl_title', 'ldl_legend', 'ldl_authors','ldl_section'
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
            $limit = 100;
            $offset = get('offset');
            if ($offset == '')
                {
                    $offset = 0;
                }
            $dtt = $this->countAllResults();

            $dta = $this->FindAll($limit, $offset);

            $type = 'prod';

            $API = new \App\Models\ElasticSearch\API();
            $sx = 'Export ElasticSearch v2.0 - ';
            $sx .= $offset.' of '.$dtt;
            if ($dtt > 0)
                {
                    $percent = ($offset / $dtt * 100);
                } else {
                    $percent = 100;
                }

            $sx .= ' ('.number_format($percent,1).'%)';
            $sx .= '<hr>';

            foreach($dta as $id=>$line)
                {
                    $dt = json_encode($line);
                    $dt = $line;
                    $dt['id'] = $line['article_id'];
                    $dt['full'] = $line['title'] . ' ' . $line['abstract'] . ' ' . $line['keywords'] . ' ' . $line['authors'];
                    $dt['full'] = strip_tags($dt['full']);
                    $id = $dt['id'];
                    $rst = $API->call('brapci3.1/'.$type.'/'. $id, 'POST', $dt);
                    $API->server = 'http://143.54.112.91:9200/';
                    $sx .= $id .= ' => '.$rst['result'].' v.'.$rst['_version'].' ('.$line['collection'].')<br>';

                }
            if (count($dta) == $limit)
                {
                    $sx .= metarefresh(PATH. '/elasticsearch/update_index?offset='.($offset+$limit),1);
                } else {
                    $sx = bsmessage('Elastic Search Exported',1);
                }
            $sx = bs(bsc($sx,12));
            return $sx;
        }

    function set_status($id,$dta)
        {
            $dt = $this->where('article_id',$id)->first();
            if ($dt != '')
                {
                    $this->set($dta)->where('article_id', $id)->update();
                }
        }

    function xx___register($id, $type = '')
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
            if (is_array($data['id_jnl']))
                {
                    $da['id_jnl'] = $data['id_jnl'][0];
                } else {
                    $da['id_jnl'] = $data['id_jnl'];
                }
        } else {
            return([]);
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
                        case 'Person':
                            $da['collection'] = 'AU';
                            $da['type'] = $data['Class'];
                            break;
                        case 'CorporateBody':
                            $da['collection'] = 'AC';
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

        if (isset($data['Authors'])) {
            $da['authors'] = '';
            foreach ($data['Authors'] as $id => $title) {
                $title = ' ' . strtolower(ascii($title));
                $title = troca($title, ';', ' ');
                $da['authors'] .= $title;
            }
        }

        if (isset($data['prefLabel'])) {
                $da['title'] = mb_strtolower(ascii($data['prefLabel']));
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

        if (!isset($da['year'])) { $da['year'] = 1900; }
        if ($da['year']=='') { $da['year'] = 1900; }

        if (isset($data['PDF'])) { $da['pdf'] = 1; }

        if (isset($data['difusion']['LDL_title'])) {
            $da['ldl_title'] = $data['difusion']['LDL_title'];
        }
        if (isset($data['difusion']['LDL_lang'])) {
            $da['ldl_lang'] = $data['difusion']['LDL_lang'];
        }
        if (isset($data['difusion']['LDL_author'])) {
            $da['ldl_authors'] = $data['difusion']['LDL_author'];
        }
        if (isset($data['difusion']['LDL_legend'])) {
            $da['ldl_legend'] = $data['difusion']['LDL_legend'];
        }
        if (isset($data['difusion']['LFL_section'])) {
            $da['ldl_section'] = $data['difusion']['LFL_section'];
        }
        if (isset($data['difusion']['LDL_journal'])) {
            $da['ldl_journal'] = $data['difusion']['LDL_journal'];
        }

        $da['updated_at'] = date("Y-m-d H:i:s");
        return $da;
    }


    function data($id,$xdata)
        {
            $dt = $this->where('article_id',round($id))->findAll();
            if (count($xdata) == 0)
                {
                    echo '======================== A001 ==';
                    pre($xdata);
                    $sx = lang('brapci.skip').' deleted';
                    return $sx;
                }

            $data = $this->data_convert_elastic($xdata);
            if (count($dt) == 0)
                {
                    if (count($data) > 0)
                        {
                            $this->set($data)->insert();
                            $sx = lang('brapci.inserted');
                        } else {
                            $sx = lang('brapci.deleted');
                        }
                } else {
                    if (count($data) > 0) {
                        $this->set($data)
                            ->where('article_id', $id)
                            ->update();
                        $sx = lang('brapci.updated');
                    } else {
                        $this->where('article_id', $id)->delete();
                        $sx = lang('brapci.deleted');
                    }
                }
            return $sx;

        }


}