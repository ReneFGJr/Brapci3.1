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
        $RDF = new \App\Models\RDF2\RDF();
        $dt = $RDF->le($id);

        if ($dt == [])
            {
                $sx = bsmessage("Item não existe - Download PDF - ".$id);
                return $sx;
            }

        $class = $dt['concept']['c_class'];

        switch ($class) {
            case 'Article':
                echo "OK";
                $id = $RDF->extract($dt, 'hasFileStorage','A');
                if (!isset($id[0]))
                    {
                    echo "OK2";
                        $Download = new \App\Models\Base\Download();
                    echo "OK3";
                        foreach($dt['data'] as $id=>$line)
                            {
                                if (trim($line['c_class']) == 'hasRegisterId')
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