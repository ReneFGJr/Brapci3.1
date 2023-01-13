<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Export extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_bots.tasks';
    protected $primaryKey       = 'id_task';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_task', 'task_id', 'task_status',
        'task_propriry', 'task_offset', 'updated_at',
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
    var $eof = 0;

    function index($d1, $d2, $d3)
    {
        $sx = 'EXPORT ' . $d1;
        switch ($d1) {
            default:
                $sx = bsc($this->menu(), 12);
                break;
        }
        $sx = bs($sx);
        return $sx;
    }

    function resume()
        {
            $sx = h(lang('brapci.service_cron'),4);
            $dt = $this->findAll();
            $sx .= '<table class="table" style="width: 100%; font-size: 0.7em;">';
            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $st = $line['task_status'];
                    $style = "";

                    if ($st == '0') { $style = 'color: #888;'; }
                    if ($st == '1') { $style = 'color: #0F0;'; }

                    $sx .= '<tr>';
                    $sx .= '<td>'.$line['task_id'].'</td>';
                    $sx .= '<td style="'.$style.'">' .  bsicone('circle'). '</td>';
                    $sx .= '<td>' . $line['task_offset'] . '</td>';
                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            $sx .= '<div style=" font-size: 0.6em;">';
            $sx .= '<span style="color: #888;">' . bsicone('circle') . '</span> ' . lang('brapci.service.stop');
            $sx .= '<br>';
            $sx .= '<span style="color: #0F0;">' . bsicone('circle') . '</span> ' . lang('brapci.service.running');
            $sx .= '</div>';
            return $sx;
        }

    function next($type = '')
    {
        $dt = $this->where('task_status', 1)->orderBy('task_propriry')->findAll();
        if (count($dt) > 0) {
            $dt = $dt[0];
        }
        return $dt;
    }

    function register($task_id, $priority, $offset, $status)
    {
        $dta['task_id'] = $task_id;
        $dta['task_status'] = $status;
        $dta['task_propriry'] = $priority;
        $dta['task_offset'] = $offset;
        $dta['updated_at'] = date("Y-m-d H:i:s");
        $dt = $this->where('task_id', $task_id)->findAll();
        if (count($dt) == 0) {
            $this->set($dta)->insert();
        } else {
            $this->set($dta)->where('task_id', $task_id)->update();
        }
        return true;
    }

    function cron($d1, $d2, $d3 = '')
    {
        $sx = '';
        switch ($d1) {
                /************************************ DEFAULT */
            default:
                if ($d1 == '') {
                    $sx .= '=====EXPORT============' . cr();
                    $dtd = $this->next();

                    if (count($dtd) > 0) {
                        $sx .= $this->export_works($dtd);
                    }
                } else {
                    echo "OPS EXPORT NOT FOUND [$d1]";
                }
        }
        return $sx;
    }


    function menu()
    {
        $sx = '';
        $menu = array();
        $mod = 'export';
        $menu['#brapci.EXPORT_ELASTIC'] = '#';
        $menu[PATH . 'admin/' . $mod . '/articles'] = lang('brapci.export') . ' ' . lang('brapci.articles');
        $menu[PATH . 'admin/' . $mod . '/proceeding'] = lang('brapci.export') . ' ' . lang('brapci.proceeding');
        $menu[PATH . 'admin/' . $mod . '/books'] = lang('brapci.export') . ' ' . lang('brapci.books');
        $sx = menu($menu);
        $sx = bs(bsc($sx));
        return $sx;
    }

    function export_works($dta, $id = 0)
    {
        $sx = '';
        $offset = round(0);
        $limit = 100;

        $TYPE = $dta['task_id'];

        if ($dta['task_id'] == 'EXPORT_ARTICLE') {
            $class = 'Article';
            $type = 'JA';
        }
        if ($dta['task_id'] == 'EXPORT_BOOK') {
            $class = 'Book';
            $type = 'BO';
        }
        if ($dta['task_id'] == 'EXPORT_PROCEEDING') {
            $class = 'Proceeding';
            $type = 'EV';
        }

        $offset = $dta['task_offset'];
        $sx .= "<br>OFFSET: $offset";
        $sx .= $this->export_data($class, $type, $offset, $limit);
        if ($this->eof) {
            $this->register($TYPE, 0, 0, 0);
        } else {
            $this->register($TYPE, 1, $offset + $limit, 1);
            $sx .= '<CONTINUE>';
        }
        return $sx;
    }

    function export_data($class, $type, $offset, $limit)
    {
        $RDF = new \App\Models\Rdf\RDF();
        $RDFClass = new \App\Models\Rdf\RDFClass();
        $RDFConcept = new \App\Models\Rdf\RDFConcept();
        $Metadata = new \App\Models\Base\Metadata();
        $ElasticRegister = new \App\Models\ElasticSearch\Register();

        $id = $RDFClass->Class($class, false);

        $total = $RDFConcept->select('count(*) as total')
            ->where('cc_class', $id)
            ->findAll();
        $ids = $RDFConcept->where('cc_class', $id)->findAll($limit, $offset);
        if (count($ids) == 0) {
            $this->eof = 1;
            return "FIM";
        }
        $sx = '';
        $total = $total[0]['total'];
        $sx .= '<br>Processado: ' . (number_format($offset / $total * 100, 1, ',', '.')) . '%';
        $sx .= '<ul>';
        for ($r = 0; $r < count($ids); $r++) {
            $line = $ids[$r];
            $idr = $line['id_cc'];

            $dir = $RDF->directory($idr);
            $file = $dir . 'article.json';

            if (!file_exists($file)) {
                $RDF->c($idr);
            }

            /*
                    $json = file_get_contents($file);
                    $json = (array)json_decode($json);
                    $json['type'] = $type;
                    $json['collection'] = substr($class,0,1);
                    pre($json);
                    $sx .= '<li>' . strzero($idr, 8) . ' ' . $ElasticRegister->data($idr, $json) . ' (' . $dir . ')</li>';
                    */

            $line = $RDF->le($idr);
            $Metadata->metadata = array();
            $Metadata->metadata($line);
            $meta = $Metadata->metadata;
            $meta['collection'] = substr($class, 0, 1);
            $meta['fulltext'] = '';
            $meta['year'] = '';
            $meta['pdf'] = 0;
            $sx .= '<li>' . strzero($meta['article_id'], 8) . ' ' . $ElasticRegister->data($idr, $meta) . ' (' . $dir . ')</li>';
        }
        $sx .= '</ul>';
        return $sx;
    }
}
