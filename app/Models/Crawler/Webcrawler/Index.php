<?php

namespace App\Models\Crawler\Webcrawler;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_bots.webcrawler';
    protected $primaryKey       = 'id_wc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cron', 'cron_acron', 'cron_name',
        'cron_cmd', 'cron_day', 'cron_exec',
        'cron_timeout', 'cron_prior'
    ];
    protected $typeFields    = [
        'hidden', 'string*', 'string*',
        'text', 'op:1&every_day:2&last_day_month:15&every_hour', 'op:webcrawler&webcrawler:php&php:pythob&python*',
        'op:30&30s:60&60s:90&90s:120&120s*', '[1-99]*',
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

    function index($act, $id)
    {
        $sx = h('Webcrawler - ' . $act . ' - ' . $id, 3);

        switch ($act) {
            case 'harvesting':
                $mth = get("mth");
                $sx .= $this->harvesting($id, $mth);
                break;
            default:
                $sx .= $this->menu($act);
                break;
        }
        return bs($sx);
    }

    function menu($id)
    {
        $menu = array();
        $menu[PATH . COLLECTION . '/webcrawler/harvesting/' . $id] = 'Harvesting';

        $CTASK = new \App\Models\Crawler\Webcrawler\CrawlerTaskUrl();
        $tot = $CTASK
            ->select('tsk_propriety, count(*) as total')
            ->where('tsk_task', $id)
            ->where('tsk_status', 0)
            ->groupBy('tsk_propriety')
            ->findAll();
        for ($r = 0; $r < count($tot); $r++) {
            $menu[PATH . COLLECTION . '/webcrawler/harvesting/' . $id . '?mth=' . $tot[$r]['tsk_propriety']] = 'Harvesting - ' . $tot[$r]['tsk_propriety'] . ' (' . $tot[$r]['total'] . ')';
        }


        $sx = menu($menu);
        return $sx;
    }

    function harvesting($id, $mth = '')
    {
        $Crawler = new \App\Models\Crawler\Index();
        $dt = $Crawler->find($id);
        $sx = h($mth);

        switch ($mth) {
            case 'hasIssue':
                $CTASK = new \App\Models\Crawler\Webcrawler\CrawlerTaskUrl();
                $Scielo = new \App\Models\Crawler\Webcrawler\Scielo();
                $dt = $CTASK->where('tsk_task', $id)->where('tsk_status', 0)->where('tsk_propriety', $mth)->first();
                $sx .= $Scielo->hasIssue($dt);
                break;
            case 'hasIssueArticle':
                $CTASK = new \App\Models\Crawler\Webcrawler\CrawlerTaskUrl();
                $Scielo = new \App\Models\Crawler\Webcrawler\Scielo();
                $dt = $CTASK->where('tsk_task', $id)->where('tsk_status', 0)->where('tsk_propriety', $mth)->first();
                $sx .= $Scielo->hasIssueArticle($dt);
                break;
            default:
                $url = $dt['cron_cmd'];
                $t = read_link($url);
                $ids = $this->method_id($t);

                /*************** SCIELO */
                $Scielo = new \App\Models\Crawler\Webcrawler\Scielo();
                $sx .= $Scielo->recover_urls($t, $id);
                break;
        }

        return $sx;
    }

    function method_id($txt)
    {
        $ids = array();
        while ($pos = strpos($txt, 'id="')) {
            $id = substr($txt, $pos + 4, 30);
            $id = substr($id, 0, strpos($id, '"'));
            $ids[$id] = $id;
            $txt = substr($txt, $pos + 4, strlen($txt));
        }
        return $ids;
    }
}
