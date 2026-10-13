<?php

namespace App\Models\Api\Endpoint;

use App\Models\Pensador\CitedPensador;
use App\Models\Pensador\WikiImport;

class Cited
{
    /** GET /api/cited/author/{ID}; ID é o identificador RDF do autor. */
    public function author(string $id): array
    {
        $authorId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($authorId === false) {
            http_response_code(422);
            return ['status' => 422, 'message' => 'Informe um ID de autor válido.'];
        }

        $pensadores = (new CitedPensador())->thinkersCitedByAuthor($authorId);
        $wiki = new WikiImport();
        foreach ($pensadores as &$pensador) {
            $pensador['foto'] = null;
            $pensadorId = (int) $pensador['id'];
            try {
                $dados = $wiki->read($pensadorId);
                $arquivo = $dados['fotos'][0]['arquivo'] ?? null;
                if (is_string($arquivo) && $arquivo !== ''
                    && is_file($wiki->directory($pensadorId) . $arquivo)) {
                    $pensador['foto'] = base_url(
                        '_repository/thinkers/' . $pensadorId . '/' . rawurlencode($arquivo)
                    );
                }
            } catch (\RuntimeException $error) {
                log_message('warning', 'Foto do pensador {id}: {message}', [
                    'id' => $pensadorId,
                    'message' => $error->getMessage(),
                ]);
            }
        }
        unset($pensador);

        return $pensadores;
    }
}
