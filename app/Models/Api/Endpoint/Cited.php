<?php

namespace App\Models\Api\Endpoint;

use App\Models\Pensador\CitedPensador;

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

        return (new CitedPensador())->thinkersCitedByAuthor($authorId);
    }
}
