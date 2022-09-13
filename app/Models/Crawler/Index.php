<?php

namespace App\Models\Crawler;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'brapci_bots.cron';
    protected $primaryKey       = 'id_cron';
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

    function index($act, $id, $id2='', $id3='')
    {
        $sx = '';
        $this->id = $id;
        $this->path = PATH . COLLECTION ;
        $this->path_back = PATH . COLLECTION ;

        $sx .= h('Webcrawler - '.$act . ' - '.$id,3);

        switch ($act) {
            case 'webcrawler':
                $Crawler = new \App\Models\Crawler\Webcrawler\Index();
                //$sx .= $Crawler->index($id2, $id3);
                break;

            case 'viewid':
                $sx .= $this->viewid($id);
                break;

            case 'edit':
                $this->id = $id;
                $sx .= form($this);
                break;
            default:
                $sx .= $this->task();
                break;
        }
        $sx = bs($sx);
        return $sx;
    }


    function task()
        {
            $sx = '';
            $sx .= h(lang('crawler.task'), 2);
            $sx .= tableview($this);
            return $sx;
        }

    function viewid($id,$id2='',$id3='',$id4='')
        {
            $dt = $this->find($id);
            $sx = '';
            $sx .= bsc(h($dt['cron_name']), 12);
            $sx .= bsc($dt['cron_acron'],4);
            $sx .= bsc($dt['cron_exec'],1);
            $sx .= bsc('Timeout: '.$dt['cron_timeout'], 2);
            $sx .= bsc('Prioriry: ' . $dt['cron_prior'], 2);
            $sx .= bsc('<pre>'.$dt['cron_cmd'].'</pre>', 12);


            $type = $dt['cron_exec'];

            $sx .= h($id.'-'.$id2.'-'.$id3.'-'.$id4, 2);
            switch($type)
                {
                    case 'php':
                        break;
                    case 'python':
                        break;

                    case 'webcrawler':
                        $Crawler = new \App\Models\Crawler\Webcrawler\Index();
                        $sx .= $Crawler->index($id,$id2,$id3);
                        break;
                }
            $sx = bs($sx);
            return $sx;
        }

}
