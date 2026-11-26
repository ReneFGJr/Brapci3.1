<?php

namespace App\Models\Api\Endpoint\Cited;

use App\Models\Pensador\CitedPensador;

class Coupling
{
    /** GET /api/cited/coupling/{ID}; ranks thinkers by shared cited works. */
    public function author(string $id): array
    {
        $thinkerId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($thinkerId === false) {
            http_response_code(422);
            return ['status' => 422, 'message' => 'Informe um ID de pensador válido.'];
        }

        return (new CitedPensador())->couplingByThinker($thinkerId);
    }

    /** GET /api/cited/coupling/{ID}/timeline; years belong to shared cited works. */
    public function timeline(string $id): array
    {
        $thinkerId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($thinkerId === false) {
            http_response_code(422);
            return ['status' => 422, 'message' => 'Informe um ID de pensador válido.'];
        }

        return (new CitedPensador())->couplingTimelineByThinker($thinkerId);
    }
}
