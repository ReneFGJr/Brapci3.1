<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BrapciLabs\ResearchProjectModel;
use App\Models\BrapciLabs\CodebookModel;
use App\Models\BrapciLabs\ProjectAuthorModel;

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
    protected $projectAuthorModel;
    protected $session;

    public function __construct()
    {
        $this->projectModel = new ResearchProjectModel();
        $this->codebookModel = new CodebookModel();
        $this->projectAuthorModel = new ProjectAuthorModel();
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

        if ($projectId === null) {
            return redirect()
                ->to('/labs/projects/select')
                ->with('error', 'Selecione um projeto para continuar.');
        }

        $data['codebookCount'] = $this->codebookModel->countByProject($projectId);
        $data['authorsCount'] = $this->projectAuthorModel->countByProject($projectId); // implementar contagem de autores

        return view('BrapciLabs/home', $data);
    }

    private function getProjectsID()
    {
        $projectId = session('project_id');
        if ($projectId) {
            return $projectId;
        } else {
            return 0;
        }

    }

    /**** authors */
    public function authors()
    {
        $ownerId  = $this->session->get('user_id');
        $ownerId  = 1; // temporário
        $projectId = $this->getProjectsID();

        $q = $this->request->getGet('q');

        // =============================
        // BUILDER PARA PAGINAÇÃO
        // =============================
        $model = $this->projectAuthorModel;
        $model->where('project_id', $projectId);

        if (!empty($q)) {
            $model->like('nome', $q);
        }
        $model->orderBy('nome', 'ASC');

        $authors = $model->paginate(25);
        $pager   = $model->pager;

        // =============================
        // BUILDER PARA CONTAGEM TOTAL
        // =============================
        $countModel = clone $this->projectAuthorModel;
        $countModel->where('project_id', $projectId);

        if (!empty($q)) {
            $countModel->like('nome', $q);
        }

        $total = $countModel->countAllResults();

        // =============================
        // DATA
        // =============================
        $data = [
            'current' => $projectId,
            'project' => $this->projectModel->find($projectId),
            'authors' => $authors,
            'pager'   => $pager,
            'q'       => $q,
            'total'   => $total
        ];

        return view('BrapciLabs/widget/authors/view', $data);
    }

    public function check_ids()
    {
        $projectId = session('project_id');

        if (! $projectId) {
            return redirect()
                ->to('/labs/projects/select')
                ->with('error', 'Selecione um projeto para continuar.');
        }
        $removed = $this->projectAuthorModel->check_ids($projectId);

        return redirect()
            ->to('/labs/project/authors')
            ->with('success', "Autores duplicados removidos: {$removed}.");
    }


    public function authors_import($id = null)
    {
        $projectId = session('project_id');

        if (! $projectId) {
            return redirect()
                ->to('/labs/projects/select')
                ->with('error', 'Selecione um projeto para continuar.');
        }

        $text = trim($this->request->getPost('authors'));

        if (empty($text)) {
            return redirect()->back()
                ->with('error', 'Nenhum autor informado.');
        }

        $lines = preg_split('/\r\n|\r|\n/', $text);

        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($lines as $lineNumber => $line) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            // Esperado: Nome | lattes_id | brapci_id
            $parts = array_map('trim', explode('|', $line));

            if (count($parts) < 3) {
                $parts = [$parts[0],'',0];
            }

            [$nome, $lattesId, $brapciId] = $parts;

            /* ===============================
           VALIDAÇÃO DO NOME
        =============================== */
            if (!preg_match('/^[\p{L}\s]+$/u', $nome)) {
                $errors[] = "Linha " . ($lineNumber + 1) . ": nome do autor inválido (use apenas letras e espaços).";
                continue;
            }

            /* ===============================
           VALIDAÇÃO DO BRAPCI ID
        =============================== */
            // Evita duplicação no projeto
            $nome = nbr_author($nome,7);
            if ($this->projectAuthorModel->existsInProject((string)$nome, $projectId)) {
                $skipped++;
                continue;
            }

            $data = [
                'nome'       => $nome,
                'project_id' => $projectId
            ];

            $this->projectAuthorModel->set($data)->insert();
            $inserted++;
        }

        /* ===============================
       FEEDBACK AO USUÁRIO
    =============================== */
        if (!empty($errors)) {
            session()->setFlashdata('warning', implode('<br>', $errors));
        }

        return redirect()
            ->to('/labs/project/authors')
            ->with('success', "Autores inseridos: {$inserted}. Ignorados: {$skipped}.");
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
            'current'  => $projectId,
            'codebooks' => $this->codebookModel->getByProject($projectId)
        ];

        return view('BrapciLabs/widget/codebook/view', $data);
    }

    /* ===============================
       INCLUIR
    =============================== */
    public function newCodebook()
    {
        return view('BrapciLabs/widget/codebook/form', [
            'action' => 'create'
        ]);
    }

    public function createCodebook()
    {
        $tags = $this->request->getPost('tags');
        $tags = array_map('trim', explode(',', $tags));

        $this->codebookModel->insert([
            'project_id' => session('project_id'),
            'title'      => $this->request->getPost('title'),
            'content'    => $this->request->getPost('content'),
            'tags'       => json_encode($tags),
            'created_by' => session('user_id')
        ]);

        return redirect()->to('/labs/project/codebook')
            ->with('success', 'Anotação criada com sucesso.');
    }

    /* ===============================
       EDITAR
    =============================== */
    public function editCodebook($id)
    {
        $projectId = session('project_id');

        return view('BrapciLabs/widget/codebook/form', [
            'codebook' => $this->codebookModel->getOne($id, $projectId),
            'action'   => 'update'
        ]);
    }

    public function updateCodebook($id)
    {
        $tags = $this->request->getPost('tags');
        $tags = array_map('trim', explode(',', $tags));
        $this->codebookModel->update($id, [
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'tags'    => json_encode($tags)
        ]);

        return redirect()->to('/labs/project/codebook/view/' . $id)
            ->with('success', 'Anotação atualizada.');
    }

    /* ===============================
       EXCLUIR
    =============================== */
    public function deleteCodebook($id)
    {
        $this->codebookModel->delete($id);

        return redirect()->to('/labs/project/codebook')
            ->with('success', 'Anotação excluída.');
    }



    public function codebook_view($id = null)
    {
        $ownerId = $this->session->get('user_id'); // ajuste conforme seu auth
        $ownerId = 1; // temporário
        $projectId = $this->getProjectsID();
        $data = [
            'current'  => $this->session->get('project_id'),
            'codebook' => $this->codebookModel->find($id)
        ];

        return view('BrapciLabs/widget/codebook/show', $data);
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

        return view('BrapciLabs/projects/select', $data);
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
