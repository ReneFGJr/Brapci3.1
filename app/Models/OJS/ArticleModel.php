<?php

namespace App\Models\OJS;

use CodeIgniter\Model;

use CodeIgniter\Database\BaseConnection;

class ArticleModel extends Model
{
    protected $DBGroup = 'ojs_import'; // Conexão com o banco ojs_import
    protected $table = 'inma'; // nome da tabela no banco
    protected $primaryKey = 'idR';
    protected $useAutoIncrement = true;

    // Conexão com o banco ojs_import
    protected $dbOjsImport;

    public function __construct()
    {
        parent::__construct();
        // Conecta ao banco ojs_import
        $this->dbOjsImport = \Config\Database::connect('ojs_import');
    }

    /**
     * Busca todos os registros da tabela inma no banco ojs_import
     */
    public function getAllInma()
    {
        return $this->dbOjsImport->table('inma')->get()->getResultArray();
    }

    /**
     * Busca um registro específico pelo ID
     */
    public function getInmaById($id)
    {
        return $this->dbOjsImport->table('inma')->where('ID', $id)->get()->getRowArray();
    }

    private $apiUrl    = 'https://editora.inma.gov.br/index.php/mbml/api/v1';
    private $apiToken  = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.WyI4YzEzMWNkY2RhZDlmMzNkMzNhOTE5ZWU3MDJiMTA1ZTQzM2ZlOThjIl0.jzV6pNZLbSvBaGqXna7HC3yk1wy46a-gOm0aVn6dWS0';

    protected $allowedFields = [
        'title',
        'abstract',
        'author',
        'email',
        'file_name',
        'submission_id',
        'status',
        'created_at',
        'updated_at',
        'submit_id',
        'submit_data',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    function curl($url, $method = 'GET', $data = null)
    {
        $ch = curl_init();

        $jsonPayload = $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;

        // 🔥 buffer para debug
        $verbose = fopen('php://temp', 'w+');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiToken,
            'Accept: application/json',
            'Content-Type: application/json',
        ]);

