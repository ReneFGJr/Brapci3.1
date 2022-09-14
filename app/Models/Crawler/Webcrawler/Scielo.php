<?php

namespace App\Models\Crawler\Webcrawler;

use CodeIgniter\Model;

class Scielo extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'scielos';
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

    function hasDOI($id)
    {
        $sx = '';
        $CTASK = new \App\Models\Crawler\Webcrawler\CrawlerTaskUrl();
        $da = $CTASK
            ->where('tsk_task', $id)
            ->where('tsk_propriety', 'hasDOI')
            ->findAll();

        $sx .= '<table class="table">';
        for($r=0;$r < count($da);$r++)
            {
                $line = $da[$r];
                $vlr = $line['tsk_value'];
                $ano = substr($vlr,strpos($vlr,'-')+1,strlen($vlr));
                $ano = substr($ano, 4, 4);
                $sx .= '<tr>';
                $sx .= '<td>';
                $sx .= $line['tsk_value'];
                $sx .= '</td>';

                $sx .= '<td>';
                $sx .= $ano;
                $sx .= '</td>';

                $sx .= '</tr>';
            }
        $sx .= '</table>';
        return $sx;
    }

    function hasIssueArticle($dt)
    {
        $sx = '';
        if ($dt != '') {
            $sx .= metarefresh('');
        } else {
            $sx .= metarefresh(PATH . COLLECTION);
            return $sx;
        }

        $CTASK = new \App\Models\Crawler\Webcrawler\CrawlerTaskUrl();

        $url = $dt['tsk_value'];
        $t = read_link($url);

        /******************************** DOI META */
        $s = '<meta name="citation_doi" content="';
        $pos = strpos($t, $s);
        $doi = substr($t,$pos+strlen($s),150);
        $doi = substr($doi,0,strpos($doi,'"'));

        /******************************** TITLE META */
        $data['tsk_task'] = $dt['tsk_task'];
        $data['tsk_propriety'] = 'hasDOI';
        $data['tsk_value'] = $doi;
        $data['tsk_status'] = 0;
        $data['tsk_father'] = $dt['id_tsk'];
        $CTASK->register($data);

        $sx .= h($doi);

        $urls = $this->recover_doi($t);

        foreach ($urls as $url => $id) {
                $data['tsk_task'] = $dt['tsk_task'];
                $data['tsk_propriety'] = 'hasDOIref';
                $data['tsk_value'] = $url;
                $data['tsk_status'] = 0;
                $data['tsk_father'] = $dt['id_tsk'];
                $CTASK->register($data);
                $sx .= $url . '<br/>';
        }
        $dd['tsk_status'] = 1;
        $CTASK
            ->set($dd)
            ->where('id_tsk', $dt['id_tsk'])
            ->update();
        return $sx;
    }

    function hasIssue($dt)
    {
        $sx = '';
        if ($dt != '')
            {
                $sx .= metarefresh('');
            } else {
                $sx .= metarefresh(PATH.COLLECTION);
                return $sx;
            }

        $CTASK = new \App\Models\Crawler\Webcrawler\CrawlerTaskUrl();

        $url = $dt['tsk_value'];
        $t = read_link($url);

        $urls = $this->recover_ahref($t);

        foreach($urls as $url=>$id)
            {
                $url = troca($url,'abstract/','');
                if (strpos($url,'/a/'))
                    {
                        $data['tsk_task'] = $dt['tsk_task'];
                        $data['tsk_propriety'] = 'hasIssueArticle';
                        $data['tsk_value'] = $url;
                        $data['tsk_status'] = 0;
                        $CTASK->register($data);
                        $sx .= $url . '<br/>';
                    }
            }
        $dd['tsk_status'] = 1;
        $CTASK
            ->set($dd)
            ->where('id_tsk',$dt['id_tsk'])
            ->update();
        return $sx;
    }

    function recover_doi($t)
        {
        $urls = array();
        $s = 'doi.org/';
        while ($pos = strpos($t, $s)) {
            $ta = substr($t, $pos + 8, strlen($t));
            if (strpos($ta, '"')) { $ta = substr($ta, 0, strpos($ta, '"')); }
            if (strpos($ta, ' ')) { $ta = substr($ta, 0, strpos($ta, ' ')); }
            if (strpos($ta, '<')) { $ta = substr($ta, 0, strpos($ta, '<')); }
            if (strpos($ta, '>')) { $ta = substr($ta, 0, strpos($ta, '>')); }
            if (strpos($ta, "'")) { $ta = substr($ta, 0, strpos($ta, "'")); }
            if (strpos($ta, "&")) { $ta = substr($ta, 0, strpos($ta, "&"));}
            $url = $ta;
            $url = troca($url,chr(13),'');
            $url = troca($url, chr(10), '');

            $loop = 0;
            while(substr($url,strlen($url)-1,1) == '.')
                {
                    $url = substr($url,0,strlen($url)-1);
                    $loop++;
                    if ($loop > 500) { break; }
                }
            if (isset($urls[$url]))
                {
                    $urls[$url]++;
                } else {
                    $urls[$url] = 1;
                }
            $t = substr($t, $pos + 5, strlen($t));
        }
        return ($urls);
        }

    function recover_ahref($t, $s = 'a href="/j/')
    {
        $urls = array();

        while ($pos = strpos($t, $s)) {
            $ta = substr($t, $pos + 8, strlen($t));
            $url = substr($ta, 0, strpos($ta, '"'));
            if (strpos($url, '?') > 0) {
                $url = substr($ta, 0, strpos($ta, '?'));
            }

            if (strpos($url, '/i/') > 0) {
                $url = 'https://www.scielo.br/' . $url;
                $urls[$url] = 1;
            }

            if (strpos($url, '/a/') > 0) {
                $url = 'https://www.scielo.br/' . $url;
                $urls[$url] = 1;
            }

            $t = substr($t, $pos + 5, strlen($t));
        }
        return($urls);
    }

    function recover_urls($t, $id)
    {

        $urls = $this->recover_ahref($t);

        $CTASK = new \App\Models\Crawler\Webcrawler\CrawlerTaskUrl();
        $id_task = $id;
        $tot = 0;

        foreach ($urls as $url => $id) {
            $data['tsk_task'] = $id_task;
            $data['tsk_propriety'] = 'hasIssue';
            $data['tsk_value'] = $url;
            $data['tsk_status'] = 0;

            $CTASK->register($data);
            $tot++;
        }
        return "Processo Scielo finalizado - $tot links encontrados";
    }
}
