<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');

class Ajax extends BaseController
{
    public function index($act = '')
    {
        switch ($act) {
            case 'work':
                $this->work();
                break;
            case 'work_clear':
                $this->workClear();
                break;
            case 'work_all':
                $this->workAll();
                break;
            case 'mark':
                $this->mark();
                break;
            default:
                echo '=AJAX=>' . $act;
                break;
        }
    }

    function mark()
    {
        $Source = new \App\Models\Base\Sources();
        echo $Source->ajax();
    }

    function workClear()
    {
        $Work = new \App\Models\Base\Work();
        $Work->workClear();
        $sx = '<span class="text-red">';
        $sx .= lang('brapci.WorkClearing');
        $sx .= '</span>';
        $sx .= reload();
        echo $sx;
    }

    function workAll()
    {
        $Work = new \App\Models\Base\Work();
        $Issue = new \App\Models\Base\Issues();
        $IssuesWorks = new \App\Models\Base\IssuesWorks();
        $sel = $Work->getWorkMark();

        $dd1 = get("dd1");
        $dd2 = get("dd2");

        if ($pos = strpos($dd1,'?id='))
            {
                $id = substr($dd1,$pos+4,20);
                if (strpos($id,'&') > 0)
                    {
                        echo $id.'=========';
                    }

                $id = round($id);

                if ($id > 0)
                    {
                        $dt = $Issue->find($id);
                        if (count($dt) > 0)
                            {
                                $id_rdf = $dt['is_source_issue'];
                                $dt = $IssuesWorks->issueWorks($id_rdf);
                                for($r=0;$r < count($dt);$r++)
                                    {
                                        $w = 'w'.trim($dt[$r]['siw_work_rdf']);
                                        $sel[$w] = 1;
                                    }
                            } else {
                                echo "#ERRO AJ01 - Erro de Identificação";
                                exit;
                            }
                    }
            }
            $Work->putWorkMark($sel);
            $sx = '';
            $sx = '<span class="text-red">';
            $sx .= lang('brapci.WorkSelecting');
            $sx .= '</span>';
            $sx .= reload();
            echo $sx;
    }

    function work()
    {
        $Work = new \App\Models\Base\Work();
        //$Source = new \App\Models\Base\Sources();
        //echo $Source->ajax();
        $id = get("id");
        $ck = (string)get("check");

        /******************************************************** */
        $Work->workMark($id,$ck);
        echo $Work->WorkSelected();
    }
}