        // 🔧 método
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($jsonPayload) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($jsonPayload) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            }
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        // 🔴 SSL (dev only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        // 🔥 debug detalhado
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);

        // 🔥 CAPTURA ANTES DE FECHAR
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        rewind($verbose);
        $debug = stream_get_contents($verbose);

        curl_close($ch);

        return [
            "httpCode" => $httpCode,
            "response" => json_decode($response, true), // 🔥 resposta original
            "row" => $response, // 🔥 resposta original (sem json_decode)
            "decoded" => json_decode($response),
            "json_error" => json_last_error_msg(),
            "curl_error" => $curlError,
            "debug" => $debug,
            "payload" => $data,
            "url" => $url
        ];
    }

    /**
     * Cria uma submissão no OJS a partir dos dados do CSV
     */
    public function createSubmissionFromCsv($data)
    {
        $payload = [
            'sectionId' => $data['sectionId'] ?? 1,
            'title' => ['pt_BR' => $data['title']['pt_BR'] ?? $data['title'] ?? ''],
            'locale' => $data['locale'] ?? 'pt_BR',
            'language' => $data['language'] ?? 'pt_BR',
        ];
        // O campo authors e year não são padrão do endpoint, mas podem ser usados em metadados
        // Adapte conforme a API do seu OJS

        $url = $this->apiUrl . '/submissions?apiToken=' . urlencode($this->apiToken);
        $rsp = $this->curl($url, 'POST', $payload);
        return $rsp;
    }

    function submitEditoracao($submissionId)
    {
        $url = $this->apiUrl . "/submissions/{$submissionId}/submit";

        $payload = [
            "stageId" => 3,
            "toStageId" => 4,
            "skipEmail" => true
        ];
        $RST = $this->curl($url, 'PUT', $payload);

        $payload = [
            "stageId" => 4,
            "toStageId" => 5,
            "skipEmail" => true
        ];
        $RST = $this->curl($url, 'PUT', $payload);

        $payload = [
            "stageId" => 5,
            "toStageId" => 6,
            "skipEmail" => true
        ];
        $RST = $this->curl($url, 'PUT', $payload);

        return $RST;
    }

    function submitWithoutEmail($submissionId)
    {
        $url = $this->apiUrl . "/submissions/{$submissionId}/submit";

        $payload = [
            "stageId" => 1,
            "toStageId" => 3,
            "skipEmail" => true
        ];

        $RST = $this->curl($url, 'PUT', $payload);
        return $RST;
    }

    /**
     * Importa um arquivo CSV e retorna os dados lidos
     * @param string $file Caminho do arquivo CSV
     * @return array|string Dados importados ou mensagem de erro
     */
    public function importCsv($file)
    {
        if (!file_exists($file)) {
            return 'Arquivo CSV não encontrado: ' . $file;
        }
        $dados = [];
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ';'); // lê cabeçalho
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $registro = array_combine($header, $row);
                // Verifica se já existe pelo campo ID
                $id = $registro['ID'] ?? $registro['id'] ?? null;
                $jaExiste = false;
                if ($id) {
                    $jaExiste = $this->where('id', $id)->countAllResults() > 0;
                }
                if (!$jaExiste) {
                    $dados[] = $registro;
                }
            }
            fclose($handle);
        } else {
            return 'Erro ao abrir o arquivo CSV.';
        }
        return $dados;
    }

    /**
     * Busca todas as submissões ativas no OJS via API
     * @return array|null
     */
    public function getActiveSubmissions()
    {
        $endPoint = $this->apiUrl . '/submissions?status[]=1&apiToken=' . urlencode($this->apiToken);
        $rsp = $this->curl($endPoint, 'GET');
        // O JSON retorna 'items', não 'submissions'
        if ($rsp['httpCode'] == 200 && isset($rsp['response']->items)) {
            return $rsp['response']->items;
        }
        return null;
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
            'keywords' => [
                'pt_BR' => [
                    'OJS',
                ]
            ],

            // 🔹 Keywords (palavras-chave)
            // Deve ser um array de arrays, com locale como chave
            'keywords' => [
                'pt_BR' => ['OJS', 'API', 'PHP', 'Submissão', 'Automação']
            ]
        ];

        $submissionId = 38;
        $publicationId = 38;

        $this->insertAuthor($submissionId, $publicationId);

        $RSP = $this->curl($this->apiUrl . '/submissions/'.$submissionId.'/publications/'.$publicationId.'?apiToken=' . urlencode($this->apiToken), 'PUT', $data);
        return $RSP;
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

        curl_close($ch);

        echo "HTTP Status: {$httpCode}\n";
        echo "Resposta: {$response}\n";

        exit;
    }

    /**
     * 0 - Submissão inicial
     */
    public function submitToOJS($data)
    {
        return $this->createSubmissionFromCsv($data);
    }

    /**
     * 1 - Atualizar título
     */
    public function updateTitleOJS($submissionId, $title, $abstract='', $locale = 'pt_BR')
    {
        $publicationId = $submissionId;
        if ($abstract == '') { $abstract = 'Resumo não disponível para: ' . $title; }
        $payload = [
            'title' => [
                $locale => $title
            ],
            'fullTitle' => [
                $locale => $title
            ],
            'abstract' => [
                $locale => $abstract
            ]
        ];

        $url = $this->apiUrl . "/submissions/{$submissionId}/publications/{$publicationId}?apiToken=" . urlencode($this->apiToken);

        return $this->curl($url, 'PUT', $payload);
    }

    public function addAuthors($submissionId, $authors)
    {
        if (($authors == 'Ruschi Augusto') or ($authors == 'Ruschi Augusto;')){
            return [
                'httpCode' => 400,
                'response' => 'Nenhum autor fornecido para adicionar.'
            ];
        }
        $authors = explode(',', $authors);
        foreach ($authors as $a) {
            $familyName = substr(trim($a), 0, strpos(trim($a), ' '));
            $firstName = trim(str_replace($familyName, '', $a));
            $firstName = str_replace([';', ''], '', $firstName);
            $firstName = trim($firstName);
            $payload = [
                "givenName" => ["pt_BR" => trim($firstName)],
                "familyName" => ["pt_BR" => trim($familyName)],
                "email" => 'editora@inma.gov.br',
                "country" => "BR",
                "primaryContact" => true,
                "userGroupId" => 14,
                "affiliations" => [
                    [
                        "name" => ["pt_BR" => 'INMA']
                    ]
                ]
            ];
        }
        $this->addAuthorsIndividual($submissionId, $payload);
        return [
            'httpCode' => 200,
            'response' => 'Autores processados: ' . implode(', ', $authors)
        ];
        /*
                $payload = [
            "givenName" => ["pt_BR" => "Augusto"],
            "familyName" => ["pt_BR" => "Ruschi"],
            "email" => "autor@email.com",
            "country" => "BR",
            "primaryContact" => true,
            "userGroupId" => 14,
            "affiliations" => [
                [
                    "name" => ["pt_BR" => "INMA"]
                ]
            ]
        ];
        */
    }

    public function addAuthorsIndividual($submissionId, $payload)
    {


        $updateUrl = $this->apiUrl . "/submissions/{$submissionId}/publications/{$submissionId}/contributors?apiToken=" . urlencode($this->apiToken);

        // 3. cURL correto
        $ch = curl_init($updateUrl);

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

        // DEBUG
        $verbose = fopen('php://temp', 'w+');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Content-Length: " . strlen($jsonPayload),
            "Authorization: Bearer {$this->apiToken}"
        ]);

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        rewind($verbose);
        $debug = stream_get_contents($verbose);

        curl_close($ch);

        $RSP = [
            "status" => $httpCode,
            "raw" => $response,
            "decoded" => json_decode($response),
            "curl_error" => $curlError,
            "debug" => $debug
        ];
        return $RSP;
    }

    public function uploadPDFToSubmission($submissionId, $filePath, $locale = 'pt_BR')
    {
        $url = $this->apiUrl . "/submissions/{$submissionId}/files?apiToken=" . urlencode($this->apiToken);

        $file = new \CURLFile($filePath, 'application/pdf', basename($filePath));

        $postFields = [
            'file' => $file,
            'name[' . $locale . ']' => basename($filePath),
            'genreId' => 1, // ID do gênero (ajuste conforme necessário)
            'fileStage' => 2 // 🔥 corrigido
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        // DEBUG
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        // SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $curlError = curl_error($ch);

        rewind($verbose);
        $debug = stream_get_contents($verbose);

        curl_close($ch);

        $RSP = [
            "status" => $httpCode,
            "raw" => $response,
            "decoded" => json_decode($response),
            "curl_error" => $curlError,
            "debug" => $debug
        ];
        pre($RSP);
        return $RSP;
    }

    /**
     * 2 - Atualizar autores
     */
    public function updateAuthorsOJS($submitId, $authors)
    {
        $payload = [
            'authors' => $authors
        ];
        $url = $this->apiUrl . '/submissions/' . $submitId . '?apiToken=' . urlencode($this->apiToken);
        return $this->curl($url, 'PUT', $payload);
    }

    /**
     * 3 - Enviar Arquivo
     */
    public function updateAbstractOJS($submitId, $abstract)
    {
        $payload = [
            'abstract' => ['pt_BR' => $abstract]
        ];
        $url = $this->apiUrl . '/submissions/' . $submitId . '?apiToken=' . urlencode($this->apiToken);
        return $this->curl($url, 'PUT', $payload);
    }

    /**
     * 4 - Upload de arquivo
     */
    public function uploadFileOJS($submitId, $filePath)
    {
        // Exemplo de upload de arquivo via cURL (ajuste conforme a API do OJS)
        $url = $this->apiUrl . '/submissions/' . $submitId . '/files?apiToken=' . urlencode($this->apiToken);
        $cfile = new \CURLFile($filePath, 'application/pdf');
        $post = [
            'file' => $cfile,
            'fileStage' => 2, // 2 = Submissão
             'genreId' => 1, // ID do gênero (ajuste conforme necessário)
             'name[pt_BR]' => basename($filePath) // Nome do arquivo com locale
            ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['httpCode' => $httpCode, 'response' => json_decode($response)];
    }
}
