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
define("COLLECTION", 'bibliofind');
define("LIBRARY", '0000');

class Bibliofind extends BaseController
{
    function index($act='',$d1='',$d2='',$d3='')
    {
        $data['page_title'] = 'Brapci';
        $data['bg'] = 'bg-admin';
        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);

        switch($act)
            {
                case 'h':
                    $H = new \App\Models\Find\Harvesting\Index();
                    echo $H->harvesting($d1);
                    break;
                default:
                    $sx .= "BiblioFind";
                    break;
            }

        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function h($d1='')
        {
            echo "OK";
        }
}
