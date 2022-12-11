<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */
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
        $RDF = new \App\Models\Rdf\RDF();
        $dt = $RDF->le($id);
        $class = $dt['concept']['c_class'];
        switch ($class) {
            case 'Proceeding':
                $d = $RDF->extract($dt, 'hasFileStorage');
                break;
        }
        $Download = new \App\Models\Base\Download();
        $Download->download_pdf($id);
        exit;
    }
}