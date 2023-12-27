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
            case 'Article':
                $id = $RDF->extract($dt, 'hasFileStorage');
                if (!isset($id[0]))
                    {
                        $Download = new \App\Models\Base\Download();
                        foreach($dt['data'] as $id=>$line)
                            {
                                pre($line);
                                if ($line['c_class'] == 'hasRegisterId')
                                    {
                                        $Download->download_methods($line, $id);
                                    }
                            }
                        $id = $RDF->extract($dt, 'hasFileStorage');
                        if (!isset($id[0])) {
                            echo "PDF não disponível";
                            exit;
                        }

                    }
                $id = $id[0];
                break;
            case 'Proceeding':
                $id = $RDF->extract($dt, 'hasFileStorage');
                $id = $id[0];
                break;
        }
        $Download = new \App\Models\Base\Download();
        $Download->download_pdf($id);
        exit;
    }
}