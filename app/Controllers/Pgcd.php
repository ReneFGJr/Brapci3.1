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
define("COLLECTION",'pgcd');

class Pgcd extends BaseController
{
    public function index($act='',$subact='',$id='')
    {
        $user = 1;
        $data['page_title'] = 'Gestão do Plano de Ciclo de Dados - PGCD';
        $sx = '';
        $sx .= view('PGCD/Headers/header', $data);
        $sx .= view('PGCD/Headers/navbar', $data);
        switch($act)
            {
                case 'plans':
                    $Plans = new \App\Models\PGCD\Plans();
                    $sx .= $Plans->index($subact,$id);
                    break;

                default:
                $Plans = new \App\Models\PGCD\Plans();
                    $sx .= view('PGCD/Pages/welcome', $data);
                    $sx .= view('PGCD/Pages/management/plan_create', $data);
                    $sx .= $Plans->plans_list($user);
                break;
            }        
        $sx .= view('PGCD/Headers/footer', $data);
        return $sx;
    }
}