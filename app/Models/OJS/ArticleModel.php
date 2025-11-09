<?php

namespace App\Models\OJS;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table = 'articles'; // nome da tabela no banco
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    private $apiUrl    = 'https://editora.inma.gov.br/index.php/mbml/api/v1';
    private $apiToken  = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.WyJhMGQyYzEyMDMxOTM4MzU1Y2YzYTc0YjNhMmY1NTIzZDkwMTFhY2JiIl0.eYcvJZZrNEJf-vobUHndFJbgAbrx88V5YdTlJZbhF3E';

    protected $allowedFields = [
        'title',
        'abstract',
        'author',
        'email',
        'file_name',
        'submission_id',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    function curl($url, $method = 'GET', $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiToken,
            'Accept: application/json',
            'Content-Type: application/json',
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        // ---------- DESABILITAR VERIFICAÇÃO SSL (inseguro) ----------
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // não verifica o certificado do peer
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // não verifica o nome do host no certificado

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['httpCode' => $httpCode, 'response' => json_decode($response)];
    }

    function insertAuthor($submissionId = 28, $publicationId = 28)
    {
        // === 2️⃣ Adicionar um autor (contributor) ===
        $author = [
            'givenName' => ['pt_BR' => 'Rene Faustino'],
            'familyName' => ['pt_BR' => 'Gabriel Junior'],
            'email' => 'renefgj@gmail.com',
            'country' => 'BR',
            'affiliation' => ['pt_BR' => 'Universidade Federal do Rio Grande do Sul'],
            'orcid' => 'https://orcid.org/0000-0003-1021-3360',
            'primaryContact' => true,
            'seq' => 1,
            'userGroupId' => 14
        ];// Lógica para inserir autor no banco de dados

        $endPoint = $this->apiUrl . '/submissions/' . $submissionId . '/publications/' . $publicationId . '/contributors?apiToken=' . urlencode($this->apiToken);
        $rsp = $this->curl($endPoint, 'POST', $author);
        return $rsp;
    }

    function updateSubmission()
    {
        $data = [
            // Campos que deseja atualizar na publication
            'title' => ['pt_BR' => 'X2X2 Novo Título do Artigo via API'],
            'fullTitle' => ['pt_BR' => 'Titulo Completo (opcional)'],
            'subtitle' => ['pt_BR' => 'Subtítulo (se necessário)'],
            'abstract' => [
                'pt_BR' => 'Este artigo apresenta um estudo sobre o uso da API do OJS para automação de submissões e atualização de metadados via PHP.'
            ],

            // 🔹 Keywords (palavras-chave)
            // Deve ser um array de arrays, com locale como chave
            'keywords' => [
                'pt_BR' => ['OJS', 'API', 'PHP', 'Submissão', 'Automação']
            ]
        ];

        $submissionId = 28;
        $publicationId = 28;

        $this->insertAuthor($submissionId, $publicationId);

        $RSP = $this->curl($this->apiUrl . '/submissions/'.$submissionId.'/publications/'.$publicationId.'?apiToken=' . urlencode($this->apiToken), 'PUT', $data);


        pre($RSP);


    }

    public function createSubmission()
    {
        $contextId = 1; // ID da revista/contexto
        $sectionId = 1; // ID da seção para a submissão (por exemplo “Artigo”)
        $locale = 'pt_BR'; // idioma da submissão

        $data = [
            'sectionId' => 1,
            'title' => ['pt_BR' => 'Título via API sem contextId no body'],
            'locale' => 'pt_BR',
            'language' => 'pt_BR'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/submissions?apiToken=' . urlencode($this->apiToken));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // ---------- DESABILITAR VERIFICAÇÃO SSL (inseguro) ----------
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // não verifica o certificado do peer
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // não verifica o nome do host no certificado

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        pre($response);
        curl_close($ch);

        echo "HTTP Status: {$httpCode}\n";
        echo "Resposta: {$response}\n";

        exit;
    }
}
