<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'admin');
define("LIBRARY", '1000');

class Bibliofind extends BaseController
{
    public function index($act='',$d1='',$d2='',$d3='')
    {
        switch($act)
            {
                case 'h':
                    $H = new \App\Models\Find\Harvesting\Index();
                    echo $H->harvesting($d1);
                    break;

            }
    }

    function h($d1='')
        {
            echo "OK";
        }
}
