<?php

namespace App\Models\DOI;

use CodeIgniter\Model;

class DOI_json extends Model
{
    protected $DBGroup          = 'brapci_cited';
    protected $table            = 'doi_json';
    protected $primaryKey       = 'id_doi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'doi_ID',
        'doi_content',
        'doi_status',
        'doi_ref',
        'doi_created_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'doi_created_at';
    protected $updatedField  = '';

    /** Importa os DOIs distintos das referencias para a fila de processamento. */
    public function importarCitedArticle(): array
    {
        $normalize = static function (string $doi): string {
            return strtolower(trim(preg_replace('~^(?:https?://(?:dx\.)?doi\.org/|doi:\s*)~i', '', trim($doi))));
        };
        $counts = ['importados' => 0, 'existentes' => 0, 'invalidos' => 0];
        $lock = 'brapci_cited.crossref_import';
        $acquired = $this->db->query('SELECT GET_LOCK(?, 30) AS acquired', [$lock])->getRowArray();
        if ((int) $acquired['acquired'] !== 1) {
            throw new \RuntimeException('Nao foi possivel iniciar a importacao. Tente novamente.');
        }
        try {
            if (!$this->db->transBegin()) {
                throw new \RuntimeException('Falha ao iniciar a transacao.');
            }
            $known = [];
            foreach ($this->select('doi_ID')->findAll() as $row) {
                $known[$normalize($row['doi_ID'])] = true;
            }
            $rows = $this->db->table('cited_article')->select('ca_doi')->distinct()
                ->where('ca_doi IS NOT NULL', null, false)->where('ca_doi !=', '')->get()->getResultArray();
            $batch = [];
            foreach ($rows as $row) {
                $doi = $normalize($row['ca_doi']);
                if (!preg_match('~^10\.\d{4,9}/\S+$~', $doi) || strlen($doi) > 100) {
                    $counts['invalidos']++;
                    continue;
                }
                if (isset($known[$doi])) {
                    $counts['existentes']++;
                    continue;
                }
                $known[$doi] = true;
                $batch[] = ['doi_ID' => $doi, 'doi_content' => '', 'doi_status' => 0,
                    'doi_created_at' => date('Y-m-d H:i:s')];
                $counts['importados']++;
                if (count($batch) === 500) {
                    if ($this->insertBatch($batch) === false) {
                        throw new \RuntimeException('Falha ao importar os DOIs.');
                    }
                    $batch = [];
                }
            }
            if ($batch && $this->insertBatch($batch) === false) {
                throw new \RuntimeException('Falha ao importar os DOIs.');
            }
            if (!$this->db->transStatus() || !$this->db->transCommit()) {
                throw new \RuntimeException('Falha ao concluir a importacao.');
            }
            return $counts;
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', [$lock]);
        }
    }
    public function countByStatus(): array
    {
        return $this->select('doi_status, COUNT(*) AS total')
            ->groupBy('doi_status')->orderBy('doi_status')->findAll();
    }

