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

    public function createDraftSubmission(int $journalId, array $article): array
    {
        $journalModel = new JournalModel();
        $journal = $journalModel->where('id', $journalId)->where('active', 1)->first();
        if ($journal === null) {
            throw new \RuntimeException('A revista selecionada não existe ou está inativa.');
        }
        if (trim((string) ($journal['api_key'] ?? '')) === '') {
            throw new \RuntimeException('A revista selecionada não possui APIKEY configurada.');
        }

        $this->apiUrl = $journalModel->getApiUrl($journal);
        $this->apiToken = $journal['api_key'];
        $payload = [
            'sectionId' => 1,
            'locale' => 'pt_BR',
            'language' => 'pt_BR',
            'title' => ['pt_BR' => trim((string) ($article['Title'] ?? ''))],
            'authors' => trim((string) ($article['Authors'] ?? '')),
            'affiliation' => trim((string) ($article['Affiliation'] ?? '')),
            'year' => trim((string) ($article['Year'] ?? '')),
            'section' => trim((string) ($article['Vol'] ?? '')),
        ];

        // Cria somente o rascunho. Não chama /submissions/{id}/submit.
        $apiResponse = $this->curl(
            $this->apiUrl . '/submissions?apiToken=' . urlencode($this->apiToken),
            'POST',
            $payload
        );
        $httpCode = (int) ($apiResponse['httpCode'] ?? 0);
        $responseData = is_array($apiResponse['response'] ?? null) ? $apiResponse['response'] : [];
        $submissionId = isset($responseData['id']) ? (int) $responseData['id'] : null;
        $success = in_array($httpCode, [200, 201], true) && $submissionId !== null;

        if ($success) {
            $this->dbOjsImport->table('article')
                ->where('idR', (int) $article['idR'])
                ->where('journal_id', $journalId)
                ->where('journal_submit_id', null)
                ->update([
                    'journal_submit_id' => $submissionId,
                    'submit_id' => $submissionId,
                    'submit_data' => date('Y-m-d H:i:s'),
                ]);
        }

        return [
            'article_id' => (int) $article['idR'],
            'title' => (string) ($article['Title'] ?? ''),
            'payload' => $payload,
            'http_code' => $httpCode,
            'success' => $success,
            'submission_id' => $submissionId,
            'response' => $responseData,
            'error' => $apiResponse['curl_error'] ?: ($responseData['errorMessage'] ?? $responseData['error'] ?? null),
        ];
    }
    public function getArticlesForSubmission(int $journalId, array $articleIds): array
    {
        $articleIds = array_values(array_unique(array_filter(array_map('intval', $articleIds), static fn (int $id): bool => $id > 0)));
        if ($articleIds === []) {
            return [];
        }

        return $this->dbOjsImport->table('article')
            ->where('journal_id', $journalId)
            ->where('journal_submit_id', null)
            ->whereIn('idR', $articleIds)
            ->orderBy('idR', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function updateSubmittedArticle(int $journalId, int $articleId, array $data): bool
    {
        $allowed = ['Title', 'Authors', 'Affiliation', 'Year', 'Vol', 'Num', 'PagINI', 'PagEND', 'Keywords'];
        $updates = array_intersect_key($data, array_flip($allowed));
        if ($updates === []) {
            return false;
        }

        return $this->dbOjsImport->table('article')
            ->where('idR', $articleId)
            ->where('journal_id', $journalId)
            ->where('journal_submit_id IS NOT NULL', null, false)
            ->update($updates);
    }
    public function markArticleAsSubmitted(int $journalId, int $articleId): bool
    {
        return $this->dbOjsImport->table('article')
            ->where('idR', $articleId)
            ->where('journal_id', $journalId)
            ->where('journal_submit_id IS NOT NULL', null, false)
            ->update(['status' => 2]);
    }
    public function getSubmittedArticle(int $journalId, int $articleId): ?array
    {
        return $this->dbOjsImport->table('article')
            ->where('idR', $articleId)
            ->where('journal_id', $journalId)
            ->where('journal_submit_id IS NOT NULL', null, false)
            ->get()
            ->getRowArray();
    }

    public function updateOjsSubmissionFromArticle(int $journalId, array $article): array
    {
        $submissionId = (int) ($article['journal_submit_id'] ?? 0);
        $submission = $this->getSubmissionDetails($journalId, $submissionId);
        $publications = $submission['publications'] ?? [];
        $currentPublicationId = (int) ($submission['currentPublicationId'] ?? 0);
        $publication = null;

        foreach ($publications as $candidate) {
            if ($currentPublicationId === 0 || (int) ($candidate['id'] ?? 0) === $currentPublicationId) {
                $publication = $candidate;
                break;
            }
        }

        if ($publication === null || empty($publication['id'])) {
            throw new \RuntimeException('A publicação atual da submissão não foi encontrada no OJS.');
        }

        $year = trim((string) ($article['Year'] ?? ''));
        $authors = trim((string) ($article['Authors'] ?? ''));
        $volume = trim((string) ($article['Vol'] ?? ''));
        $payload = [
            'title' => ['pt_BR' => trim((string) ($article['Title'] ?? ''))],
            'fullTitle' => ['pt_BR' => trim((string) ($article['Title'] ?? ''))],
            'abstract' => [
                'pt_BR' => 'Artigo da Revista publicado em ' . $year . ' por ' . $authors . ' na ' . $volume,
            ],
        ];
        if (preg_match('/^\d{4}$/', (string) ($article['Year'] ?? ''))) {
            $payload['copyrightYear'] = (int) $article['Year'];
        }
        if ($volume !== '') {
            $payload['keywords'] = ['pt_BR' => [$volume]];
        }

        $response = $this->curl(
            $this->apiUrl . '/submissions/' . $submissionId . '/publications/' . (int) $publication['id']
                . '?apiToken=' . urlencode($this->apiToken),
            'PUT',
            $payload
        );
        $httpCode = (int) ($response['httpCode'] ?? 0);
        $responseData = is_array($response['response'] ?? null) ? $response['response'] : [];

        $metadataSuccess = in_array($httpCode, [200, 201], true);
        $authorsResult = $metadataSuccess
            ? $this->syncOjsContributors(
                $submissionId,
                (int) $publication['id'],
                (string) ($article['Authors'] ?? ''),
                (string) ($article['Affiliation'] ?? '')
            )
            : ['success' => false, 'processed' => 0, 'errors' => ['Metadados da publicação não foram atualizados.']];

        $error = $response['curl_error'] ?: ($responseData['errorMessage'] ?? $responseData['error'] ?? null);
        if ($metadataSuccess && !$authorsResult['success']) {
            $error = implode(' ', $authorsResult['errors']);
        }
        return [
            'success' => $metadataSuccess && $authorsResult['success'],
            'http_code' => $httpCode,
            'payload' => $payload,
            'response' => $responseData,
            'authors' => $authorsResult,
            'error' => $error,
        ];
    }

    private function syncOjsContributors(int $submissionId, int $publicationId, string $authors, string $affiliation): array
    {
        $authors = trim($authors);
        if ($authors === '') {
            return ['success' => true, 'processed' => 0, 'errors' => []];
        }

        $names = str_contains($authors, ';')
            ? preg_split('/\s*;\s*/', $authors, -1, PREG_SPLIT_NO_EMPTY)
            : preg_split('/\s*,\s*/', $authors, -1, PREG_SPLIT_NO_EMPTY);
        $baseUrl = $this->apiUrl . '/submissions/' . $submissionId . '/publications/' . $publicationId . '/contributors';
        $currentResponse = $this->curl($baseUrl . '?apiToken=' . urlencode($this->apiToken), 'GET');
        $currentData = is_array($currentResponse['response'] ?? null) ? $currentResponse['response'] : [];
        $current = $currentData['items'] ?? $currentData;
        if (!is_array($current)) {
            $current = [];
        }

        $errors = [];
        $processed = 0;
        foreach ($names as $index => $fullName) {
            $parts = preg_split('/\s+/', trim($fullName), -1, PREG_SPLIT_NO_EMPTY);
            if ($parts === []) {
                continue;
            }
            $familyName = array_shift($parts);
            $givenName = trim(implode(' ', $parts));
            if ($givenName === '') {
                $givenName = $familyName;
                $familyName = '';
            }

            $existing = $current[$index] ?? [];
            $contributorId = (int) ($existing['id'] ?? 0);
            $existingEmail = trim((string) ($existing['email'] ?? ''));
            $email = $existingEmail !== '' && !preg_match('/^ojs-import\+.*@brapci\.inf\.br$/i', $existingEmail)
                ? $existingEmail
                : 'boletim@inma.gov.br';
            $payload = [
                'givenName' => ['pt_BR' => $givenName],
                'familyName' => ['pt_BR' => $familyName],
                'email' => $email,
                'country' => $existing['country'] ?? 'BR',
                'primaryContact' => $index === 0,
                'userGroupId' => (int) ($existing['userGroupId'] ?? 14),
            ];

            if ($contributorId > 0) {
                $payload['affiliations'] = [[
                    'authorId' => $contributorId,
                    'name' => ['pt_BR' => 'Instituto Nacional da Mata Atlântica'],
                    'ror' => 'https://ror.org/0395f2d85',
                ]];
            }

            $url = $baseUrl . ($contributorId > 0 ? '/' . $contributorId : '')
                . '?apiToken=' . urlencode($this->apiToken);
            $result = $this->curl($url, $contributorId > 0 ? 'PUT' : 'POST', $payload);
            $status = (int) ($result['httpCode'] ?? 0);
            if (!in_array($status, [200, 201], true)) {
                $errors[] = $this->formatContributorError($index, $status, $result);
                continue;
            }

            // Para novos autores, o vínculo institucional só pode ser salvo após obter o authorId.
            if ($contributorId === 0) {
                $created = is_array($result['response'] ?? null) ? $result['response'] : [];
                $contributorId = (int) ($created['id'] ?? 0);
                if ($contributorId === 0) {
                    $errors[] = 'Autor ' . ($index + 1) . ': o OJS criou o colaborador sem retornar o authorId.';
                    continue;
                }

                $payload['affiliations'] = [[
                    'authorId' => $contributorId,
                    'name' => ['pt_BR' => 'Instituto Nacional da Mata Atlântica'],
                    'ror' => 'https://ror.org/0395f2d85',
                ]];
                $affiliationResult = $this->curl(
                    $baseUrl . '/' . $contributorId . '?apiToken=' . urlencode($this->apiToken),
                    'PUT',
                    $payload
                );
                $affiliationStatus = (int) ($affiliationResult['httpCode'] ?? 0);
                if (!in_array($affiliationStatus, [200, 201], true)) {
                    $errors[] = $this->formatContributorError($index, $affiliationStatus, $affiliationResult);
                    continue;
                }
            }

            $processed++;
        }

        return ['success' => $errors === [], 'processed' => $processed, 'errors' => $errors];
    }
    private function formatContributorError(int $index, int $status, array $result): string
    {
        $data = is_array($result['response'] ?? null) ? $result['response'] : [];
        $details = $data['errors'] ?? $data['errorMessage'] ?? $data['error'] ?? null;
        if (is_array($details)) {
            $details = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $message = trim((string) ($details ?: ($result['curl_error'] ?? '') ?: ($result['row'] ?? '')));
        if ($message === '') {
            $message = 'O OJS não informou o campo inválido.';
        }

        return 'Autor ' . ($index + 1) . ': HTTP ' . $status . ' — ' . $message;
    }
    public function updateArticleFromOjs(int $journalId, array $article): array
    {
        $submission = $this->getSubmissionDetails($journalId, (int) $article['journal_submit_id']);
        $publications = $submission['publications'] ?? [];
        $currentPublicationId = (int) ($submission['currentPublicationId'] ?? 0);
        $publication = null;

        foreach ($publications as $candidate) {
            if ($currentPublicationId === 0 || (int) ($candidate['id'] ?? 0) === $currentPublicationId) {
                $publication = $candidate;
                break;
            }
        }
        if ($publication === null) {
            throw new \RuntimeException('A publicação atual da submissão não foi encontrada no OJS.');
        }

        $localized = static function ($value): string {
            if (is_string($value)) {
                return $value;
            }
            if (!is_array($value)) {
                return '';
            }
            return (string) ($value['pt_BR'] ?? $value['en_US'] ?? $value['en'] ?? reset($value) ?: '');
        };

        $updates = [];
        $title = $localized($publication['fullTitle'] ?? $publication['title'] ?? '');
        if ($title !== '') {
            $updates['Title'] = $title;
        }
        if (!empty($publication['authorsString'])) {
            $updates['Authors'] = (string) $publication['authorsString'];
        }
        $year = $publication['copyrightYear'] ?? null;
        if ($year === null && !empty($publication['datePublished'])) {
            $year = substr((string) $publication['datePublished'], 0, 4);
        }
        if (preg_match('/^\d{4}$/', (string) $year)) {
            $updates['Year'] = (string) $year;
        }

        if ($updates === []) {
            throw new \RuntimeException('O OJS não retornou metadados compatíveis para atualizar a tabela Article.');
        }

        $this->dbOjsImport->table('article')
            ->where('idR', (int) $article['idR'])
            ->where('journal_id', $journalId)
            ->update($updates);

        return $updates;
    }
    public function getSubmissionDetails(int $journalId, int $submissionId): array
    {
        $journalModel = new JournalModel();
        $journal = $journalModel->where('id', $journalId)->where('active', 1)->first();
        if ($journal === null) {
            throw new \RuntimeException('A revista selecionada não existe ou está inativa.');
        }
        if (trim((string) ($journal['api_key'] ?? '')) === '') {
            throw new \RuntimeException('A revista selecionada não possui APIKEY configurada.');
        }

        $this->apiUrl = $journalModel->getApiUrl($journal);
        $this->apiToken = $journal['api_key'];
        $apiResponse = $this->curl(
            $this->apiUrl . '/submissions/' . $submissionId . '?apiToken=' . urlencode($this->apiToken),
            'GET'
        );

        if ((int) ($apiResponse['httpCode'] ?? 0) !== 200) {
            throw new \RuntimeException('Não foi possível consultar a submissão no OJS (HTTP ' . (int) ($apiResponse['httpCode'] ?? 0) . ').');
        }

        return is_array($apiResponse['response'] ?? null) ? $apiResponse['response'] : [];
    }
    public function getSubmittedArticles(int $journalId, ?string $year = null): array
    {
        $builder = $this->dbOjsImport->table('article')
            ->where('journal_id', $journalId)
            ->where('journal_submit_id IS NOT NULL', null, false)
            ->where('status <', 2);

        if ($year !== null) {
            $builder->where('Year', $year);
        }

        return $builder->orderBy('Year', 'DESC')
            ->orderBy('idR', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getArticlesToSubmit(int $journalId, ?string $year = null): array
    {
        $builder = $this->dbOjsImport->table('article')
            ->where('journal_id', $journalId)
            ->where('journal_submit_id', null);

        if ($year !== null) {
            $builder->where('Year', $year);
        }

        return $builder->orderBy('Year', 'DESC')
            ->orderBy('idR', 'ASC')
            ->get()
            ->getResultArray();
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
    public function getActiveSubmissions(int $journalId): array
    {
        $journalModel = new JournalModel();
        $journal = $journalModel->where('id', $journalId)
            ->where('active', 1)
            ->first();

        if ($journal === null) {
            throw new \RuntimeException('A revista selecionada não existe ou está inativa.');
        }

        if (trim((string) ($journal['api_key'] ?? '')) === '') {
            throw new \RuntimeException('A revista selecionada não possui APIKEY configurada.');
        }

        $this->apiUrl = $journalModel->getApiUrl($journal);
        $this->apiToken = $journal['api_key'];

        $endPoint = $this->apiUrl . '/submissions?status[]=1&apiToken=' . urlencode($this->apiToken);
        $rsp = $this->curl($endPoint, 'GET');

        if ($rsp['httpCode'] !== 200) {
            throw new \RuntimeException('Não foi possível consultar as submissões no OJS selecionado (HTTP ' . $rsp['httpCode'] . ').');
        }

        $items = $rsp['response']['items'] ?? [];
        if (!is_array($items)) {
            return [];
        }

        // Mantém o formato de objetos esperado pela view, inclusive nos níveis internos.
        return json_decode(json_encode($items, JSON_UNESCAPED_UNICODE)) ?? [];
    }
    public function getIssues(int $journalId, bool $isPublished): array
    {
        $journalModel = new JournalModel();
        $journal = $journalModel->where('id', $journalId)
            ->where('active', 1)
            ->first();

        if ($journal === null) {
            throw new \RuntimeException('A revista selecionada não existe ou está inativa.');
        }

        if (trim((string) ($journal['api_key'] ?? '')) === '') {
            throw new \RuntimeException('A revista selecionada não possui APIKEY configurada.');
        }

        $this->apiUrl = $journalModel->getApiUrl($journal);
        $this->apiToken = $journal['api_key'];

        $pageSize = 100;
        $offset = 0;
        $page = 0;
        $allItems = [];
        $seenIds = [];

        do {
            $endPoint = $this->apiUrl . '/issues?isPublished=' . ($isPublished ? '1' : '0')
                . '&count=' . $pageSize
                . '&offset=' . $offset
                . '&apiToken=' . urlencode($this->apiToken);
            $rsp = $this->curl($endPoint, 'GET');

            if ($rsp['httpCode'] !== 200) {
                throw new \RuntimeException('Não foi possível consultar as edições no OJS selecionado (HTTP ' . $rsp['httpCode'] . ').');
            }

            $response = $rsp['response'];
            $items = $response['items'] ?? $response ?? [];
            if (!is_array($items)) {
                break;
            }

            foreach ($items as $item) {
                $itemId = is_array($item) ? ($item['id'] ?? null) : null;
                $key = $itemId !== null ? (string) $itemId : md5(json_encode($item));
                if (!isset($seenIds[$key])) {
                    $seenIds[$key] = true;
                    $allItems[] = $item;
                }
            }

            $returned = count($items);
            $offset += $returned;
            $itemsMax = isset($response['itemsMax']) ? (int) $response['itemsMax'] : $offset;
            $page++;
        } while ($returned > 0 && $offset < $itemsMax && $page < 100);

        return json_decode(json_encode($allItems, JSON_UNESCAPED_UNICODE)) ?? [];
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
        $url = $this->apiUrl . "/submissions/{$submissionId}/files";

        if (!file_exists($filePath)) {
            return [
                "status" => 400,
                "error" => "Arquivo não encontrado localmente."
            ];
        }

        $file = new \CURLFile(
            $filePath,
            'application/pdf',
            basename($filePath)
        );

        $postFields = [
            'file' => $file,
            'name[' . $locale . ']' => basename($filePath),
            'genreId' => '1',
            'fileStage' => '2'
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $postFields,

            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: Bearer ' . $this->apiToken
            ],

            CURLOPT_USERAGENT => 'Brapci-OJS-Client/1.0',

            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,

            CURLOPT_VERBOSE => true
        ]);

        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        rewind($verbose);
        $debug = stream_get_contents($verbose);

        curl_close($ch);

        return [
            "status" => $httpCode,
            "raw" => $response,
            "decoded" => json_decode($response, true),
            "curl_error" => $curlError,
            "debug" => $debug
        ];
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
    public function uploadFileOJS($submitId, $filePath, int $fileStage = 2)
    {
        $url = $this->apiUrl . "/submissions/{$submitId}/files";

        if (!file_exists($filePath)) {
            return [
                "status" => 400,
                "error" => "Arquivo não encontrado localmente."
            ];
        }

        $file = new \CURLFile(
            $filePath,
            'application/pdf',
            basename($filePath)
        );

        $postFields = [
            'file' => $file,
            'name[' . 'pt_BR' . ']' => basename($filePath),
            'genreId' => '1',
            'fileStage' => '2'
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $postFields,

            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: Bearer ' . $this->apiToken
            ],

            CURLOPT_USERAGENT => 'Brapci-OJS-Client/1.0',

            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,

            CURLOPT_VERBOSE => true
        ]);

        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        rewind($verbose);
        $debug = stream_get_contents($verbose);

        curl_close($ch);

        return [
            "status" => $httpCode,
            "raw" => $response,
            "decoded" => json_decode($response, true),
            "curl_error" => $curlError,
            "debug" => $debug
        ];
    }
}
