<?php

namespace App\Controllers;

use CodeIgniter\Controller;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);

// Inicia a mesma sessão usada pelo /admin para disponibilizar o usuário autenticado.
$session = \Config\Services::session();

if (!defined('URL')) {
    define('URL', getenv('app.baseURL'));
}
if (!defined('PATH')) {
    define('PATH', rtrim((string) getenv('app.baseURL'), '/') . '/');
}
if (!defined('COLLECTION')) {
    define('COLLECTION', 'admin');
}

class Ojs extends Controller
{
    private $apiUrl    = 'https://editora.inma.gov.br/index.php/mbml/api/v1';
    private $apiToken  = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.WyJhMGQyYzEyMDMxOTM4MzU1Y2YzYTc0YjNhMmY1NTIzZDkwMTFhY2JiIl0.eYcvJZZrNEJf-vobUHndFJbgAbrx88V5YdTlJZbhF3E';

    public function index()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }
        // Página inicial com menu
        $data = [
            'page_title' => 'OJS - Submissoes',
            'bg' => 'bg-admin',
            'journal' => $this->getSelectedJournal(),
        ];

        return view('Brapci/Headers/header', $data)
            . view('Brapci/Headers/navbar', $data)
            . view('OJS/home', $data)
            . view('Brapci/Headers/footer', $data);
    }

    /**
     * Importa arquivo CSV de submissões
     */
    public function csv()
    {
        $articleModel = new \App\Models\OJS\ArticleModel();
        $status = $this->request->getGet('status');
        if ($status !== null && $status !== '') {
            $result = $articleModel->where('status', $status)->findAll();
        } else {
            $result = $articleModel->where('status', '0')->findAll();
        }

        // Totalização por status
        $statusTotals = $articleModel->select('status, COUNT(*) as total')->groupBy('status')->findAll();
        $totais = [];
        foreach ($statusTotals as $row) {
            $totais[$row['status']] = $row['total'];
        }

        return view('OJS/csv_result', [
            'result' => $result,
            'totais' => $totais
        ]);
    }

    public function submissoes()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        $submissoes = [];
        $apiError = null;

        if ($journal === null) {
            $apiError = 'Nenhuma revista ativa está selecionada. Selecione uma revista para consultar a API.';
        } else {
            try {
                $articleModel = new \App\Models\OJS\ArticleModel();
                $submissoes = $articleModel->getActiveSubmissions((int) $journal['id']);
            } catch (\RuntimeException $exception) {
                $apiError = $exception->getMessage();
            }
        }

        return $this->renderOjsPage('OJS/submissoes', [
            'page_title' => 'Submissões Ativas',
            'submissoes' => $submissoes,
            'journal' => $journal,
            'apiError' => $apiError,
        ]);
    }
    /**
     * Exibe dados importados do CSV e botão de confirmação
     */
    public function send()
    {
        $csv = $this->request->getPost('csv');
        $confirm = $this->request->getPost('confirm');

        if (!$csv) {
            return '<div class="alert alert-warning m-5">Nenhum dado recebido para submissão.</div>';
        }

        // Se confirmou, envia para o OJS
        if ($confirm) {
            $articleModel = new \App\Models\OJS\ArticleModel();
            // Monta o payload para o OJS
            $data = [
                'sectionId' => 1, // ou outro valor conforme necessário
                'title' => ['pt_BR' => $csv['title'] ?? ''],
                'locale' => 'pt_BR',
                'language' => 'pt_BR',
                'authors' => $csv['authors'] ?? '',
                'year' => $csv['Year'] ?? $csv['year'] ?? ''
            ];
            $rsp = $articleModel->createSubmissionFromCsv($data);

            // Atualiza registro na tabela se houver ID
            $id = $csv['ID'] ?? $csv['id'] ?? null;
            if ($id && isset($rsp['response']->id)) {
                $articleModel->update($id, [
                    'submit_id' => $rsp['response']->id,
                    'submit_data' => date('Y-m-d H:i:s'),
                    'status' => 1
                ]);
            }

            return view('OJS/send_result', [
                'csv' => $csv,
                'result' => $rsp
            ]);
        }

        // Senão, só mostra para confirmação
        return view('OJS/send_confirm', [
            'csv' => $csv
        ]);
    }

    /**
     * 0 - Submissão inicial (rota /ojs/send/0)
     */
    public function send0()
    {
        $articleModel = new \App\Models\OJS\ArticleModel();
        $csv = $this->request->getPost('csv');
        if ($csv) {
            // Envia submissão inicial para o OJS
            $rsp = $articleModel->submitToOJS($csv);

            $id = $csv['ID'] ?? $csv['id'] ?? null;
            if ($id && isset($rsp['httpCode']) && $rsp['httpCode'] == 200) {
                $dd = [];
                $idR = $csv['idR'] ?? null;
                $IID = $rsp['response']['id'];
                $dd['status'] = 1;
                $dd['submit_id'] = $IID;
                $dd['submit_data'] = date('Y-m-d H:i:s');
                $articleModel->set($dd)->where('idR', $idR)->update();
                $csv = $articleModel->where('idR', $idR)->first();

                $submitId = $rsp['response']['id'] ?? null;

                $title = $csv['Title'] ?? '';
                $rsp = $articleModel->updateTitleOJS($submitId, $title);
                $dd['status'] = 2;
                $articleModel->set($dd)->where('idR', $idR)->update();

                $RSP = $articleModel->addAuthors($submitId, $csv['Authors'] ?? '');
                $dd['status'] = 3;
                $articleModel->set($dd)->where('idR', $idR)->update();

                /************* `Phase 4` */
                $filePath = "../_Documments/OJS/modelo.pdf";
                if (!file_exists($filePath)) {
                    return '<div class="alert alert-danger m-5">Arquivo para upload não encontrado: ' . esc($filePath) . '</div>';
                }
                $rsp = $articleModel->uploadFileOJS($submitId, $filePath);
                $dd['status'] = 5;
                $articleModel->set($dd)->where('idR', $idR)->update();

                $articleModel->submitWithoutEmail($submitId);
                $articleModel->submitEditoracao($submitId);
                $dd['status'] = 10;
                $articleModel->set($dd)->where('idR', $idR)->update();

                return redirect()->to(base_url('ojs/csv?status=0'));

                return view('OJS/send_result', [
                'response' => $rsp,
                'csv' => $csv
                ]);
            }
        }
        // Se não houver dados, retorna confirmação
        return view('OJS/send_confirm', [
            'csv' => $csv
        ]);
    }

    public function nova()
    {
        // Redireciona para o formulário de nova submissão
        return view('OJS/form_upload');
    }

    public function send5()
    {
        $csv = $this->request->getPost('csv');
        $confirm = $this->request->getPost('confirm');
        if (!$csv) {
            return '<div class="alert alert-warning m-5">Nenhum dado recebido para atualização de título.</div>';
        }
        if ($confirm) {
            $submitId = $csv['submit_id'] ?? null;
            $idR = $csv['idR'] ?? null;
            $articleModel = new \App\Models\OJS\ArticleModel();
            $articleModel->submitWithoutEmail($submitId);
            $dd['status'] = 10;
            $articleModel->set($dd)->where('idR', $idR)->update();
            echo $articleModel->getlastquery();
            return redirect()->to(base_url('ojs/csv?status=0'));
        }
        return view('OJS/send_confirm', [
            'csv' => $csv
        ]);
    }
    /**
     * Atualizar título
     */
    public function send1()
    {
        $csv = $this->request->getPost('csv');
        $confirm = $this->request->getPost('confirm');
        if (!$csv) {
            return '<div class="alert alert-warning m-5">Nenhum dado recebido para atualização de título.</div>';
        }
        if ($confirm) {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $submitId = $csv['submit_id'] ?? null;
            $title = $csv['Title'] ?? '';
            $rsp = $articleModel->updateTitleOJS($submitId, $title);

            // Se sucesso, atualiza status para 2 e recarrega visualizador
            $id = $csv['ID'] ?? $csv['id'] ?? null;
            if ($id && isset($rsp['httpCode']) && $rsp['httpCode'] == 200) {
                $dd = [];
                $idR = $csv['idR'] ?? null;
                $dd['status'] = 2;
                $articleModel->set($dd)->where('idR', $idR)->update();
                // Redireciona para o visualizador para próxima fase
                return redirect()->to(base_url('ojs/csv?status=2'));
            }

            return view('OJS/send_result', [
                'csv' => $csv,
                'result' => $rsp
            ]);
        }
        return view('OJS/send_confirm', [
            'csv' => $csv
        ]);
    }

    /**
     * Atualizar autores
     */
    public function send2()
    {
        $csv = $this->request->getPost('csv');
        $confirm = $this->request->getPost('confirm');
        if (!$csv) {
            return '<div class="alert alert-warning m-5">Nenhum dado recebido para atualização de autores.</div>';
        }
        if ($confirm) {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $submitId = $csv['submit_id'] ?? null;
            $RSP = $articleModel->addAuthors($submitId, $csv['Authors'] ?? '');

            $dd = [];
            $idR = $csv['idR'] ?? null;
            $dd['status'] = 3;
            $articleModel->set($dd)->where('idR', $idR)->update();
            return redirect()->to(base_url('ojs/csv?status=3'));
        }
        return view('OJS/send_confirm', [
            'csv' => $csv
        ]);
    }

    /**
     * Enviar Arquivo
     */
    public function send3()
    {
        $csv = $this->request->getPost('csv');
        $confirm = $this->request->getPost('confirm');
        if (!$csv) {
            return '<div class="alert alert-warning m-5">Nenhum dado recebido para atualização de resumo.</div>';
        }
        if ($confirm) {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $submitId = $csv['submit_id'] ?? null;
            $filePath = "../_Documments/OJS/modelo.pdf";
            if (!file_exists($filePath)) {
                return '<div class="alert alert-danger m-5">Arquivo para upload não encontrado: ' . esc($filePath) . '</div>';
            }
            $rsp = $articleModel->uploadFileOJS($submitId, $filePath);

            $dd = [];
            $idR = $csv['idR'] ?? null;
            $dd['status'] = 5;
            $articleModel->set($dd)->where('idR', $idR)->update();
            return redirect()->to(base_url('ojs/csv?status=5'));
        }
        return view('OJS/send_confirm', [
            'csv' => $csv
        ]);
    }

    /**
     * Upload de arquivo
     */
    public function send4()
    {
        $csv = $this->request->getPost('csv');
        $confirm = $this->request->getPost('confirm');
        if (!$csv) {
            return '<div class="alert alert-warning m-5">Nenhum dado recebido para upload de arquivo.</div>';
        }
        if ($confirm) {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $submitId = $csv['submit_id'] ?? null;
            $filePath = $csv['file_path'] ?? '';
            $rsp = $articleModel->uploadFileOJS($submitId, $filePath);
            return view('OJS/send_result', [
                'csv' => $csv,
                'result' => $rsp
            ]);
        }
        return view('OJS/send_confirm', [
            'csv' => $csv
        ]);
    }

    public function editSubmittedArticle(int $articleId)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        if ($journal === null) {
            return redirect()->to(base_url('ojs/journals'))->with('error', 'Selecione uma revista.');
        }

        $model = new \App\Models\OJS\ArticleModel();
        $article = $model->getSubmittedArticle((int) $journal['id'], $articleId);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artigo submetido não encontrado.');
        }

        return $this->renderOjsPage('OJS/submitted_article_edit', [
            'page_title' => 'Editar artigo submetido',
            'journal' => $journal,
            'article' => $article,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function saveSubmittedArticle(int $articleId)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        if ($journal === null) {
            return redirect()->to(base_url('ojs/journals'))->with('error', 'Selecione uma revista.');
        }

        $model = new \App\Models\OJS\ArticleModel();
        if ($model->getSubmittedArticle((int) $journal['id'], $articleId) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artigo submetido não encontrado.');
        }

        $year = trim((string) $this->request->getPost('Year'));
        if ($year !== '' && !preg_match('/^\d{4}$/', $year)) {
            return redirect()->back()->withInput()->with('errors', ['Informe um ano válido com quatro dígitos.']);
        }

        $data = [];
        foreach (['Title', 'Authors', 'Affiliation', 'Year', 'Vol', 'Num', 'PagINI', 'PagEND', 'Keywords'] as $field) {
            $data[$field] = trim((string) $this->request->getPost($field));
        }
        if ($data['Title'] === '') {
            return redirect()->back()->withInput()->with('errors', ['Informe o título do artigo.']);
        }

        $model->updateSubmittedArticle((int) $journal['id'], $articleId, $data);

        return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
            ->with('success', 'Dados da tabela Article atualizados com sucesso.');
    }
    public function updateSubmittedArticleOjs(int $articleId)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        if ($journal === null) {
            return redirect()->to(base_url('ojs/journals'))->with('error', 'Selecione uma revista.');
        }

        $model = new \App\Models\OJS\ArticleModel();
        $article = $model->getSubmittedArticle((int) $journal['id'], $articleId);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artigo submetido não encontrado.');
        }

        try {
            $result = $model->updateOjsSubmissionFromArticle((int) $journal['id'], $article);
            if (!$result['success']) {
                throw new \RuntimeException($result['error'] ?: 'O OJS recusou a atualização (HTTP ' . $result['http_code'] . ').');
            }
            $authorCount = (int) ($result['authors']['processed'] ?? 0);
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('success', 'Dados da submissão atualizados no OJS. Autores processados: ' . $authorCount . '.');
        } catch (\RuntimeException $exception) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', $exception->getMessage());
        }
    }

    public function updateSubmittedArticleLocal(int $articleId)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        if ($journal === null) {
            return redirect()->to(base_url('ojs/journals'))->with('error', 'Selecione uma revista.');
        }

        $model = new \App\Models\OJS\ArticleModel();
        $article = $model->getSubmittedArticle((int) $journal['id'], $articleId);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artigo submetido não encontrado.');
        }

        try {
            $fields = $model->updateArticleFromOjs((int) $journal['id'], $article);
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('success', 'Tabela Article atualizada: ' . implode(', ', array_keys($fields)) . '.');
        } catch (\RuntimeException $exception) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', $exception->getMessage());
        }
    }
    public function submittedArticleView(int $articleId)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        if ($journal === null) {
            return redirect()->to(base_url('ojs/journals'))
                ->with('error', 'Selecione uma revista para visualizar a submissão.');
        }

        $articleModel = new \App\Models\OJS\ArticleModel();
        $article = $articleModel->getSubmittedArticle((int) $journal['id'], $articleId);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artigo submetido não encontrado.');
        }

        $submission = [];
        $apiError = null;
        try {
            $submission = $articleModel->getSubmissionDetails(
                (int) $journal['id'],
                (int) $article['journal_submit_id']
            );
        } catch (\RuntimeException $exception) {
            $apiError = $exception->getMessage();
        }

        return $this->renderOjsPage('OJS/submitted_article_view', [
            'page_title' => 'Visualizar submissão',
            'journal' => $journal,
            'article' => $article,
            'submission' => $submission,
            'apiError' => $apiError,
        ]);
    }
    public function articlesSubmied()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        $articles = [];
        $error = null;
        $yearInput = trim((string) $this->request->getGet('year'));
        $selectedYear = preg_match('/^\d{4}$/', $yearInput) ? $yearInput : null;

        if ($yearInput !== '' && $selectedYear === null) {
            $error = 'Informe um ano válido com quatro dígitos.';
        } elseif ($journal === null) {
            $error = 'Nenhuma revista ativa está selecionada. Selecione uma revista para listar os artigos submetidos.';
        } else {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $articles = $articleModel->getSubmittedArticles((int) $journal['id'], $selectedYear);
        }

        return $this->renderOjsPage('OJS/articles_submied', [
            'page_title' => 'Artigos submetidos',
            'journal' => $journal,
            'articles' => $articles,
            'error' => $error,
            'selectedYear' => $yearInput,
        ]);
    }
    public function articlesToSubmit()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        $articles = [];
        $error = null;
        $yearInput = trim((string) $this->request->getGet('year'));
        $selectedYear = preg_match('/^\d{4}$/', $yearInput) ? $yearInput : null;

        if ($yearInput !== '' && $selectedYear === null) {
            $error = 'Informe um ano válido com quatro dígitos.';
        } elseif ($journal === null) {
            $error = 'Nenhuma revista ativa está selecionada. Selecione uma revista para listar os artigos.';
        } else {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $articles = $articleModel->getArticlesToSubmit((int) $journal['id'], $selectedYear);
        }

        return $this->renderOjsPage('OJS/articles_to_submit', [
            'page_title' => 'Artigos para submissão',
            'journal' => $journal,
            'articles' => $articles,
            'error' => $error,
            'selectedYear' => $yearInput,
        ]);
    }
    public function submit()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        if ($journal === null) {
            return redirect()->to(base_url('ojs/articles_to_submit'))
                ->with('error', 'Selecione uma revista antes de submeter artigos.');
        }

        $selectedIds = $this->request->getPost('articles');
        if (!is_array($selectedIds) || $selectedIds === []) {
            return redirect()->to(base_url('ojs/articles_to_submit'))
                ->with('error', 'Selecione pelo menos um artigo para submeter.');
        }

        $articleModel = new \App\Models\OJS\ArticleModel();
        $articles = $articleModel->getArticlesForSubmission((int) $journal['id'], $selectedIds);
        if ($articles === []) {
            return redirect()->to(base_url('ojs/articles_to_submit'))
                ->with('error', 'Nenhum dos artigos selecionados está disponível para submissão.');
        }

        $results = [];
        foreach ($articles as $article) {
            try {
                $results[] = $articleModel->createDraftSubmission((int) $journal['id'], $article);
            } catch (\RuntimeException $exception) {
                $results[] = [
                    'article_id' => (int) $article['idR'],
                    'title' => (string) ($article['Title'] ?? ''),
                    'payload' => [],
                    'http_code' => 0,
                    'success' => false,
                    'submission_id' => null,
                    'response' => [],
                    'error' => $exception->getMessage(),
                ];
            }
        }

        return $this->renderOjsPage('OJS/submit', [
            'page_title' => 'Submeter artigos no OJS',
            'journal' => $journal,
            'results' => $results,
        ]);
    }
    public function issues()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        $publishedIssues = [];
        $openIssues = [];
        $apiError = null;

        if ($journal === null) {
            $apiError = 'Nenhuma revista ativa está selecionada. Selecione uma revista para consultar as edições.';
        } else {
            try {
                $articleModel = new \App\Models\OJS\ArticleModel();
                $publishedIssues = $articleModel->getIssues((int) $journal['id'], true);
                $openIssues = $articleModel->getIssues((int) $journal['id'], false);
            } catch (\RuntimeException $exception) {
                $apiError = $exception->getMessage();
            }
        }

        return $this->renderOjsPage('OJS/issues', [
            'page_title' => 'Edições do OJS',
            'journal' => $journal,
            'publishedIssues' => $publishedIssues,
            'openIssues' => $openIssues,
            'apiError' => $apiError,
        ]);
    }
    public function journals()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $model = new \App\Models\OJS\JournalModel();

        return $this->renderOjsPage('OJS/journals/index', [
            'page_title' => 'Revistas OJS',
            'journals' => $model->orderBy('name', 'ASC')->findAll(),
            'selectedJournalId' => (int) ($this->request->getCookie('ojs_journal_id') ?? 0),
        ]);
    }

    public function journalView(int $id)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $model = new \App\Models\OJS\JournalModel();
        $journal = $model->find($id);
        if ($journal === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Revista OJS não encontrada.');
        }

        unset($journal['api_key']);

        return $this->renderOjsPage('OJS/journals/view', [
            'page_title' => 'Revista OJS',
            'journal' => $journal,
        ]);
    }
    public function journalNew()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        return $this->renderOjsPage('OJS/journals/form', [
            'page_title' => 'Nova revista OJS',
            'journal' => [],
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function journalCreate()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $model = new \App\Models\OJS\JournalModel();
        $data = $this->journalPostData(true);

        if (!$model->saveJournal($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(base_url('ojs/journals'))->with('success', 'Revista cadastrada com sucesso.');
    }

    public function journalEdit(int $id)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $model = new \App\Models\OJS\JournalModel();
        $journal = $model->find($id);
        if ($journal === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Revista OJS não encontrada.');
        }

        unset($journal['api_key']);

        return $this->renderOjsPage('OJS/journals/form', [
            'page_title' => 'Editar revista OJS',
            'journal' => $journal,
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function journalUpdate(int $id)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $model = new \App\Models\OJS\JournalModel();
        if ($model->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Revista OJS não encontrada.');
        }

        $data = $this->journalPostData(false);
        $data['id'] = $id;

        if (!$model->saveJournal($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(base_url('ojs/journals'))->with('success', 'Revista atualizada com sucesso.');
    }

    public function journalSelect(int $id)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $model = new \App\Models\OJS\JournalModel();
        $journal = $model->find($id);
        if ($journal === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Revista OJS não encontrada.');
        }

        if (empty($journal['active'])) {
            return redirect()->to(base_url('ojs/journals'))
                ->with('error', 'Uma revista inativa não pode ser selecionada.');
        }

        $response = redirect()->to(base_url('ojs/journals'))
            ->with('success', 'Revista selecionada: ' . $journal['name'] . '.');
        $response->setCookie('ojs_journal_id', (string) $id, 60 * 60 * 24 * 30, '', '/', '', null, true, 'Lax');

        return $response;
    }
    public function journalDelete(int $id)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $model = new \App\Models\OJS\JournalModel();
        if ($model->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Revista OJS não encontrada.');
        }

        $model->delete($id);

        return redirect()->to(base_url('ojs/journals'))->with('success', 'Revista excluída com sucesso.');
    }

    private function journalPostData(bool $requireApiKey): array
    {
        $apiKey = trim((string) $this->request->getPost('api_key'));
        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'acronym' => trim((string) $this->request->getPost('acronym')),
            'base_url' => rtrim(trim((string) $this->request->getPost('base_url')), '/'),
            'context_path' => trim((string) $this->request->getPost('context_path'), '/'),
            'is_default' => $this->request->getPost('is_default') ? 1 : 0,
            'active' => $this->request->getPost('active') ? 1 : 0,
        ];

        if ($requireApiKey || $apiKey !== '') {
            $data['api_key'] = $apiKey;
        }

        return $data;
    }

    private function getSelectedJournal(): ?array
    {
        $journalId = (int) ($this->request->getCookie('ojs_journal_id') ?? 0);
        if ($journalId <= 0) {
            return null;
        }

        $journal = (new \App\Models\OJS\JournalModel())
            ->where('id', $journalId)
            ->where('active', 1)
            ->first();

        if ($journal === null) {
            return null;
        }

        unset($journal['api_key']);
        return $journal;
    }
    private function hasJournalAccess(): bool
    {
        return (new \App\Models\Socials())->getAccess('#ADM#CAT');
    }

    private function renderOjsPage(string $view, array $data = []): string
    {
        $data['bg'] = 'bg-admin';

        return view('Brapci/Headers/header', $data)
            . view('Brapci/Headers/navbar', $data)
            . view($view, $data)
            . view('Brapci/Headers/footer', $data);
    }}
