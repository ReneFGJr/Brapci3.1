<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", 'bots');
define("PREFIX", '');
define("LIBRARY", '1000');

class Bots extends BaseController
{
    public function index($act = '')
    {
        switch ($act) {
            case 'pdf':
                $DownloadPDF = new \App\Models\Bots\DownloadPDF();
                $DownloadPDF->harvesting();
            default:
                $menu = array();
                $menu[PATH . COLLECTION . '/pdf'] = lang('bots.harvesting_pdf');
                echo menu($menu);
                break;
        }
    }
}