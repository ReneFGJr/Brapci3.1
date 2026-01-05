<?php

namespace App\Controllers;

use App\Models\Events\EventModel;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
helper('meta');
$session = \Config\Services::session();

class Event extends BaseController
{
    protected $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
    }

    private function headerPage($title = 'Eventos')
    {
        $data['page_title'] = $title;
        $data['metadata'] = meta([
            ['name' => 'description', 'content' => 'Eventos do Brapci'],
            ['name' => 'keywords', 'content' => 'eventos, brapci, conferências, workshops']
        ]);
        return view('Brapci/Headers/header2026', $data) .
            view('Brapci/Headers/navbar2026', $data);
    }

    public function index()
    {


        return $this->headerPage() . view('Events/index', [
            'events' => $this->eventModel->orderBy('ev_data_start', 'DESC')->findAll()
        ]);
    }

    public function create()
    {
        return view('Events/form');
    }

    public function store()
    {
        $imageName = null;

        $file = $this->request->getFile('ev_image');
        if ($file && $file->isValid()) {
            $imageName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/events', $imageName);
        }

        $this->eventModel->save([
            'ev_name'        => $this->request->getPost('ev_name'),
            'ev_place'       => $this->request->getPost('ev_place'),
            'ev_ative'       => $this->request->getPost('ev_ative') ?? 1,
            'ev_permanent'   => $this->request->getPost('ev_permanent') ?? 0,
            'ev_data_start'  => $this->request->getPost('ev_data_start'),
            'ev_data_end'    => $this->request->getPost('ev_data_end') ?? '1900-01-01',
            'ev_deadline'    => $this->request->getPost('ev_deadline') ?? 0,
            'ev_url'         => $this->request->getPost('ev_url'),
            'ev_description' => $this->request->getPost('ev_description'),
            'ev_image'       => $imageName
        ]);

        return redirect()->to('/events')->with('success', 'Evento criado');
    }

    public function edit($id)
    {
        return $this->headerPage() . view('Events/form', [
            'event' => $this->eventModel->find($id)
        ]);
    }

    public function update($id)
    {
        $event = $this->eventModel->find($id);
        $imageName = $event['ev_image'];

        $file = $this->request->getFile('ev_image');
        if ($file && $file->isValid()) {
            if ($imageName && file_exists(FCPATH . '_repository/events/' . $imageName)) {
                unlink(FCPATH . '_repository/events/' . $imageName);
            }
            $imageName = $file->getRandomName();
            $file->move(FCPATH . '_repository/events', $imageName);
        }

        $this->eventModel->update($id, [
            'ev_name'        => $this->request->getPost('ev_name'),
            'ev_place'       => $this->request->getPost('ev_place'),
            'ev_ative'       => $this->request->getPost('ev_ative'),
            'ev_permanent'   => $this->request->getPost('ev_permanent'),
            'ev_data_start'  => $this->request->getPost('ev_data_start'),
            'ev_data_end'    => $this->request->getPost('ev_data_end'),
            'ev_deadline'    => $this->request->getPost('ev_deadline'),
            'ev_url'         => $this->request->getPost('ev_url'),
            'ev_description' => $this->request->getPost('ev_description'),
            'ev_image'       => $imageName
        ]);

        return redirect()->to('/events')->with('success', 'Evento atualizado');
    }

    public function delete($id)
    {
        $event = $this->eventModel->find($id);

        if ($event['ev_image'] && file_exists(FCPATH . 'uploads/events/' . $event['ev_image'])) {
            unlink(FCPATH . 'uploads/events/' . $event['ev_image']);
        }

        $this->eventModel->delete($id);
        return redirect()->to('/events')->with('success', 'Evento removido');
    }
}
