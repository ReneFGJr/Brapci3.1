<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */

$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie', 'highchart']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '0000');
define("COLLECTION", '/benancib');

class BrapciLab extends BaseController
{
    public function home()
    {
        $data = [];
        return view('BrapciLabs/home', $data);
    }
}
