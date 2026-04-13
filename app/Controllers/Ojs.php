<?php

namespace App\Controllers;

use CodeIgniter\Controller;

helper('sisdoc_forms');

class Ojs extends Controller
{
    private $apiUrl    = 'https://editora.inma.gov.br/index.php/mbml/api/v1';
    private $apiToken  = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.WyJhMGQyYzEyMDMxOTM4MzU1Y2YzYTc0YjNhMmY1NTIzZDkwMTFhY2JiIl0.eYcvJZZrNEJf-vobUHndFJbgAbrx88V5YdTlJZbhF3E';

    public function index()
    {
        // Página inicial com menu
        return view('OJS/home');
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
        // Carrega o model
        $articleModel = new \App\Models\OJS\ArticleModel();
        $submissoes = $articleModel->getActiveSubmissions();

        return view('OJS/submissoes', [
            'submissoes' => $submissoes
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
}
