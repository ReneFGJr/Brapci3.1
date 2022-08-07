<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '1000');
define("COLLECTION", '/books');

function cab()
{
    $data['title'] = 'RDF Editor';
    $sx = view('Brapci/Headers/header', $data);
    return $sx;
}

class RDF extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '')
    {
        $sx = '';
        switch ($d2) {
            case 'editRDF':
                $RDF = new \App\Models\Rdf\RDF();
                $sx .= cab();
                $sx .= $RDF->form($d3);
                break;
            case 'edit':
                $sx .= cab();
                $RDF = new \App\Models\Rdf\RDF();
                $sx .= $RDF->index('form', $d2, $d3, $d4, $d5);
                break;

            default:
                $sx .= cab();
                $RDF = new \App\Models\Rdf\RDF();
                $sx .= $RDF->index($d1, $d2, $d3, $d4, $d5);
                break;
        }
        return $sx;
    }
}
