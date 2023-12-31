<?php

namespace App\Models\Bots;

use CodeIgniter\Model;

class Cron extends Model
{
    protected $DBGroup          = 'bots';
    protected $table            = 'cron';
    protected $primaryKey       = 'id_cron';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_cron','cron_acron','cron_name',
        'cron_cmd','cron_day','cron_exec',
        'cron_timeout','cron_prior','update_at'
    ];

    protected $typeFields    = [
        'hidden', 'string', 'string',
        'text', '[0:31]', 'op:php&php:python&python',
        '[0:120]', '[0:99]', 'set:0'
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
    public $agent = 0;
    public $id = 0;
    public $path = PATH.'admin/cron/';
    public $path_back = PATH . 'admin/cron/';

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $web = agent();
        if ($web == '') {
            $web = 0;
        }
        $this->agent = $web;

        $sx = '';
        $d1 .= get("d1");
        $d2 .= get("d2");
        $d3 .= get("d3");
        $sx .= "[$d1,$d2,$d3]";

        switch ($d1) {
            case 'view':
                $sx .= $this->viewid($d2, $d3);
                break;
            case 'edit':
                $sx .= $this->edit($d2,$d3);
                break;
            case 'next':
                $this->next();
                break;
            default:
                $sx .= $this->show_task();
        }
        return $sx;
    }

    function edit($d1)
        {
            $this->id = $d1;
            $sx = bs(bsc(form($this),12));
            return $sx;
        }

    function viewid($d1, $d2)
    {
        $CronLogs = new \App\Models\Bots\CronLogs();
        $dtc = $this->where('cron_acron', $d1)->findAll();
        $sx = '';
        $sb = '';
        foreach($dtc as $id=>$dt)
            {
                $sb .= $this->header($dt);
            }
        $sb = bs($sb);
        $sa = $CronLogs->show($d1, 20);
        $sx .= bsc($sb, 9) . bsc($sa, 3, 'text-end');
        $sx = bs($sx);
        return $sx;
    }

    function btn_edit($line)
        {
            $sx = '<a href="'.PATH.'admin/cron/edit/'.$line['id_cron'].'" class="btn btn-outline-primary">'.lang("brapci.edit").'</a>';
            return $sx;
        }

    function header($dt)
    {
        $sx = '';
        $sx .= bsc(h($dt['cron_name'], 2), 10);
        $sx .= bsc(h($dt['cron_acron'], 2), 2, 'text-end');

        $sx .= bsc($dt['cron_exec'], 2,'border-top border-secondary p-2');
        $sx .= bsc($dt['cron_day'] . ' day',2, 'border-top border-secondary p-2');
        $sx .= bsc($dt['cron_timeout'] . ' timeout',2, 'border-top border-secondary p-2');
        $sx .= bsc($dt['cron_prior'] . ' priority',2, 'border-top border-secondary p-2');
        $sx .= bsc($this->btn_edit($dt),4, 'border-top border-secondary p-2');
        $sx = bs($sx);
        return $sx;
    }

    function show_task()
    {
        $web = $this->agent;
        $date = date("Ymd");

        $dt = $this
            ->where('update_at <> '.$date)
            ->orderby("cron_prior")
            ->findAll();

        $sx = '';
        if ($web) {
            $sx .= '<ul>';
        } else {
            echo "SERVICE NAME";
            echo "\t\t\t\t";
            echo "\t";
            echo "DAY";
            echo "\t";
            echo "TYPE";
            echo "\t";
            echo "PRIOR";
            echo "\t";
            echo "LAST";
            echo cr();
            echo "=========================================";
            echo "=========================================";
            echo cr();
        }
        foreach ($dt as $id => $line) {
            $link = '<a href="' . PATH . '/admin/cron/view/' . $line['cron_acron'] . '">';
            $linka = '</a>';
            if ($web) {
                $sx .= '<li>';
                $sx .= $link . $line['cron_name'] . $linka;
                $sx .= ' <sup>'. $line['cron_exec'].'</sup>';
                $sx .= ' ('.stodbr($line['update_at']).')';
                $sx .= '</li>';
            } else {
                $sx .= $line['cron_name'] . cr();
                $name = $line['cron_name'];
                while (strlen($name) < 40) {
                    $name .= ' ';
                }
                echo $name;
                echo "\t";
                echo $line['cron_day'];
                echo "\t";
                echo $line['cron_exec'];
                echo "\t";
                echo $line['cron_prior'];
                echo "\t";
                echo stodbr($line['update_at']);
                echo cr();
            }
        }
        if ($web) {
            $sx .= '</ul>';
            $sx = bs(bsc($sx, 12));
        } else {
            exit;
        }
        return $sx;
    }

    function next()
    {
        $date = date("Ymd");
        $day = date("d");
        $dt = $this
            ->where("update_at <> ".$date)
            ->orderBy("cron_prior")
            ->findAll();

        foreach ($dt as $id => $line) {
            $ok = False;
            ########### Regra 1
            if ($line['cron_day'] == 0) {
                $ok = True;
            }
            ########### Regra 2
            if ($line['cron_day'] == $day) {
                $ok = True;
            }

            ########## List to DO
            if ($ok) {
                if ($this->agent == 0) {
                    $this->exec($line);
                }
            }
        }

        echo "FIM DO PROCESSO".cr();
    }

    function exec($line)
        {
            $type = $line['cron_exec'];
            $cmd = $line['cron_cmd'];
            echo "==". UpperCase($type)."============".cr();
            switch($type)
                {
                    case 'php':
                        echo "\t". $line['cron_name'];
                        echo cr();
                        break;
                    case 'python':
                        echo "\t". $line['cron_name'];
                        echo cr();
                        if ($cmd != '')
                        {
                            $txt = shell_exec($cmd);
                            echo $txt;
                        } else {
                            echo "Sem comando";
                        }


                        echo cr();
                        break;
                }
        }
}
