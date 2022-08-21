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
define("COLLECTION", '/rdf');

function cab()
{
    $data['title'] = 'RDF Editor';
    $sx = view('Brapci/Headers/header', $data);
    return $sx;
}

class RDF extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '',$cab='')
    {
        $data['title'] = 'Brapci - RDF';
        $data['bg'] = 'bg-admin';
        $sx = '';
        $cab = cab();
        $RDF = new \App\Models\Rdf\RDF();

        $RDF = new \App\Models\Rdf\RDF();
        $sx .= $RDF->index($d1, $d2, $d3, $d4,$d5, $cab);

        $sx .= view('Brapci/Headers/footer', $data);

        return $sx;
    }
}
