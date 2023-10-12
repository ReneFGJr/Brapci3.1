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
        $sx = '';
        $bread = array();
        $bread['Admin'] = PATH . '/admin';
        $sx .= breadcrumbs($bread);
        switch ($d1) {
            case 'index':
                $RDFExport = new \App\Models\Rdf\RDFExport();
                $sx .= $RDFExport->export($d2, $d3);
                break;
            case 'clear':
                $conf = get("confirm");
                if ($conf == "true") {
                    $Register = new \App\Models\ElasticSearch\Register();
                    $sql = "TRUNCATE dataset;";
                    $Register->query($sql);
                    $sx .= bsmessage('Database Dataset cleared!');
                } else {
                    $sx = 'Confirma exclusão da Base Cached?<hr/>';
                    $sx .= '<table class="table" style="width: 200px;"><tr>';
                    $sx .= '<td>';
                    $sx .= anchor(PATH . '/admin/export/clear/?confirm=true', 'SIM');
                    $sx .= '</td>';
                    $sx .= '<td>';
                    $sx .= anchor(PATH . '/admin/', lang('NO'));
                    $sx .= '</td>';
                    $sx .= '</tr></table>';
                }
                break;
            case 'articles':
                $Export = new \App\Models\Base\Export();
                return $Export->cron($d1, 'start');
                break;
            case 'authority':
                $Export = new \App\Models\Base\Export();
                return $Export->cron($d1, 'start');
                break;
            case 'corporate':
                $Export = new \App\Models\Base\Export();
                return $Export->cron($d1, 'start');
                break;
            case 'proceeding':
                $Export = new \App\Models\Base\Export();
                return $Export->cron($d1, 'start');
                break;
            case 'books':
                $Export = new \App\Models\Base\Export();
                return $Export->cron($d1, 'start');
                break;
            case 'booksChapter':
                $Export = new \App\Models\Base\Export();
                return $Export->cron($d1, 'start');
                break;
            default:
                $sx = bsc($this->menu(), 12);
                break;
        }
        $sx = bs($sx);
        return $sx;
    }

    function resume()
    {
        $sx = h(lang('brapci.service_cron'), 4);
        $dt = $this->findAll();
        $sx .= '<table class="table" style="width: 100%; font-size: 0.7em;">';
        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            $st = $line['task_status'];
            $style = "";

            if ($st == '0') {
                $style = 'color: #888;';
            }
            if ($st == '1') {
                $style = 'color: #0F0;';
            }

            $sx .= '<tr>';
            $sx .= '<td>' . $line['task_id'] . '</td>';
            $sx .= '<td style="' . $style . '">' .  bsicone('circle') . '</td>';
            $sx .= '<td>' . $line['task_offset'] . '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        $sx .= '<div style=" font-size: 0.6em;">';
        $sx .= '<span style="color: #888;">' . bsicone('circle') . '</span> ' . lang('brapci.service.stop');
        $sx .= '<br>';
        $sx .= '<span style="color: #0F0;">' . bsicone('circle') . '</span> ' . lang('brapci.service.running');
        $sx .= '<br>';
        $sx .= anchor(PATH . 'bots', lang('brapci.Bots'));
        $sx .= ' | ';
        $sx .= anchor(PATH . 'admin/export', lang('brapci.Export'));
        $sx .= ' | ';
        $sx .= anchor(PATH . 'admin/elastic/update', lang('brapci.ElasticSearch'));
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

    function register($task_id, $priority, $offset, $status, $total = -1)
    {
        $dta['task_id'] = $task_id;
        $dta['task_status'] = $status;
        $dta['task_propriry'] = $priority;
        $dta['task_offset'] = $offset;
        $dta['task_total'] = $total;
        $dta['updated_at'] = date("Y-m-d H:i:s");
        $dt = $this->where('task_id', $task_id)->findAll();
        if (count($dt) == 0) {
            $this->set($dta)->insert();
        } else {
            $this->set($dta)->where('task_id', $task_id)->update();
        }
        return true;
    }

    function remove_all($type = '')
    {
        $rst = $this->where('task_id', $type)->delete();
    }

    function cron($d1, $d2, $d3 = '')
    {
        $sx = '';
        switch ($d1) {
            case 'articles':
                switch ($d2) {
                    case 'start':
                        $this->remove_all('EXPORT_ARTICLE');
                        $BOTS = new \App\Models\Bots\Index();
                        $BOTS->task_remove('EXPORT_ARTICLE');
                        $dt = $BOTS->task('EXPORT_ARTICLE');
                        $sx .= 'Started ' . $d2 . ' export';
                        $sx .= '<hr>';
                        $sx .= anchor(PATH . 'bots/export', 'Start Export', array('class' => 'btn btn-outline-primary'));
                        break;
                    default:
                        echo "OK ==> $d2";
                }
                break;

            case 'books':
                switch ($d2) {
                    case 'start':
                        $this->remove_all('EXPORT_BOOK');
                        $BOTS = new \App\Models\Bots\Index();
                        $BOTS->task_remove('EXPORT_BOOK');
                        $dt = $BOTS->task('EXPORT_BOOK');
                        $sx .= 'Started ' . $d2 . ' export';
                        $sx .= '<hr>';
                        $sx .= anchor(PATH . 'bots/export', 'Start Export', array('class' => 'btn btn-outline-primary'));
                        break;
                    default:
                        echo "OK ==> $d2";
                }
                break;

            case 'corporate':
                switch ($d2) {
                    case 'start':
                        $this->remove_all('EXPORT_CORPORATEBODY');
                        $BOTS = new \App\Models\Bots\Index();
                        $BOTS->task_remove('EXPORT_CORPORATEBODY');
                        $dt = $BOTS->task('EXPORT_CORPORATEBODY');
                        $sx .= 'Started ' . $d2 . ' export';
                        $sx .= '<hr>';
                        $sx .= anchor(PATH . 'bots/export', 'Start Export', array('class' => 'btn btn-outline-primary'));
                        break;
                    default:
                        echo "OK ==> $d2";
                }
                break;

            case 'authority':
                switch ($d2) {
                    case 'start':
                        $this->remove_all('EXPORT_AUTHORITY');
                        $BOTS = new \App\Models\Bots\Index();
                        $BOTS->task_remove('EXPORT_AUTHORITY');
                        $dt = $BOTS->task('EXPORT_AUTHORITY');
                        $sx .= 'Started ' . $d2 . ' export';
                        $sx .= '<hr>';
                        $sx .= anchor(PATH . 'bots/export', 'Start Export', array('class' => 'btn btn-outline-primary'));
                        break;
                    default:
                        echo "OK ==> $d2";
                }
                break;

            case 'booksChapter':
                switch ($d2) {
                    case 'start':
                        $this->remove_all('EXPORT_BOOKCHAPTER');
                        $BOTS = new \App\Models\Bots\Index();
                        $BOTS->task_remove('EXPORT_BOOKCHAPTER');
                        $dt = $BOTS->task('EXPORT_BOOKCHAPTER');
                        $sx .= 'Started ' . $d2 . ' export';
                        $sx .= '<hr>';
                        $sx .= anchor(PATH . 'bots/export', 'Start Export', array('class' => 'btn btn-outline-primary'));
                        break;
                    default:
                        echo "OK ==> $d2";
                }
                break;

            case 'proceeding':
                switch ($d2) {
                    case 'start':
                        $this->remove_all('EXPORT_PROCEEDING');
                        $BOTS = new \App\Models\Bots\Index();
                        $BOTS->task_remove('EXPORT_PROCEEDING');
                        $dt = $BOTS->task('EXPORT_PROCEEDING');
                        $sx .= 'Started ' . $d2 . ' export';
                        $sx .= '<hr>';
                        $sx .= anchor(PATH . 'bots/export', 'Start Export', array('class' => 'btn btn-outline-primary'));
                        break;
                    default:
                        echo "OK $d2";
                }
                break;
                /************************************ DEFAULT */
            default:
                if ($d1 == '') {
                    $dtd = $this->next();

                    if (count($dtd) > 0) {
                        $sx .= $this->export_works($dtd);
                    } else {
                        if (agent()) {
                            $sx .= bsmessage('No task at Cron', 2);
                        } else {
                            $sx .= 'No task at Cron';
                        }
                    }
                } else {
                    echo "OPS EXPORT NOT FOUND [$d1]";
                }
        }
        $sx = bs(bsc($sx));
        return $sx;
    }


    function menu()
    {
        $sx = '';
        $menu = array();
        $mod = 'export';
        $menu['#brapci.EXPORT_ELASTIC'] = '#';
        $menu[PATH . 'admin/' . $mod . '/clear'] = lang('brapci.clear_database');
        $menu[PATH . 'admin/' . $mod . '/articles'] = lang('brapci.export') . ' ' . lang('brapci.articles');
        $menu[PATH . 'admin/' . $mod . '/proceeding'] = lang('brapci.export') . ' ' . lang('brapci.proceeding');
        $menu[PATH . 'admin/' . $mod . '/books'] = lang('brapci.export') . ' ' . lang('brapci.books');
        $menu[PATH . 'admin/' . $mod . '/booksChapter'] = lang('brapci.export') . ' ' . lang('brapci.booksChapters');
        $menu[PATH . 'admin/' . $mod . '/authority'] = lang('brapci.export') . ' ' . lang('brapci.authority');
        $menu[PATH . 'admin/' . $mod . '/corporate'] = lang('brapci.export') . ' ' . lang('brapci.corporate');

        $menu[PATH . 'admin/' . $mod . '/index/index_authors'] = lang('brapci.export') . ' ' . lang('brapci.index_person');
        $menu[PATH . 'admin/' . $mod . '/index/index_corporatebody'] = lang('brapci.export') . ' ' . lang('brapci.index_corporate');
        $menu[PATH . 'admin/' . $mod . '/index/index_subject'] = lang('brapci.export') . ' ' . lang('brapci.index_subject');
        $menu[PATH . 'admin/' . $mod . '/index/index_journal'] = lang('brapci.export') . ' ' . lang('brapci.index_journal');
        $menu[PATH . 'admin/' . $mod . '/index/index_proceeding'] = lang('brapci.export') . ' ' . lang('brapci.index_proceeding');

        $menu['#BOTS'] = "";
        $menu[PATH . 'bots/'] = lang('brapci.export') . ' ' . lang('brapci.bots');

        $sx = menu($menu);
        $sx = bs(bsc($sx));
        return $sx;
    }

    function export_works($dta, $id = 0)
    {
        $sx = '';
        $offset = round(0);
        $limit = 1000;

        $TYPE = $dta['task_id'];

        switch ($TYPE) {
            case 'EXPORT_ARTICLE':
                $class = 'Article';
                $type = 'JA';
                break;
            case 'EXPORT_BOOK':
                $class = 'Book';
                $type = 'BO';
                break;
            case 'EXPORT_PROCEEDING':
                $class = 'Proceeding';
                $type = 'EV';
                break;
            case 'EXPORT_BOOKCHAPTER':
                $class = 'BookChapter';
                $type = 'BC';
                break;
            case 'EXPORT_AUTHORITY':
                $class = 'Person';
                $type = 'BC';
                break;
            case 'EXPORT_CORPORATEBODY':
                $class = 'CorporateBody';
                $type = 'BC';
                break;
            case 'CHECK_ALTLABEL':
                $RDFChecks = new \App\Models\Rdf\RDFChecks();
                if (agent()) {
                    $sx .= $RDFChecks->next_prefLabel();
                } else {
                    $sx = $RDFChecks->next_prefLabel();
                    echo $sx;
                    exit;
                }
                return $sx;
                break;

            case 'CHECK_ABSTRACT':
                $Abstracts = new \App\Models\AI\NLP\Abstracts();
                if (agent()) {
                    $sx .= $Abstracts->check_next();
                } else {
                    $sx = $Abstracts->check_next();
                    echo $sx;
                    exit;
                }
                return $sx;
                break;
            case 'CHECK_TITLES':
                $titles = new \App\Models\AI\NLP\Titles();
                if (agent()) {
                    $sx .= $titles->check_next();
                } else {
                    $sx = $titles->check_next();
                    echo $sx;
                    exit;
                }
                return $sx;
                break;
            default:
                echo "OPS EXPORT NOT IMPLEMENTED $TYPE";
                exit;
        }

        if (!isset($class)) {
            return "";
        }

        $offset = $dta['task_offset'];
        if (agent() == 1) {
            $data['title'] = 'BotPage';
            $data['bg'] = 'bg-admin';
            $sx .= view('Brapci/Headers/header', $data);
            $sx .= view('Brapci/Headers/navbar', $data);
        }
        $sx .= "<br>OFFSET: $offset - LIMIT $limit ";
        $sx .= $this->export_data($class, $type, $offset, $limit);
        if ($this->eof) {
            $this->remove_all($dta['task_id']);
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
        $sx = h($class);
        $total = $total[0]['total'];

        $sx .= '<br>Processado: ' . (number_format($offset / $total * 100, 1, ',', '.')) . '%';
        $sx .= '<ul>';
        for ($r = 0; $r < count($ids); $r++) {
            $xline = $ids[$r];
            $idr = $xline['id_cc'];

            $line = $RDF->le($idr);
            $Metadata->metadata = array();

            /*********************** Metadata */
            $Metadata->metadata($line);
            $meta = $Metadata->metadata;

            //$meta['year'] = '';
            if (count($meta) > 0) {
                /***************************** KEYWORD */
                if (!isset($meta['Keywords'])) {
                    $meta['KEYWORD'] = 0;
                } else {
                    $meta['KEYWORD'] = 1;
                }
                /***************************** KEYWORD */
                if (!isset($meta['Abstract'])) {
                    $meta['ABSTRACT'] = 0;
                } else {
                    $meta['ABSTRACT'] = 1;
                }
                /************************** pdf */
                if (!isset($meta['PDF_id'])) {
                    $meta['PDF'] = 0;
                } else {
                    $meta['PDF'] = 1;
                }

                $sx .= '<li>' . strzero(trim($meta['ID']), 8) . ' ' .
                    $ElasticRegister->data($idr, $meta) . '</li>';
            }
        }
        $sx .= '</ul>';
        return $sx;
    }
}
