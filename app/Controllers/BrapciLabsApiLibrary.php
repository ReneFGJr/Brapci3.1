<?php

namespace App\Controllers;

use App\Models\ApiLibraryModel;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie', 'highchart', 'text']);
$session = \Config\Services::session();


class BrapciLabsApiLibrary extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new \App\Models\BrapciLabs\ApiLibraryModel();
    }

    /** LISTAGEM */
    public function index()
    {
        $data['apis'] = $this->model->orderBy('nome')->findAll();
        $html = '';
        $html = view('BrapciLabs/layout/header');
        $html .= view('BrapciLabs/layout/sidebar');
        $html .= '<div class="content">';
        $html .= view('BrapciLabs/api_library/index', $data);
        $html .= '</div>';
        return $html;
    }

    /** FORM NOVO */
    public function create()
    {
        $html = '';
        $html = view('BrapciLabs/layout/header');
        $html .= view('BrapciLabs/layout/sidebar');
        $html .= '<div class="content">';
        $html .= view('BrapciLabs/api_library/form');
        $html .= '</div>';
        return $html;
    }

    /** SALVAR */
    public function store()
    {
        $this->model->save($this->request->getPost());
        return redirect()->to('/labs/api-library')->with('success', 'API cadastrada');
    }

    /** EDITAR */
    public function edit($id)
    {
        $data['api'] = $this->model->find($id);
        $html = '';
        $html = view('BrapciLabs/layout/header');
        $html .= view('BrapciLabs/layout/sidebar');
        $html .= '<div class="content">';
        $html .= view('BrapciLabs/api_library/form', $data);
        $html .= '</div>';
        return $html;
    }

    /** ATUALIZAR */
    public function update($id)
    {
        $data = $this->request->getPost();
        $data['id'] = $id;

        $this->model->save($data);
        return redirect()->to('/labs/api-library')->with('success', 'API atualizada');
    }

    /** EXCLUIR */
    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->to('/labs/api-library')->with('success', 'API removida');
    }

    /** VISUALIZAR */
    public function show($id)
    {
        $data['api'] = $this->model->find($id);
        $html = '';
        $html = view('BrapciLabs/layout/header');
        $html .= view('BrapciLabs/layout/sidebar');
        $html .= '<div class="content">';
        $html .= view('BrapciLabs/api_library/show', $data);
        $html .= '</div>';
        return $html;
    }
}
