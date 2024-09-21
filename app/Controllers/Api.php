<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("COLLECTION", '/api');
define("PREFIX", '');

class Api extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 ='', $d4 ='', $d5 ='', $d6 = '')
    {
        $sx = '';
        $API = new \App\Models\Api\Index();
        $sx = $API->index($d1, $d2, $d3, $d4, $d5, $d6);
        return $sx;
    }
}