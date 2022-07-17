<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", '');

class Download extends BaseController
{
    function download($id = '')
    {
        $Download = new \App\Models\Base\Download();
        $Download->download_pdf($id);
        exit;
    }
    public function index($act = '')
    {
        switch ($act) {
            default:
                echo '=DOWNLOAD=>' . $act;
                break;
        }
    }
}