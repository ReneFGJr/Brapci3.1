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
        'id_ds', 'ID', 'json','CLASS',
        'JOURNAL','ISSUE', 'YEAR','KEYWORD','ABSTRACT',
        'PDF', 'updated_at'
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
            $limit = 500;
            $offset = get('offset');
            if ($offset == '')
                {
                    $offset = 0;
                }
            $dtt = $this->countAllResults();

            $dta = $this->FindAll($limit, $offset);

            $type = 'prod';

            $API = new \App\Models\ElasticSearch\API();
            $sx = 'Export ElasticSearch v2.1 - ';
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
                    $dt['id'] = $line['ID'];
                    $dt['full'] = $line['title'] . ' ' . $line['ABSTRACT'] . ' ' . $line['KEYWORD'] . ' ' . $line['authors'];
                    $dt['full'] = strip_tags($dt['full']);
                    $id = $dt['id'];
                    $rst = $API->call('brapci3.2/'.$type.'/'. $id, 'POST', $dt);

                    /* Second Server */
                    $API->server = 'http://143.54.112.91:9200/';
                    $rst = $API->call('brapci3.2/' . $type . '/' . $id, 'POST', $dt);

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
            $dt = $this->where('ID',$id)->first();
            if ($dt != '')
                {
                    $this->set($dta)->where('ID', $id)->update();
                }
        }


    function resume()
        {
            $tot = 0;
            $sx = h(lang('brapci.ElasticSearch'),4);
            $sa = '';

            $dt = $this
                ->select('count(*) as total, CLASS')
                ->groupBy('CLASS')
                ->findAll();

            $sa .= '<ul style="font-size: 0.7em;">';
            foreach($dt as $line)
                {
                    $sa .= '<li>'.lang('brapci.'.$line['CLASS']).' ('. number_format($line['total'], 0, ',', '.').')</li>';
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
            ->select('count(*) as total, KEYWORD')
            ->where('KEYWORD is NULL')
            ->groupBy('KEYWORD')
            ->findAll();

        foreach ($dt as $line) {
            $sx .= '<li>' . lang('brapci.keywords_without') . ' (' . number_format($line['total'], 0, ',', '.') . ')</li>';
        }

        /***************************************** ABSTRACT */
        $dt = $this
            ->select('count(*) as total, ABSTRACT')
            ->where('ABSTRACT is NULL')
            ->Orwhere('ABSTRACT','')
            ->groupBy('ABSTRACT')
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

        $da['ID'] = $data['ID'];
        $da['json'] = json_encode($data);
        $da['CLASS'] = $data['Class'];

        if (isset($data['Issue']['ID'])) {
            $da['ISSUE'] = $data['Issue']['ID'];
        }

        if ((isset($data['YEAR'])) and ($data['YEAR'] != ''))
            {
                $da['YEAR'] = $data['YEAR'];
            }

        if (!isset($da['YEAR'])) { $da['YEAR'] = 9998; }
        if ($da['YEAR']=='') { $da['year'] = 9997; }

        if (isset($data['PDF'])) { $da['PDF'] = $data['PDF']; }
        else {
            $data['PDF'] = 0;
        }

        if (isset($data['id_jnl'])) {
            $da['JOURNAL'] = $data['id_jnl'];
        }

        $da['updated_at'] = date("Y-m-d H:i:s");
        return $da;
    }

    function check($dt,$stop)
        {
            $sx = '';
            switch($dt['CLASS'])
                {
                    case 'Article':
                        $sx .= $this->checkIssue($dt);
                        $sx .= $this->checkYear($dt);
                }
            if (($stop == True) and ($sx != ''))
                {
                    echo h("ERROS",1);
                    echo $sx;
                    exit;
                }
        }
    function checkYear($dt)
    {
        if (!isset($dt['YEAR'])) {
            return "YEAR not set<br>";
        } else {
            return "";
        }
    }
    function checkIssue($dt)
        {
            if (!isset($dt['ISSUE']))
                {
                    return "ISSUE not set<br>";
                } else {
                    return "";
                }
        }


    function data($id,$xdata)
        {
            $dt = $this->where('ID',round($id))->findAll();
            if (count($xdata) == 0)
                {
                    echo '======================== A001 ==';
                    pre($xdata);
                    $sx = lang('brapci.skip').' deleted';
                    return $sx;
                }

            /*********************** CONVERT DADOS */
            $data = $this->data_convert_elastic($xdata);
            $this->check($data,true);

            /* NOVO REGISTRO */
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
                            ->where('ID', $id)
                            ->update();
                        $sx = lang('brapci.updated');
                    } else {
                        $this->where('ID', $id)->delete();
                        $sx = lang('brapci.deleted');
                    }
                }
            return $sx;

        }


}