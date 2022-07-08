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
        echo '==>' . $act;
        switch ($act) {
            case 'mark':
                $this->mark();
                break;
            default:
                break;
        }
    }

    function mark()
    {
        $id = get("id");
        $ok = get("ok");
        if (!isset($_SESSION['sj'])) {
            $sj = array();
        } else {
            $sj = (array)json_decode($_SESSION['sj']);
        }

        /********************************* CHECK */
        if (!isset($sj[$id])) {
            $sj[$id] = 1;
        } else {
            if ($sj[$id] == 1) {
                $sj[$id] = 0;
            } else {
                $sj[$id] = 1;
            }
        }
        $_SESSION['sj'] = json_encode($sj);
        print_r($_SESSION);
    }
}