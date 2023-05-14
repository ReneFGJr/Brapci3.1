<?php

namespace App\Controllers;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'wp');

use App\Controllers\BaseController;

class Wp extends BaseController
{
    public function index($d1='', $d2 ='', $d3 ='', $d4 = '')
    {
        $WP = new \App\Models\Wordpress\Index();
        return $WP->index($d1,$d2,$d3,$d3,$d4);
    }
}
