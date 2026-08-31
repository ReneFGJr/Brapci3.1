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
        return view('Authority/form_upload');
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
                $ojsResponse = [
                    'httpCode' => $result['file']['http_code'] ?? $result['http_code'] ?? 0,
                    'fileStage' => $result['file']['file_stage'] ?? null,
                    'response' => array_key_exists('file', $result)
                        ? ($result['file']['response'] ?? null)
                        : ($result['response'] ?? null),
                    'raw' => $result['file']['raw'] ?? null,
                    'curlError' => $result['file']['curl_error'] ?? null,
                ];
                return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                    ->with('error', $result['error'] ?: 'O OJS recusou a atualização (HTTP ' . $result['http_code'] . ').')
                    ->with('ojs_response', json_encode($ojsResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
            $authorCount = (int) ($result['authors']['processed'] ?? 0);
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('success', 'Dados da submissão atualizados no OJS. Autores processados: ' . $authorCount . '.');
        } catch (\RuntimeException $exception) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', $exception->getMessage());
        }
    }

    public function sendSubmittedArticleToReview(int $articleId)
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

        if (!in_array((int) ($article['status'] ?? 0), [2, 3], true)) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', 'Somente artigos submetidos ou em avaliação podem ser enviados para Avaliação.');
        }

        try {
            $submissionId = (int) $article['journal_submit_id'];
            $submission = $model->getSubmissionDetails((int) $journal['id'], $submissionId);
            if ((int) ($submission['status'] ?? 0) !== 1) {
                return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                    ->with('error', 'A submissão precisa estar em fluxo editorial no OJS.');
            }

            if ((int) ($submission['stageId'] ?? 0) === 3) {
                if (!$model->markArticleAsInEvaluation((int) $journal['id'], $articleId)) {
                    throw new \RuntimeException('Não foi possível confirmar o status local 3 para o artigo.');
                }

                return redirect()->to(base_url('ojs/avaliation_in'))
                    ->with('success', 'A submissão já está na etapa de Avaliação no OJS.');
            }

            if ((int) ($submission['stageId'] ?? 0) !== 1) {
                return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                    ->with('error', 'A submissão precisa estar na etapa de Submissão para ser enviada à Avaliação.');
            }

            $result = $model->sendToReview((int) $journal['id'], $submissionId);
            $httpCode = (int) ($result['httpCode'] ?? 0);
            if (!in_array($httpCode, [200, 201], true)) {
                $details = $result['response']['errorMessage']
                    ?? $result['response']['error']
                    ?? $result['row']
                    ?? $result['curl_error']
                    ?? null;
                if (is_array($details)) {
                    $details = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                throw new \RuntimeException(
                    'O OJS recusou o envio para Avaliação (HTTP ' . $httpCode . ').'
                    . ($details ? ' ' . trim((string) $details) : '')
                );
            }

            $updatedSubmission = $model->getSubmissionDetails((int) $journal['id'], $submissionId);
            if ((int) ($updatedSubmission['stageId'] ?? 0) !== 3) {
                throw new \RuntimeException(
                    'O OJS respondeu com sucesso, mas a submissão permaneceu fora da etapa de Avaliação.'
                );
            }

            if (!$model->markArticleAsInEvaluation((int) $journal['id'], $articleId)) {
                throw new \RuntimeException(
                    'A submissão foi enviada para Avaliação no OJS, mas não foi possível alterar o status local para 3.'
                );
            }

            return redirect()->to(base_url('ojs/avaliation_in'))
                ->with('success', 'Submissão enviada para a etapa de Avaliação no OJS.');
        } catch (\RuntimeException $exception) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', $exception->getMessage());
        }
    }
    public function sendSubmittedArticleToCopyediting(int $articleId)
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

        if ((int) ($article['status'] ?? 0) !== 3) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', 'Somente artigos com status 3 podem ser enviados para Edição de Texto.');
        }

        try {
            $submissionId = (int) $article['journal_submit_id'];
            $submission = $model->getSubmissionDetails((int) $journal['id'], $submissionId, true);
            if ((int) ($submission['status'] ?? 0) !== 1 || (int) ($submission['stageId'] ?? 0) !== 3) {
                return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                    ->with('error', 'A submissão precisa estar na etapa de Avaliação do fluxo editorial do OJS.');
            }

            $reviewRoundId = $model->getLatestExternalReviewRoundId($submission);
            if ($reviewRoundId <= 0) {
                throw new \RuntimeException('O OJS não retornou uma rodada de avaliação válida para esta submissão.');
            }

            $result = $model->acceptSubmission((int) $journal['id'], $submissionId, $reviewRoundId);
            $httpCode = (int) ($result['httpCode'] ?? 0);
            if (!in_array($httpCode, [200, 201], true)) {
                $details = $result['response']['errorMessage']
                    ?? $result['response']['error']
                    ?? $result['row']
                    ?? $result['curl_error']
                    ?? null;
                if (is_array($details)) {
                    $details = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                throw new \RuntimeException(
                    'O OJS recusou a aceitação da submissão (HTTP ' . $httpCode . ').'
                    . ($details ? ' ' . trim((string) $details) : '')
                );
            }

            $updatedSubmission = $model->getSubmissionDetails((int) $journal['id'], $submissionId, true);
            if ((int) ($updatedSubmission['stageId'] ?? 0) !== 4) {
                throw new \RuntimeException(
                    'O OJS respondeu com sucesso, mas a submissão não avançou para Edição de Texto.'
                );
            }
            if (!$model->markArticleAsSentToCopyediting((int) $journal['id'], $articleId)) {
                throw new \RuntimeException(
                    'A submissão foi aceita no OJS, mas não foi possível alterar o status local para 4.'
                );
            }

            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('success', 'Submissão aceita no OJS e enviada para Edição de Texto. Status local alterado para 4.');
        } catch (\RuntimeException $exception) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', $exception->getMessage());
        }
    }
    public function uploadSubmittedArticlePdf(int $articleId)
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

        $articlePdf = $this->findSubmittedArticlePdf($article);
        if ($articlePdf['path'] === null) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', 'O PDF do artigo não foi encontrado: ' . $articlePdf['expected']);
        }

        try {
            $submission = $model->getSubmissionDetails(
                (int) $journal['id'],
                (int) $article['journal_submit_id']
            );
            if ((int) ($submission['status'] ?? 0) !== 1) {
                return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                    ->with('error', 'O arquivo só pode ser enviado quando a submissão estiver em fluxo editorial.');
            }

            $result = $model->uploadFileOJS(
                (int) $article['journal_submit_id'],
                $articlePdf['path'],
                2
            );
            $httpCode = (int) ($result['httpCode'] ?? 0);
            if (!in_array($httpCode, [200, 201], true)) {
                $ojsResponse = [
                    'httpCode' => $httpCode,
                    'fileStage' => 2,
                    'response' => $result['response'] ?? null,
                    'raw' => $result['raw'] ?? null,
                    'curlError' => $result['curl_error'] ?? null,
                ];
                return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                    ->with('error', 'O OJS recusou o arquivo PDF (HTTP ' . $httpCode . ').')
                    ->with('ojs_response', json_encode($ojsResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('success', 'Arquivo enviado ao OJS com sucesso: ' . $articlePdf['name'] . '.');
        } catch (\RuntimeException $exception) {
            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('error', $exception->getMessage());
        }
    }
    public function finalizeSubmittedArticle(int $articleId)
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
            $submission = $model->getSubmissionDetails(
                (int) $journal['id'],
                (int) $article['journal_submit_id']
            );
            if ((int) ($submission['status'] ?? 0) !== 1) {
                return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                    ->with('error', 'A submissão só pode ser finalizada quando estiver em fluxo editorial.');
            }

            $result = $model->submitWithoutEmail((int) $article['journal_submit_id']);
            $httpCode = (int) ($result['httpCode'] ?? 0);
            if (!in_array($httpCode, [200, 201], true)) {
                $details = $result['response']['errorMessage']
                    ?? $result['response']['error']
                    ?? $result['row']
                    ?? null;
                if (is_array($details)) {
                    $details = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                throw new \RuntimeException(
                    'O OJS recusou a finalização (HTTP ' . $httpCode . ').'
                    . ($details ? ' ' . trim((string) $details) : '')
                );
            }

            if (!$model->markArticleAsSubmitted((int) $journal['id'], $articleId)) {
                throw new \RuntimeException(
                    'A submissão foi finalizada no OJS, mas não foi possível alterar o status local do Article para 2.'
                );
            }

            return redirect()->to(base_url('ojs/articles_submied/view/' . $articleId))
                ->with('success', 'Submissão finalizada no OJS e status do Article alterado para 2.');
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
    public function submissionAction(int $articleId, int $targetStatus)
    {
        return $this->renderSubmissionActionPage($articleId, $targetStatus);
    }

    public function executeSubmissionAction(int $articleId, int $targetStatus)
    {
        return $this->renderSubmissionActionPage($articleId, $targetStatus, true);
    }

    private function renderSubmissionActionPage(int $articleId, int $targetStatus, bool $execute = false)
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
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artigo não encontrado.');
        }

        $flow = [
            2 => ['label' => 'Submetido', 'stageId' => 1, 'action' => 'Enviar para avaliação'],
            3 => ['label' => 'Em avaliação', 'stageId' => 3, 'action' => 'Aceitar submissão'],
            4 => ['label' => 'Edição de texto', 'stageId' => 4, 'action' => 'Enviar para Editoração'],
            5 => ['label' => 'Produção', 'stageId' => 5, 'action' => 'Agendar para publicação'],
            6 => ['label' => 'Agendado para publicação', 'stageId' => 5, 'action' => null],
        ];
        $result = null;
        $error = null;
        $submissionBefore = [];
        $submissionAfter = [];
        $openIssues = [];
        $selectedIssueId = (int) $this->request->getPost('issue_id');
        $articlePdf = ['path' => null, 'name' => null, 'expected' => null];
        $uploadPublicationOnly = $targetStatus === 6 && (int) ($article['status'] ?? 0) === 6;

        try {
            if (!isset($flow[$targetStatus]) || !in_array($targetStatus, [3, 4, 5, 6], true)) {
                throw new \RuntimeException('A ação solicitada não está disponível no fluxo do OJS.');
            }

            $submissionId = (int) ($article['journal_submit_id'] ?? 0);
            if ($submissionId <= 0) {
                throw new \RuntimeException('O artigo não possui uma submissão vinculada no OJS.');
            }

            $submissionBefore = $model->getSubmissionDetails((int) $journal['id'], $submissionId, true);
            if ($targetStatus === 6) {
                $articlePdf = $this->findSubmittedArticlePdf($article);
                if (!$uploadPublicationOnly) {
                    $articleYear = trim((string) ($article['Year'] ?? ''));
                    if (!preg_match('/^\\d{4}$/', $articleYear)) {
                        throw new \RuntimeException('O artigo não possui um ano válido para selecionar a edição.');
                    }

                    $openIssues = array_values(array_filter(
                        $model->getIssues((int) $journal['id'], false, true),
                        static function ($issue) use ($articleYear): bool {
                            $issueYear = is_object($issue)
                                ? trim((string) ($issue->year ?? ''))
                                : trim((string) ($issue['year'] ?? ''));
                            return $issueYear === $articleYear;
                        }
                    ));
                }
            }

            if ($execute) {
                $currentStatus = (int) ($article['status'] ?? 0);
                if ($currentStatus !== $targetStatus - 1 && !$uploadPublicationOnly) {
                    throw new \RuntimeException('Transição inválida para o status atual ' . $currentStatus . '.');
                }

                $expectedCurrentStage = (int) ($flow[$currentStatus]['stageId'] ?? 0);
                if ((int) ($submissionBefore['stageId'] ?? 0) !== $expectedCurrentStage) {
                    throw new \RuntimeException('A etapa atual do OJS não corresponde ao status local.');
                }

                if ($targetStatus === 3) {
                    $result = $model->sendToReview((int) $journal['id'], $submissionId);
                } elseif ($targetStatus === 4) {
                    $reviewRoundId = $model->getLatestExternalReviewRoundId($submissionBefore);
                    if ($reviewRoundId <= 0) {
                        throw new \RuntimeException('O OJS não retornou uma rodada de avaliação válida.');
                    }
                    $result = $model->acceptSubmission((int) $journal['id'], $submissionId, $reviewRoundId);
                } elseif ($targetStatus === 5) {
                    $result = $model->sendToProduction((int) $journal['id'], $submissionId);
                } else {
                    $publicationId = (int) ($submissionBefore['currentPublicationId'] ?? 0);
                    if ($publicationId <= 0) {
                        throw new \RuntimeException('O OJS não retornou a publicação atual da submissão.');
                    }
                    if ($articlePdf['path'] === null) {
                        throw new \RuntimeException('O PDF do artigo não foi encontrado: ' . ($articlePdf['expected'] ?? 'arquivo desconhecido'));
                    }

                    $composition = $model->createFinalComposition($submissionId, $publicationId, $articlePdf['path']);
                    $compositionHttpCode = (int) ($composition['httpCode'] ?? 0);
                    if (!in_array($compositionHttpCode, [200, 201], true)) {
                        $compositionDetails = $composition['response'] ?? $composition['raw'] ?? $composition['curl_error'] ?? null;
                        if (is_array($compositionDetails)) {
                            $compositionDetails = json_encode($compositionDetails, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                        throw new \RuntimeException('O OJS recusou a Composição Final PDF (HTTP ' . $compositionHttpCode . ').' . ($compositionDetails ? ' ' . $compositionDetails : ''));
                    }

                    if ($uploadPublicationOnly) {
                        $result = $composition;
                    } else {
                        $validIssue = false;
                        foreach ($openIssues as $issue) {
                            $issueId = is_object($issue) ? (int) ($issue->id ?? 0) : (int) ($issue['id'] ?? 0);
                            if ($issueId === $selectedIssueId) {
                                $validIssue = true;
                                break;
                            }
                        }
                        if (!$validIssue) {
                            throw new \RuntimeException('Selecione uma edição aberta válida para agendar a publicação.');
                        }
                        $result = $model->schedulePublication((int) $journal['id'], $submissionId, $publicationId, $selectedIssueId);
                        $result['steps'] = array_merge($composition['steps'] ?? [], $result['steps'] ?? []);
                    }
                }

                $httpCode = (int) ($result['httpCode'] ?? 0);
                if (!in_array($httpCode, [200, 201], true)) {
                    $details = $result['response'] ?? $result['row'] ?? $result['curl_error'] ?? null;
                    if (is_array($details)) {
                        $details = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                    throw new \RuntimeException('O OJS recusou a ação (HTTP ' . $httpCode . ').' . ($details ? ' ' . $details : ''));
                }

                $submissionAfter = $model->getSubmissionDetails((int) $journal['id'], $submissionId, true);
                if ((int) ($submissionAfter['stageId'] ?? 0) !== (int) $flow[$targetStatus]['stageId']) {
                    throw new \RuntimeException('O OJS respondeu com sucesso, mas não avançou para a etapa esperada.');
                }

                if (!$uploadPublicationOnly) {
                    $articleYear = trim((string) ($article['Year'] ?? ''));
                    if (!preg_match('/^\\d{4}$/', $articleYear)) {
                        throw new \RuntimeException('O artigo não possui um ano válido para selecionar a edição.');
                    }

                    $openIssues = array_values(array_filter(
                        $model->getIssues((int) $journal['id'], false, true),
                        static function ($issue) use ($articleYear): bool {
                            $issueYear = is_object($issue)
                                ? trim((string) ($issue->year ?? ''))
                                : trim((string) ($issue['year'] ?? ''));
                            return $issueYear === $articleYear;
                        }
                    ));
                }
            }
        } catch (\RuntimeException $exception) {
            $error = $exception->getMessage();
        }

        return $this->renderOjsPage('OJS/submission_action', [
            'page_title' => 'Ação da submissão',
            'journal' => $journal,
            'article' => $article,
            'targetStatus' => $targetStatus,
            'flow' => $flow,
            'result' => $result,
            'error' => $error,
            'executed' => $execute,
            'submissionBefore' => $submissionBefore,
            'submissionAfter' => $submissionAfter,
            'openIssues' => $openIssues,
            'selectedIssueId' => $selectedIssueId,
            'articlePdf' => $articlePdf,
            'uploadPublicationOnly' => $uploadPublicationOnly,
        ]);
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

        $articlePdf = $this->findSubmittedArticlePdf($article);

        return $this->renderOjsPage('OJS/submitted_article_view', [
            'page_title' => 'Visualizar submissão',
            'journal' => $journal,
            'article' => $article,
            'submission' => $submission,
            'apiError' => $apiError,
            'articlePdf' => $articlePdf,
        ]);
    }

    public function submittedArticlePdf(int $articleId)
    {
        if (!$this->hasJournalAccess()) {
            return $this->response->setStatusCode(403);
        }

        $journal = $this->getSelectedJournal();
        if ($journal === null) {
            return $this->response->setStatusCode(404);
        }

        $articleModel = new \App\Models\OJS\ArticleModel();
        $article = $articleModel->getSubmittedArticle((int) $journal['id'], $articleId);
        if ($article === null) {
            return $this->response->setStatusCode(404);
        }

        $articlePdf = $this->findSubmittedArticlePdf($article);
        if ($articlePdf['path'] === null) {
            return $this->response->setStatusCode(404);
        }

        return $this->response
            ->download($articlePdf['path'], null, true)
            ->setFileName(basename($articlePdf['path']))
            ->inline();
    }

    private function findSubmittedArticlePdf(array $article): array
    {
        $year = trim((string) ($article['Year'] ?? ''));
        $endPage = trim((string) ($article['PagEND'] ?? ''));
        if (preg_match('/^\d$/', $endPage)) {
            $endPage = str_pad($endPage, 2, '0', STR_PAD_LEFT);
        }
        $pageToken = $endPage !== '' ? $endPage : '{PAG_FINAL}';
        $filePatterns = ['*_' . $pageToken . '_o.pdf', '*_' . $pageToken . '.pdf'];
        $filePattern = implode(' ou ', $filePatterns);
        $relativePattern = ($year !== '' ? $year : '{YEAR}') . '/' . $filePattern;

        $configuredPath = trim((string) getenv('INMA_DATA_PATH'));
        $rootCandidates = array_filter([$configuredPath, '/INMA/data', 'D:/INMA/data']);
        $dataRoot = null;
        foreach ($rootCandidates as $candidate) {
            if (is_dir($candidate)) {
                $dataRoot = rtrim(str_replace('\\', '/', $candidate), '/');
                break;
            }
        }

        $result = [
            'path' => null,
            'name' => $filePattern,
            'expected' => '/INMA/data/' . $relativePattern,
        ];

        if ($dataRoot === null || !preg_match('/^\d{4}$/', $year) || !preg_match('/^[A-Za-z0-9.-]+$/', $endPage)) {
            return $result;
        }

        $yearDirectory = $dataRoot . '/' . $year;
        if (!is_dir($yearDirectory)) {
            return $result;
        }

        $files = scandir($yearDirectory) ?: [];
        natcasesort($files);
        $fileNamePattern = '/^.{2,}_' . preg_quote($endPage, '/') . '(?:_o)?\.pdf$/i';
        foreach ($files as $fileName) {
            if (!preg_match($fileNamePattern, $fileName)) {
                continue;
            }

            $realPath = realpath($yearDirectory . '/' . $fileName);
            if ($realPath !== false && is_file($realPath)) {
                $result['path'] = $realPath;
                $result['name'] = basename($realPath);
                break;
            }
        }

        return $result;
    }

    public function submissionSummary()
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        $articlesByStatus = [];
        $totalArticles = 0;
        $error = null;

        if ($journal === null) {
            $error = 'Nenhuma revista ativa está selecionada. Selecione uma revista para visualizar o resumo.';
        } else {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $articles = $articleModel->getArticlesForStatusSummary((int) $journal['id']);
            $totalArticles = count($articles);

            foreach ($articles as $article) {
                $status = (int) ($article['status'] ?? 0);
                $articlesByStatus[$status][] = $article;
            }

            ksort($articlesByStatus, SORT_NUMERIC);
        }

        return $this->renderOjsPage('OJS/submission_summary', [
            'page_title' => 'Resumo das submissões',
            'journal' => $journal,
            'articlesByStatus' => $articlesByStatus,
            'totalArticles' => $totalArticles,
            'error' => $error,
        ]);
    }
    public function submissionSummaryByStatus(int $status)
    {
        if (!$this->hasJournalAccess()) {
            return view('Brapci/Headers/deny');
        }

        $journal = $this->getSelectedJournal();
        $articles = [];
        $error = null;

        if ($journal === null) {
            $error = 'Nenhuma revista ativa está selecionada. Selecione uma revista para visualizar os artigos.';
        } else {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $articles = $articleModel->getArticlesForStatus((int) $journal['id'], $status);
        }

        return $this->renderOjsPage('OJS/submission_status', [
            'page_title' => 'Artigos por status',
            'journal' => $journal,
            'articles' => $articles,
            'status' => $status,
            'error' => $error,
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
    public function evaluationIn()
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
            $error = 'Nenhuma revista ativa está selecionada. Selecione uma revista para listar os artigos em avaliação.';
        } else {
            $articleModel = new \App\Models\OJS\ArticleModel();
            $articles = $articleModel->getArticlesInEvaluation((int) $journal['id'], $selectedYear);
        }

        return $this->renderOjsPage('OJS/articles_submied', [
            'page_title' => 'Artigos em avaliação',
            'journal' => $journal,
            'articles' => $articles,
            'error' => $error,
            'selectedYear' => $yearInput,
            'isEvaluationList' => true,
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

        unset($journal['api_key'], $journal['api_key_editor']);

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

        unset($journal['api_key'], $journal['api_key_editor']);

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
        $editorApiKey = trim((string) $this->request->getPost('api_key_editor'));
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
        if ($requireApiKey || $editorApiKey !== '') {
            $data['api_key_editor'] = $editorApiKey;
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

        unset($journal['api_key'], $journal['api_key_editor']);
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
