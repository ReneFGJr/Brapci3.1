<?php

namespace App\Controllers;

use App\Controllers\BaseController;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'dci');

class G3vent extends BaseController
{
    public function index($d1 = '', $d2 = '', $d3 ='', $d4 ='', $d5 = '')
    {
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $G3vent = new \App\Models\Gev3nt\Inscritos();
        switch($d1)
            {
                default:
                    $sx .= $G3vent->lista_eventos();
            }
        $sx .= view('G3vent/Headers/footer', $data);
        return $sx;
    }

    public function import()
    {
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $G3vent = new \App\Models\G3vent\EventsNamesModel();
        $sx .= 'Importar Pessoas';
        $data = [];
        $sx .= view('G3vent/person/import', $data);
        $sx .= view('G3vent/Headers/footer', $data);
        return $sx;
    }

    function importRun()
    {
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $G3vent = new \App\Models\G3vent\EventsNamesModel();

        $lista = trim(get("lista"));
        $lista = str_replace("\t", ";", $lista);
        $linhas = explode("\n", $lista);
        foreach ($linhas as $line)
            {
                $line = trim($line);
                if ($line != '')
                    {
                        $partes = explode(";", $line);
                        if (count($partes) >= 2)
                            {
                                $nome = nbr_author(trim($partes[0]),7);
                                $email = trim($partes[1]);

                                $data = [];
                                $data['n_nome'] = $nome;
                                $data['n_email'] = $email;

                                $dt = $G3vent->where('n_email', $email)->first();                                
                                if (!isset($dt['id_n']))
                                {                                    
                                    $idx = $G3vent->set($data)->insert();
                                } else {
                                    // Já existe    
                                }
                            }
                    }
            }

        $dt = $G3vent->findAll();            
        foreach ($dt as $key => $d) {
            $nome = $d['n_nome'];
            if (strpos($nome, ' ') > 0)
            {

            } else {
                $G3vent->where('id_n', $d['id_n'])->delete();
            }
        }
        

        return redirect()->to(base_url('event/pessoas'));
    }

    public function pessoas()
    {
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $G3vent = new \App\Models\G3vent\EventsNamesModel();
        $sx .= 'Lista de Pessoas';
        $data = [];
        $data['pessoas'] = $G3vent->orderby('n_nome')->findAll();
        $sx .= view('G3vent/person/lista', $data);
        $sx .= view('G3vent/Headers/footer', $data);
        return $sx;
    }   

    function events()
    {
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $EventsModel = new \App\Models\G3vent\EventsModel();
        $sx .= 'Lista de Eventos';
        $data = [];
        $data['events'] = $EventsModel->orderby('id_e desc')->findAll();
        $sx .= view('G3vent/events/lista', $data);
        $sx .= view('G3vent/Headers/footer', $data);
        return $sx;
    }

    function events_update($id_e)
    {
        $EventsModel = new \App\Models\G3vent\EventsModel();

        $data = [];
        $data['e_name']        = trim(get('e_name'));
        $data['e_event']       = trim(get('e_event'));
        $data['e_data_i']      = trim(get('e_data_i'));
        $data['e_data_f']      = trim(get('e_data_f'));
        $data['e_status']      = trim(get('e_status'));
        $data['e_texto']       = trim(get('e_texto'));
        $data['e_keywords']    = trim(get('e_keywords'));
        $data['e_data']        = trim(get('e_data'));
        $EventsModel->where('id_e', $id_e)->set($data)->update();
        return redirect()->to(base_url('event/event/view/' . $id_e));
    }

    function events_register($id_e)
    {
        $EventInscritosModel = new \App\Models\G3vent\EventInscritosModel();
        $EventsNamesModel = new \App\Models\G3vent\EventsNamesModel();
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $data = get("lista");
        $lines = explode("\n", $data);
        if (count($lines) > 0)
            {                
                foreach ($lines as $line)
                    {
                        $line = trim($line);
                        if ($line != '')
                            {
                                $email = trim($line);
                                $dt = $EventsNamesModel->where('n_email', $email)->first();
                                pre($dt);
                                if (isset($dt['id_n']))
                                    {
                                        // Inscrever no evento
                                        $data = [];
                                        $data['en_event'] = $id_e;
                                        $data['en_name']  = $dt['id_n'];
                                        $data['en_inscr'] = date("Y-m-d H:i:s");
                                        
                                        $idx = $EventInscritosModel->set($data)->insert();
                                    }
                            }
                    }
                    return redirect()->to(base_url('event/event/view/' . $id_e));
            }

        $EventsModel = new \App\Models\G3vent\EventsModel();
        $sx .= 'Inscrever Participantes';
        $data = [];
        $data['event'] = $EventsModel->where('id_e', $id_e)->first();
        $sx .= view('G3vent/events/register', $data);
        $sx .= view('G3vent/Headers/footer', $data);
        return $sx;
    }

    function events_view($id_e)
    {
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $EventsModel = new \App\Models\G3vent\EventsModel();
        $sx .= 'Ver Evento';
        $data = [];
        $data['event'] = $EventsModel->where('id_e', $id_e)->first();
        $sx .= view('G3vent/events/view', $data);
        $sx .= view('G3vent/Headers/footer', $data);
        return $sx;
    }

    function events_edit($id_e)
    {
        $data = [];
        $sx = '';
        $sx .= view('G3vent/Headers/header', $data);
        $sx .= view('G3vent/Headers/navbar', $data);

        $EventsModel = new \App\Models\G3vent\EventsModel();
        $sx .= 'Editar Evento';
        $data = [];
        $data['event'] = $EventsModel->where('id_e', $id_e)->first();
        $sx .= view('G3vent/events/edit', $data);
        $sx .= view('G3vent/Headers/footer', $data);
        return $sx;
    }
}