    /** Processa um lote de ate 10 DOIs sequencialmente. */
    public function processar(int $status): array
    {
        $resultados = [];
        switch ($status) {
            case 0:
            case 1:
            case 2:
            case 10:
                $registros = $this->where('doi_status', $status)->orderBy('id_doi')->findAll(10);
                foreach ($registros as $registro) {
                    $resultado = ['doi' => $registro['doi_ID'], 'sucesso' => false, 'status' => ($status === 2 ? 3 : 2)];
                    try {
                        $rst = $status === 2 ? $this->getDoiDataCite($registro['doi_ID']) : $this->getDoiCrossref($registro['doi_ID']);
                        $resultado['sucesso'] = !empty($rst);
                        $resultado['status'] = $resultado['sucesso'] ? 10 : ($status === 2 ? 3 : 2);
                        $resultado['mensagem'] = $resultado['sucesso']
                            ? 'Processado com sucesso.' : 'A coleta nao retornou um resultado.';
                    } catch (\Throwable $error) {
                        $resultado['mensagem'] = $error->getMessage();
                    }
                    try {
                        if (!$this->update($registro['id_doi'], ['doi_status' => $resultado['status']])) {
                            throw new \RuntimeException('Falha ao atualizar o status do registro.');
                        }
                    } catch (\Throwable $error) {
                        $resultado['sucesso'] = false;
                        $resultado['status'] = null;
                        $resultado['mensagem'] .= ' Status nao salvo: ' . $error->getMessage();
                    }
                    $resultados[] = $resultado;
                }
                break;
            default:
                break;
        }
        return $resultados;
    }
    /** Fetch Crossref, cache its response and return the normalized record. */
    public function getDoiCrossref(string $doi): array
    {
        $doi = strtolower(trim(preg_replace('~^(?:https?://(?:dx\.)?doi\.org/|doi:\s*)~i', '', trim($doi))));
        if (!preg_match('~^10\.\d{4,9}/\S+$~', $doi) || strlen($doi) > 100) {
            throw new \InvalidArgumentException('DOI invalido ou maior que 100 caracteres.');
        }
        $cached = $this->where('doi_ID', $doi)->first();
        $body = (string) ($cached['doi_content'] ?? '');
        $payload = json_decode($body, true);
        $work = is_array($payload) ? ($payload['message'] ?? null) : null;
        $validCache = is_array($work) && ($payload['status'] ?? '') === 'ok'
            && is_string($work['DOI'] ?? null) && strcasecmp($work['DOI'], $doi) === 0;

        if (!$validCache) {
            $response = \Config\Services::curlrequest()->get('https://api.crossref.org/works/' . rawurlencode($doi), [
                'headers' => ['Accept' => 'application/json', 'User-Agent' => 'Brapci/3.1 (https://brapci.inf.br)'],
                'timeout' => 30, 'connect_timeout' => 10, 'http_errors' => false,
                'verify' => false,
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Crossref retornou HTTP ' . $response->getStatusCode());
            }
            $body = $response->getBody();
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            $work = is_array($payload) ? ($payload['message'] ?? null) : null;
            if (!is_array($work) || ($payload['status'] ?? '') !== 'ok'
                || !is_string($work['DOI'] ?? null) || strcasecmp($work['DOI'], $doi) !== 0) {
                throw new \RuntimeException('Resposta invalida da Crossref.');
            }
        }
        return $this->saveCollectedWork($doi, $body, $work, 1);
    }

    public function getDoiDataCite(string $doi): array
    {
        $doi = strtolower(trim(preg_replace('~^(?:https?://(?:dx\.)?doi\.org/|doi:\s*)~i', '', trim($doi))));
        if (!preg_match('~^10\.\d{4,9}/\S+$~', $doi) || strlen($doi) > 100) {
            throw new \InvalidArgumentException('DOI invalido ou maior que 100 caracteres.');
        }
        $cached = $this->where('doi_ID', $doi)->first();
        $body = (string) ($cached['doi_content'] ?? '');
        $payload = json_decode($body, true);
        $attributes = $payload['data']['attributes'] ?? null;
        if (!is_array($attributes) || !is_string($attributes['doi'] ?? null)
            || strcasecmp($attributes['doi'], $doi) !== 0) {
            $response = \Config\Services::curlrequest()->get('https://api.datacite.org/dois/' . rawurlencode($doi), [
                'headers' => ['Accept' => 'application/json', 'User-Agent' => 'Brapci/3.1 (https://brapci.inf.br)'],
                'timeout' => 30, 'connect_timeout' => 10, 'http_errors' => false, 'verify' => false,
            ]);
            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('DataCite retornou HTTP ' . $response->getStatusCode());
            }
            $body = $response->getBody();
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            $attributes = $payload['data']['attributes'] ?? null;
            if (!is_array($attributes) || !is_string($attributes['doi'] ?? null)
                || strcasecmp($attributes['doi'], $doi) !== 0) {
                throw new \RuntimeException('Resposta invalida da DataCite.');
            }
        }
        $container = $attributes['container'] ?? [];
        $work = [
            'DOI' => $doi,
            'title' => array_column($attributes['titles'] ?? [], 'title'),
            'author' => array_map(static function ($creator) {
                return ['given' => $creator['givenName'] ?? '', 'family' => $creator['familyName'] ?? '',
                    'name' => $creator['name'] ?? ''];
            }, $attributes['creators'] ?? []),
            'published' => ['date-parts' => [[$attributes['publicationYear'] ?? null]]],
            'container-title' => [$container['title'] ?? ''],
            'ISSN' => strtolower($container['identifierType'] ?? '') === 'issn' ? [$container['identifier']] : [],
            'volume' => $container['volume'] ?? null,
            'issue' => $container['issue'] ?? null,
            'page' => isset($container['firstPage']) ? $container['firstPage']
                . (!empty($container['lastPage']) ? '-' . $container['lastPage'] : '') : null,
            'URL' => $attributes['url'] ?? 'https://doi.org/' . $doi,
        ];
        return $this->saveCollectedWork($doi, $body, $work, 2);
    }

