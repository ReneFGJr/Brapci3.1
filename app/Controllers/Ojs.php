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
        return view('OJS/form_upload');
    }

    public function journal()
    {
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Accept'        => 'application/json',
                ],
                'timeout' => 10,
                'verify'  => false, // 🚨 desativa a verificação SSL temporariamente
            ]);

            $status = $response->getStatusCode();

            if ($status !== 200) {
                return "Erro ao acessar API (HTTP {$status})";
            }

            $data = json_decode($response->getBody(), true);

            if (empty($data['items'][0])) {
                return "Nenhum dado retornado pela API.";
            }
        } catch (\Exception $e) {
            return "❌ Erro: " . $e->getMessage();
        }
        // Pega o primeiro contexto
        $revista = $data['items'][0];

        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit;
        // Passa os dados para a View
        return view('OJS/revista_view', ['revista' => $revista]);
    }



    public function send()
    {
        $ArticleModel = new \App\Models\OJS\ArticleModel();
        helper(['form', 'filesystem', 'sidoc_forms']);
        helper('sisdoc_forms');

        //$ArticleModel->createSubmission();
        $ArticleModel->updateSubmission();
        exit;

        $titulo = $this->request->getPost('titulo');
        $resumo = $this->request->getPost('resumo');
        $autor  = $this->request->getPost('autor');
        $email  = $this->request->getPost('email');
        $pdf    = $this->request->getFile('arquivo');

        if (!$pdf->isValid()) {
            return "❌ Erro: arquivo inválido.";
        }

        // === 1️⃣ Envia metadados da submissão ===
        $client = \Config\Services::curlrequest();

        // === 1️⃣ Metadados da submissão ===
        $data = [
            "contextId" => 1,   // pegue do /api/v1/contexts
            "sectionId" => 3,   // pegue do /api/v1/sections?contextId=1
            "locale"    => "pt_BR",
            "title"     => ["pt_BR" => $titulo . '2'],
            "abstract"  => ["pt_BR" => $resumo],
            "authors"   => [[
                "givenName"        => ["pt_BR" => "René Faustino"],
                "familyName"       => ["pt_BR" => "Gabriel Junior"],
                "email"            => $email,
                "country"          => "BR",
                "isPrimaryContact" => true
            ]]
        ];

        pre($data);

        try {
            $response = $client->post($this->apiUrl . '/submissions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ],
                'json'    => $data,
                'timeout' => 20,
                'verify'  => false
            ]);
            $status = $response->getStatusCode();
            $body   = $response->getBody();

            echo "<h3>HTTP $status</h3>";
            echo "<h4>Request JSON</h4><pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
            echo "<h4>Response</h4><pre>" . htmlspecialchars($body) . "</pre>";
        } catch (\Exception $e) {
            return "❌ Erro: " . $e->getMessage();
        }

        pre($response);

        $status = $response->getStatusCode();
        $json   = json_decode($response->getBody(), true);

        pre($status);
        pre($json);


        if ($status != 201 || empty($json['id'])) {
            return "❌ Falha ao criar submissão (HTTP $status):<br><pre>" .
                htmlspecialchars($response->getBody()) . "</pre>";
        }

        $submissionId = $json['id'];

        // === 2️⃣ Faz upload do PDF ===
        $uploadUrl = "https://editora.inma.gov.br/index.php/mbml/api/v1/submissions/{$submissionId}/files";

        $tempPath = WRITEPATH . 'uploads/' . $pdf->getRandomName();
        $pdf->move(WRITEPATH . 'uploads', basename($tempPath));

        $uploadResponse = $client->post($uploadUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept'        => 'application/json'
            ],
            'multipart' => [[
                'name'     => 'file',
                'contents' => fopen($tempPath, 'r'),
                'filename' => $pdf->getName()
            ]]
        ]);

        unlink($tempPath);

        $uploadStatus = $uploadResponse->getStatusCode();
        $uploadBody   = $uploadResponse->getBody();

        if ($uploadStatus != 201) {
            return "⚠️ Submissão criada (ID {$submissionId}), mas erro no upload do arquivo:<br><pre>" .
                htmlspecialchars($uploadBody) . "</pre>";
        }

        return "✅ Submissão enviada com sucesso!<br>
                🆔 ID: {$submissionId}<br><br>
                📎 Resposta do upload:<pre>" . htmlspecialchars($uploadBody) . "</pre>";
    }
}
