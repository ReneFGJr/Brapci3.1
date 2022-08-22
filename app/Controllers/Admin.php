<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'admin');
define("LIBRARY", '1000');

class Admin extends BaseController
{
    public function index($act = '', $sub = '', $id = '')
    {
        $Socials = new \App\Models\Socials();
        if ($Socials->getAccess("#ADM"))
            {
                $ADMIN = new \App\Models\Base\Admin\Index();
                $data['page_title'] = 'Brapci';
                $data['bg'] = 'bg-admin';
                $sx = '';
                $sx .= view('Brapci/Headers/header', $data);
                $sx .= view('Brapci/Headers/navbar', $data);

                switch($act)
                    {
                        case 'v':
                            $RDF = new \App\Models\Rdf\RDF();
                            $sx = $RDF->index('v', $sub);
                            break;
                        case 'a':
                            $RDF = new \App\Models\Rdf\RDF();
                            $sx .= $RDF->form($sub);
                        break;

                        default:
                            $sx .= $ADMIN->index($act, $sub, $id);
                        break;
                    }
                $sx .= view('Brapci/Headers/footer', $data);
            } else {
                return redirect('MainPages::index');
            }
        return $sx;
    }
}