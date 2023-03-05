<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("COLLECTION", '/patente');
define("MODULE", 'patente');
define("PREFIX", '');
define("LIBRARY", '0000');

class Patente extends BaseController
{
    public function index($act = '', $d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $data['page_title'] = 'Brapci - Patentes';
        $data['bg'] = 'bg-patente d-print-none';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        $sx .= view('Ai/Header/navbar', $data);
        $sx .= view('Brapci/Pages/carrossel', $data);

        switch ($act) {
            case 'agent':
                $RPIAgents = new \App\Models\Patent\RPIAgents();
                $sx .= $RPIAgents->viewtable($d1,$d2,$d3);
                break;

            case 'viewissue':
                $RPIIssue = new \App\Models\Patent\RPIIssue;
                $sx .= $RPIIssue->viewissue($d1);
                break;

            case 'v':
                $sx .= $this->v($d1);
                break;

            case 'issue':
                $RPIIssue = new \App\Models\Patent\RPIIssue;
                $sx .= $RPIIssue->panel($d1);
                break;

            case 'harvesting':
                if ($d1 == '') {
                    $RPIIssue = new \App\Models\Patent\RPIIssue();
                    $dt = $RPIIssue->select("rpi_nr")->orderBy('rpi_nr', 'DESC')->limit(1)->findAll();

                    if (count($dt) == 0)
                        {
                            $d1 = 2600;
                        } else {
                            $d1 = $dt[0]['rpi_nr'] + 1;
                        }

                }
                $Patent = new \App\Models\Patent\Index();
                $sx .= $Patent->index('harvesting', $d1);
                break;

            case 'proccess':
                $RPI_import = new \App\Models\Patent\RPIImport();
                $sx .= $RPI_import->proccess($d1);
                break;

            default:
                $menu = array();
                $menu['#'.lang('patent.RPI')] = '';
                $menu[PATH . COLLECTION . '/harvesting'] = lang('patent.harvesting');
                $menu[PATH . COLLECTION . '/issue'] = lang('patent.rpi_issue');
                $menu[PATH . COLLECTION . '/agent'] = lang('patent.rpi_agent');
                $sx .= bs(bsc(menu($menu), 12));
                break;
        }
        $sx .= view('Brapci/Headers/footer', $data);
        return $sx;
    }

    function v($id)
        {
            $RPIIssue = new \App\Models\Patent\RPIIssue;
            $RPIDespacho = new \App\Models\Patent\RPIDespacho;
            $data = array();
            $data['despacho'] = $RPIDespacho->show($id);
            $sx = view('Patente/View', $data);

            return $sx;
        }
}
