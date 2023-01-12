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
define("VERSION_BOT", 'v0.23.01.11');

class Bots extends BaseController
{
    public function index($act = '',$act2='',$act3='')
    {
        global $bot;
        $bot = true;

        $sx = '';
        if ($act == 'patent') { $act = 'patente'; }

        switch ($act) {

            case 'export':
                $Export = new \App\Models\Base\Export();
                $sx .= $Export->cron($act2, $act3);
                break;

            case 'harvesting':
                $Oaipmh = new \App\Models\Oaipmh\Index();
                $sx .= "BOT's OAIPMH ". VERSION_BOT . cr();
                $sx .= chr(13);
                $sx .= $Oaipmh->index($act2,$act3);
                break;

            case 'lattes':
                $Lattes = new \App\Models\Api\Lattes\Index();
                $sx .= "BOT 's Lattes ".VERSION_BOT.cr();
                $sx .= chr(13);
                $sx .= $Lattes->harvesting_next($act2);
                break;

            case 'patente':
                $Patente = new \App\Models\Patent\Index;
                $Patente->cron();
                break;
            case 'authority':
                $Authority = new \App\Models\Authority\Index();
                $sx .= $Authority->index('bot_'.$act2);
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
                $menu[PATH . COLLECTION . '/export'] = lang('bots.export');
                $sx .= menu($menu);
                break;
        }
        $sx .= '';
        $AGENT = $_SERVER['HTTP_USER_AGENT'];
        $POS = strpos(' '.$AGENT, 'curl');
        if ($POS > 0)
            {
                echo $sx;
                exit;
            } else {
                $sx = troca($sx,chr(10),'<br>');
                $pos = strpos($sx,'<CONTINUE>');
                if ($pos > 0)
                    {
                        $sx = troca($sx,'<CONTINUE>',metarefresh('',5));
                    }
            }
        return $sx;
    }
}