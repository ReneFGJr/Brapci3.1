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

        echo h($url);

        $urls = $this->recover_doi($t);

        pre($urls);

        foreach ($urls as $url => $id) {
            $url = troca($url, 'abstract/', '');
            if (strpos($url, '/a/')) {
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
            if (strpos($ta,'"')) { $url = substr($ta, 0, strpos($ta, '"')); }
            if (strpos($ta, ' ')) { $url = substr($ta, 0, strpos($ta, ' '));}
            $url = troca($url,chr(13),'');
            $url = troca($url, chr(10), '');


            $urls[$url] = 1;
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
