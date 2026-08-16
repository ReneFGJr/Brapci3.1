<?php

namespace App\Services\Ai;

use App\Models\RDF2\RDFdata;
use App\Models\RDF2\RDF;
use App\Models\RDF2\RDFmetadata;

class RepositoryDocumentService
{
    private const PROCESS_URL = 'https://cip.brapci.inf.br/tools/nlp/fulltext/';

    public function commandId(string $content): ?int
    {
        if (! preg_match('/^\s*carregue\s+(\d+)\s*$/iu', $content, $matches)) {
            return null;
        }

        $id = (int) $matches[1];
        return $id > 0 ? $id : null;
    }

    public function load(int $requestedId): ?string
    {
        $workId = $this->workId($requestedId);
        $id = str_pad((string) $workId, 8, '0', STR_PAD_LEFT);
        $file = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR
            . '_repository' . DIRECTORY_SEPARATOR
            . substr($id, 0, 2) . DIRECTORY_SEPARATOR
            . substr($id, 2, 2) . DIRECTORY_SEPARATOR
            . substr($id, 4, 2) . DIRECTORY_SEPARATOR
            . substr($id, 6, 2) . DIRECTORY_SEPARATOR
            . 'work_' . $id . '#00000.md';

        if (! is_file($file) || ! is_readable($file)) {
            return null;
        }

        $content = file_get_contents($file);
        return $content === false ? null : $content;
    }

    public function requestProcessing(int $requestedId): void
    {
        $curl = curl_init(self::PROCESS_URL . $requestedId);
        if ($curl === false) {
            log_message('error', '[AI repository] Nao foi possivel iniciar o processamento do documento ' . $requestedId);
            return;
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error !== '') {
            log_message('warning', '[AI repository] Falha ao solicitar o processamento do documento ' . $requestedId . ': ' . $error);
        }
    }

    public function metadata(int $requestedId): array
    {
        $workId = $this->workId($requestedId);
        $rdf = new RDF();
        $record = (new RDFmetadata())->metadataWork($rdf->le($workId), true);
        $authors = array_values(array_filter(array_map(
            static fn (array $author): string => trim((string) ($author['name'] ?? '')),
            $record['creator_author'] ?? [],
        )));

        return [
            'title' => trim((string) ($record['title'] ?? 'Título não informado')) ?: 'Título não informado',
            'authors' => $authors ?: ['Autoria não informada'],
            'year' => trim((string) ($record['year'] ?? 'Ano não informado')) ?: 'Ano não informado',
            'publication' => trim((string) ($record['publisher'] ?? 'Local de publicação não informado')) ?: 'Local de publicação não informado',
        ];
    }

    private function workId(int $requestedId): int
    {
        // O comando pode trazer o ID do arquivo RDF (por exemplo, 384240),
        // enquanto o markdown usa o ID do Work relacionado (por exemplo, 384965).
        $relations = (new RDFdata())->le($requestedId);
        foreach ($relations as $relation) {
            if (($relation['Property'] ?? '') !== 'hasFileStorage') {
                continue;
            }
            if (($relation['Class'] ?? '') === 'Work' && (int) ($relation['ID'] ?? 0) > 0) {
                return (int) $relation['ID'];
            }
        }

        return $requestedId;
    }
}
