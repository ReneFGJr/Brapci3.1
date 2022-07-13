<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');

class Ajax extends BaseController
{
    public function index($act = '')
    {
        switch ($act) {
            case 'mark':
                $this->mark();
                break;
            default:
                echo '=AJAX=>' . $act;
                break;
        }
    }

    function mark()
    {
        $Source = new \App\Models\Base\Sources();
        echo $Source->ajax();
    }
}