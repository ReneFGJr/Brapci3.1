<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BrapciLabs\ResearchProjectModel;
use App\Models\BrapciLabs\CodebookModel;
use App\Models\BrapciLabs\ProjectAuthorModel;
use App\Models\BrapciLabs\RisModel;
use App\Models\BrapciLabs\Simori;
use Google\Service\BigtableAdmin\Split;

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
    protected $risModel;
    protected $session;
    protected $repository;

    public function __construct()
    {
        $this->projectModel = new ResearchProjectModel();
        $this->codebookModel = new CodebookModel();
        $this->projectAuthorModel = new ProjectAuthorModel();
        $this->risModel = new RisModel();
        $this->session      = session();
        $this->repository = new Simori();
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
        $data['worksCount'] = $this->risModel->countByProject($projectId); // implementar contagem de obras
        return view('BrapciLabs/home', $data);
    }

    /**** Authors */
    public function authors()
    {
        $BrapciAuthorityModel = new \App\Models\BrapciLabs\BrapciAuthorityModel();
        return $BrapciAuthorityModel->list();
    }

    public function check_ids()
    {
        $projectId = $this->projectModel->getProjectsID();
        $data = [
            'current' => $projectId,
            'project' => $this->projectModel->find($projectId),
        ];

        if (! $projectId) {
            return redirect()
                ->to('/labs/projects/select')
                ->with('error', 'Selecione um projeto para continuar.');
        }
        $checked = $this->projectAuthorModel->check_ids($projectId, $data);

        return $checked;
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
        $projectId = $this->projectModel->getProjectsID();
        $data = [
            'current'  => $this->session->get('project_id'),
            'codebook' => $this->codebookModel->find($id)
        ];

        return view('BrapciLabs/widget/codebook/show', $data);
    }

    /**
     * Lista projetos para seleção
     */

    public function new()
    {
        $data = [
            'title'   => 'Novo projeto',
            'action'  => site_url('labs/projects/create'),
            'project' => null
        ];

        return view('BrapciLabs/projects/form', $data);
    }

    public function edit(int $id)
    {
        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()
                ->to(site_url('labs/projects'))
                ->with('error', 'Projeto não encontrado');
        }

        $data = [
            'title'   => 'Editar projeto',
            'action'  => site_url('labs/projects/update/' . $id),
            'project' => $project
        ];

        return view('BrapciLabs/projects/form', $data);
    }

    public function create()
    {
        $user_id = $this->session->get('user_id');;
        $data = $this->request->getPost();
        $dt = [
            'project_name' => $data['project_name'],
            'description'  => $data['description'],
            'owner_id'      => $user_id // dono do projeto
        ];
        $dt = $this->projectModel->insert($dt);

        return redirect()
            ->to(site_url('labs'))
            ->with('success', 'Projeto criado com sucesso');
    }

    public function profile()
    {
        $data = [
            'title' => 'Configurações do usuário',
        ];
        $useID = $this->session->get('user_id'); // ajuste conforme seu auth
        $Socials = new \App\Models\Socials();
        $data = $Socials->where('id_us', $useID)->findAll();

        echo view('BrapciLabs/layout/header', $data);
        echo view('BrapciLabs/layout/sidebar');
        echo view('BrapciLabs/profile', $data);
        echo view('BrapciLabs/layout/footer');
    }

    public function update(int $id)
    {
        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()
                ->to(site_url('labs/projects'))
                ->with('error', 'Projeto não encontrado');
        }

        $data = $this->request->getPost();

        $this->projectModel->update($id, [
            'project_name' => $data['project_name'],
            'description'  => $data['description'],
            'status'       => $data['status']
        ]);

        return redirect()
            ->to(site_url('labs'))
            ->with('success', 'Projeto atualizado com sucesso');
    }

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

    /*************** WORKS */
    public function uploadRIS()
    {
        $projectId = session('project_id');
        if (! $projectId) {
            return redirect()
                ->to('/labs/projects/select')
                ->with('error', 'Selecione um projeto para continuar.');
        }

        $data = [
            'project_id' => $projectId,
            'title' => 'Importação RIS'
        ];

        echo view('BrapciLabs/layout/header', $data);
        echo view('BrapciLabs/layout/sidebar');
        echo view('BrapciLabs/ris/upload');
        echo view('BrapciLabs/layout/footer');
    }


    public function importRIS()
    {
        $projectId = session('project_id');

        if (! $projectId) {
            return redirect()
                ->to('/labs/projects/select')
                ->with('error', 'Selecione um projeto para continuar.');
        }

        $file = $this->request->getFile('ris_file');

        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Arquivo inválido');
        }

        // Leitura robusta do RIS (Windows/Linux/Mac)
        $content = file_get_contents($file->getTempName());
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines   = explode("\n", $content);

        $records = [];
        $current = [];

        foreach ($lines as $line) {

            $line = trim($line);

            // Fim do registro RIS
            if ($line === 'ER  -') {
                if (!empty($current)) {
                    $records[] = $current;
                }
                $current = [];
                continue;
            }

            // Campo RIS
            if (preg_match('/^([A-Z0-9]{2})  - (.*)$/', $line, $m)) {
                $current[$m[1]][] = trim($m[2]);
            }
        }

        $model    = new RisModel();
        $inserted = 0;
        $ignored  = 0;

        foreach ($records as $r) {

            $title    = $r['TI'][0] ?? null;
            $authors  = isset($r['AU']) ? implode('; ', $r['AU']) : null;
            $journal  = $r['JO'][0] ?? ($r['T2'][0] ?? null);
            $year     = isset($r['PY'][0]) ? (int) $r['PY'][0] : null;
            $doi      = $r['DO'][0] ?? null;
            $abstract = $r['AB'][0] ?? null;
            $type     = $r['TY'][0] ?? null;
            $url      = $r['UR'][0] ?? null;

            // 🔹 Keywords (KW)
            $keywords = isset($r['KW'])
                ? implode('; ', array_unique($r['KW']))
                : null;

            // Hash para deduplicação (por projeto)
            $hash = hash(
                'sha256',
                json_encode([$projectId, $title, $authors, $journal, $year, $doi])
            );

            if ($model->existsHash($hash, $projectId)) {
                $ignored++;
                continue;
            }

            $model->insert([
                'project_id' => $projectId,
                'ris_type'   => $type,
                'title'      => $title,
                'authors'    => $authors,
                'journal'    => $journal,
                'url'        => $url,
                'year'       => $year,
                'doi'        => $doi,
                'abstract'   => $abstract,
                'keywords'   => $keywords,
                'raw_hash'   => $hash
            ]);

            $inserted++;
        }

        return redirect()->back()->with(
            'success',
            "Importação concluída. Inseridos: {$inserted} | Ignorados (duplicados): {$ignored}"
        );
    }

    /**** Authority */
    public function index_authority($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '')
    {
        $BrapciAuthorityModel = new \App\Models\BrapciLabs\BrapciAuthorityModel();
        $ProjectAuthorModel = new \App\Models\BrapciLabs\ProjectAuthorModel();
        $data = [];
        echo view('BrapciLabs/layout/header', $data);
        echo view('BrapciLabs/layout/sidebar');

        switch ($d1) {
            case 'update':
                switch ($d2) {
                    case 'Brapci':
                        $data = $ProjectAuthorModel->where('id',$d3)->first();
                        if ($data['brapci_id']==0){
                            echo 'Autor sem BRAPCI ID.';
                            break;
                        } else {
                            $d3b = $data['brapci_id'];
                            $data = $BrapciAuthorityModel->updateFromApi($d3b);
                            echo $BrapciAuthorityModel->view($d3);
                        }
                        break;
                    default:
                        echo 'Fonte de autoridade desconhecida.';
                        break;
                }
                break;
            case 'view':
                echo $BrapciAuthorityModel->view($d2);
                break;
            default:
                $BrapciAuthorityModel = new \App\Models\BrapciLabs\BrapciAuthorityModel();
                return $BrapciAuthorityModel->list();
                break;
        }
        echo view('BrapciLabs/layout/footer');
    }

    /**** Works */
    public function index_works($d1='',$d2='',$d3='',$d4='',$d5='')
    {
        $BrapciWorksModel = new \App\Models\BrapciLabs\BrapciWorksModel();
        $data = [];
        echo view('BrapciLabs/layout/header', $data);
        echo view('BrapciLabs/layout/sidebar');

        switch($d1){
            case 'cloud_keys':
                $ResearchProjectModel = new \App\Models\BrapciLabs\ResearchProjectModel();
                $prj = $ResearchProjectModel->getProjectsID();
                echo $BrapciWorksModel->cloud_keys($prj);
                break;
            case 'view':
                echo $BrapciWorksModel->show_cited_work($d2);
                break;
            default:
                echo '<center>';
                echo $d1;
        }
        echo view('BrapciLabs/layout/footer');
    }

    public function works()
    {
        $ownerId   = 1; // temporário
        $projectId = $this->projectModel->getProjectsID();

        $q = $this->request->getGet('q');

        // =============================
        // MODEL PARA LISTAGEM + PAGINAÇÃO
        // =============================
        $model = new \App\Models\BrapciLabs\RisModel();
        $model->where('project_id', $projectId);
        $model->where('status >=', 0);

        if (!empty($q)) {
            $model->like('title', $q);
        }

        $model->orderBy('status, title', 'ASC');

        $data['works'] = $model->paginate(25);
        $data['pager'] = $model->pager;

        // =============================
        // MODEL PARA CONTAGEM TOTAL
        // =============================
        $countModel = new \App\Models\BrapciLabs\RisModel();
        $countModel->where('project_id', $projectId);

        if (!empty($q)) {
            $countModel->like('title', $q);
        }

        $data['total'] = $countModel->countAllResults();

        // =============================
        // OUTROS DADOS
        // =============================
        $data['q']       = $q;
        $data['current'] = $projectId;
        $data['project'] = $this->projectModel->find($projectId);

        echo view('BrapciLabs/layout/header', $data['project']);
        echo view('BrapciLabs/layout/sidebar');
        echo view('BrapciLabs/widget/works/index', $data);
        echo view('BrapciLabs/layout/footer');
    }

    /***************************** AI */
    public function AIwelcome($d1='',$d2='',$d3='',$d4='',$d5='')
    {
        $AiToolsModel = new \App\Models\BrapciLabs\AiToolsModel();
        $data = [
            'title' => 'AI Tools – Ferramentas de Inteligência Artificial',
        ];
        echo view('BrapciLabs/layout/header', $data);
        echo view('BrapciLabs/layout/sidebar');

        switch($d1){
            case 'view':
                echo $AiToolsModel->view($d2);
                break;
            default:
                $data = [
                    'title' => 'AI Tools – Ferramentas de Inteligência Artificial',
                    'tools' => $AiToolsModel->getAllOrdered()
                ];
                echo view('BrapciLabs/ai/welcome', $data);
                break;
        }


        echo view('BrapciLabs/layout/footer');
    }

    /**************************** OAI */
    public function OAIwelcome()
    {
        $Simori = new \App\Models\BrapciLabs\Simori();
        $dt = $Simori->findAll();
        $data = [
            'title' => 'OAI Harvest – Coletor OAI-PMH',
            'repositoryID' => $Simori->getProjectsID()
        ];

        echo view('BrapciLabs/oai/welcome', $data);
    }
    public function selectRepository()
{
    $model = new \App\Models\BrapciLabs\Simori();

    $data = [
        'repositories' => $model->findAll(),
    ];

    return view('BrapciLabs/oai/select_repository', $data);
}
    public function OAIlistarSets()
    {
        // URL OAI-PMH que retorna ListSets
        $oaiUrl = "https://www.lume.ufrgs.br/oai/request?verb=ListSets";

        // Faz a requisição
        $xmlString = @file_get_contents($oaiUrl);

        if ($xmlString === false) {
            return $this->response->setJSON([
                'error' => 'Não foi possível acessar o endpoint OAI'
            ])->setStatusCode(500);
        }

        // Converte XML em objeto
        $xml = simplexml_load_string($xmlString);

        if (!$xml) {
            return $this->response->setJSON([
                'error' => 'Erro ao parsear XML'
            ]);
        }

        // Namespace do OAI
        $namespaces = $xml->getNamespaces(true);

        // Acessa ListSets
        $sets = $xml->ListSets->set ?? [];

        $colecoes = [];

        foreach ($sets as $set) {
            $setSpec = (string) $set->setSpec;
            $setName = (string) $set->setName;

            $colecoes[] = [
                'setSpec' => $setSpec,
                'setName' => $setName
            ];
        }

        // Retorna JSON das coleções
        return $this->response->setJSON($colecoes);
    }
}
