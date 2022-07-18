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
define("COLLECTION", '');

class MainPages extends BaseController
{
    public function index($pag = '')
    {
        $sx = '';
        /**** PAGES */
        if ($pag == '') {
            $pag = 'search';
            $pag = 'under_construction';
        }
        $data['page_title'] = 'Brapci - ' . ucfirst($pag);
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Brapci/Headers/navbar', $data);
        /**** CHECK PAGE */
        $file = APPPATH . 'Views/Brapci/Pages/' . strtolower($pag) . '.php';
        if (file_exists($file)) {
            $sx .= view('Brapci/Pages/' . $pag);
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        /**** FOOTER */
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }
}