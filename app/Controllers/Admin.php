<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/* SESSION */
$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'admin');
define("LIBRARY", '0000');

class Admin extends BaseController
{
    public function index($act = '', $sub = '', $id = '',$id1='',$id2='',$id3='')
    {
        $Socials = new \App\Models\Socials();
        if ($Socials->getAccess("#ADM"))
            {
                $Admin = new \App\Models\Base\Admin\Index();
                $data['page_title'] = 'Brapci';
                $data['bg'] = 'bg-admin';
                $sx = '';
                $sx .= view('Brapci/Headers/header', $data);
                $sx .= view('Brapci/Headers/navbar', $data);
                switch($act)
                    {
                        case 'v':
                            $RDF = new \App\Models\Rdf\RDF();
                            $sx .= $RDF->edit_link($sub);
                            $sx .= $RDF->index('v', $sub);
                            break;
                        case 'a':
                            $RDF = new \App\Models\Rdf\RDF();
                            $sx .= $RDF->form($sub);
                        break;

                        default:
                            $sx .= $Admin->index($act, $sub, $id,$id1,$id2,$id3);
                        break;
                    }
                $sx .= view('Brapci/Headers/footer', $data);
            } else {
                return view('Brapci/Headers/deny');
            }
        return $sx;
    }
}