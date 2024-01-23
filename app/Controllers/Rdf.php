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
define("LIBRARY", '0000');
define("COLLECTION", '/rdf');

function cab($data='')
{
    $data['title'] = 'RDF Editor';
    $sx = view('Brapci/Headers/header', $data);
    if ($data['show'])
        {
            $sx .= view('Brapci/Headers/navbar', $data);
        }
    return $sx;
}

class RDF extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '',$cab='')
    {
        $data['title'] = 'Brapci - RDF';
        $data['bg'] = 'bg-admin';
        $data['show'] = True;
        $data['menu'] = [];
        $sx = '';
        $sx .= cab($data);
        $RDF2 = new \App\Models\RDF2\RDF();
        $sx .= $RDF2->index($d1, $d2, $d3, $d4,$d5, $cab);
        return $sx;
    }

    //function
}
