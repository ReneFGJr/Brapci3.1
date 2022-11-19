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
    public function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $sx = '';
        switch ($d1) {
            case 'lattes':
                $API = new \App\Models\Api\Lattes\Index();
                $sx = $API->index($d2, $d3, $d4);
                break;
            default:
                $API = new \App\Models\Api\Index();
                $sx = $API->index($d1, $d2, $d3, $d4);
        }
        return $sx;
    }
}