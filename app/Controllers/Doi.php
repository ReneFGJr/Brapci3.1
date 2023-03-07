<?php

namespace App\Controllers;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'doi');

use App\Controllers\BaseController;

class Doi extends BaseController
{
    public function index($d1='', $d2 ='', $d3 ='', $d4 = '')
    {
        $DOI = new \App\Models\DOI\Index();
        return $DOI->tombstone($d1,$d2,$d3,$d4);
    }
}
