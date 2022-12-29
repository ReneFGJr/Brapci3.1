<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/bots');
define("PREFIX", '');
define("LIBRARY", '1000');

class Bots extends BaseController
{
    public function index($act = '',$act2='',$act3='')
    {
        $sx = '';
        if ($act == 'patent') { $act = 'patente'; }

        switch ($act) {

            case 'lattes':
                $Lattes = new \App\Models\Api\Lattes\Index();
                echo "BOT's Lattes $act2".cr();
                echo '<hr>';
                echo $Lattes->harvesting_next($act2);
                break;

            case 'patente':
                $Patente = new \App\Models\Patent\Index;
                $Patente->cron();
                break;
            case 'authority':
                $Authority = new \App\Models\Authority\Index();
                echo $Authority->index('bot_'.$act2);
                exit;
                break;
            case 'pdf':
                $DownloadPDF = new \App\Models\Bots\DownloadPDF();
                $sx .= $DownloadPDF->harvesting();
            default:
                $menu = array();
                $menu['#DAILY'] = "";
                $menu[PATH . COLLECTION . '/authority/remissive'] = lang('bots.authority.remissive');
                $menu[PATH . COLLECTION . '/authority/collaboration'] = lang('bots.authority.collaboration');
                $menu[PATH . COLLECTION . '/pdf'] = lang('bots.harvesting_pdf');
                echo menu($menu);
                break;
        }
        $sx .= '</pre>';
        return $sx;
    }
}