    protected function saveCollectedWork(string $doi, string $body, array $work, int $source): array
    {
        $authors = [];
        foreach ($work['author'] ?? [] as $author) {
            $name = trim(($author['given'] ?? '') . ' ' . ($author['family'] ?? ''));
            $name = $name !== '' ? $name : trim($author['name'] ?? '');
            if ($name !== '') {
                $authors[] = $name;
            }
        }
        // The legacy tables lack unique DOI/ISSN keys; serialize this importer.
        $lock = 'brapci_cited.crossref_import';
        $acquired = $this->db->query('SELECT GET_LOCK(?, 30) AS acquired', [$lock])->getRowArray();
        if ((int) $acquired['acquired'] !== 1) {
            throw new \RuntimeException('Falha ao obter bloqueio da importacao.');
        }
        try {
            if (!$this->db->transBegin()) {
                throw new \RuntimeException('Falha ao iniciar transacao.');
            }
            $normalized = new Cited_Normalize($this->db);
            $existing = $normalized->where('ca_doi', $doi)->first();
            if ($existing && (int) $existing['ca_locked'] !== 0) {
                $cached = $this->where('doi_ID', $doi)->first();
                $cache = ['doi_ID' => $doi, 'doi_content' => $body, 'doi_status' => 10, 'doi_ref' => $source];
                $saved = $cached ? $this->update($cached['id_doi'], $cache) : $this->insert($cache);
                if ($saved === false || !$this->db->transStatus() || !$this->db->transCommit()) {
                    throw new \RuntimeException('Falha ao salvar a coleta.');
                }
                return $existing;
            }
            $journal = (new Cited_Journals($this->db))->getOrCreateFromCrossref($work);
            $data = [
                'ca_doi' => $doi,
                'ca_journal' => $journal,
                'ca_year' => $work['published']['date-parts'][0][0] ?? $work['issued']['date-parts'][0][0]
                    ?? $work['published-print']['date-parts'][0][0] ?? $work['published-online']['date-parts'][0][0] ?? null,
                'ca_text' => html_entity_decode(implode(' ', $work['title'] ?? []), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'ca_authors' => json_encode($authors, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'ca_pages' => $work['page'] ?? null,
                'ca_vol' => $work['volume'] ?? null,
                'ca_nr' => $work['issue'] ?? $work['journal-issue']['issue'] ?? null,
                'ca_url' => $work['URL'] ?? 'https://doi.org/' . $doi,
            ];
            $id = $existing ? $existing['id_ca'] : $normalized->insert($data);
            if ($id === false || ($existing && !$normalized->update($id, $data))) {
                throw new \RuntimeException('Falha ao salvar metadados normalizados.');
            }
            $cached = $this->where('doi_ID', $doi)->first();
            $cache = ['doi_ID' => $doi, 'doi_content' => $body, 'doi_status' => 10, 'doi_ref' => $source];
            $saved = $cached ? $this->update($cached['id_doi'], $cache) : $this->insert($cache);
            if ($saved === false || !$this->db->transStatus()) {
                throw new \RuntimeException('Falha ao salvar resposta da Crossref.');
            }
            $result = $normalized->find($id);
            if (!$this->db->transCommit()) {
                throw new \RuntimeException('Falha ao concluir importacao.');
            }
            return $result;
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', [$lock]);
        }
    }
}
