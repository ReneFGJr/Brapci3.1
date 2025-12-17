<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BrapciLabs\ResearchProjectModel;
use App\Models\BrapciLabs\CodebookModel;

/* SESSION */

$language = \Config\Services::language();

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie', 'highchart', 'text']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . getenv("app.baseURL.prefix"));
define("MODULE", '');
define("PREFIX", '');
define("LIBRARY", '0000');
define("COLLECTION", '/benancib');

class BrapciLab extends BaseController
{
    protected $projectModel;
    protected $codebookModel;
    protected $session;

    public function __construct()
    {
        $this->projectModel = new ResearchProjectModel();
        $this->codebookModel = new CodebookModel();
        $this->session      = session();
    }

    public function home()
    {
        $data = [];

        $projectId = session('project_id');
        if ($projectId) {
            $data['project'] = $projectId
                ? $this->projectModel->find($projectId)
                : null;
        }

        return view('BrapciLabs/home', $data);
    }

    /**
     * Lista projetos para seleção
     */
    public function codebook()
    {
        $ownerId = $this->session->get('user_id'); // ajuste conforme seu auth
        $ownerId = 1; // temporário
        $projectId = session('project_id');
        $data = [
            'projects' => $this->projectModel->getByOwner($ownerId),
            'current'  => $this->session->get('project_id'),
            'codebooks' => $this->codebookModel->getByProject($projectId)
        ];

        return view('brapcilabs/widget/codebook/view', $data);
    }

    /**
     * Lista projetos para seleção
     */
    public function selectProject()
    {
        $ownerId = $this->session->get('user_id'); // ajuste conforme seu auth
        $ownerId = 1; // temporário
        $data = [
            'projects' => $this->projectModel->getByOwner($ownerId),
            'current'  => $this->session->get('project_id')
        ];

        return view('brapcilabs/projects/select', $data);
    }

    public function setProject()
    {
        $projectId = $this->request->getPost('project_id');

        if (!$projectId) {
            return redirect()->back()->with('error', 'Projeto inválido.');
        }

        $this->session->set('project_id', (int) $projectId);

        return redirect()->to('/labs')->with('success', 'Projeto selecionado com sucesso.');
    }

    public function projects()
    {
        $data = [];
        $ResearchProjectModel = new \App\Models\BrapciLabs\ResearchProjectModel();
        $data['projects'] = $ResearchProjectModel->findAll();
        return view('BrapciLabs/projects', $data);
    }
}
