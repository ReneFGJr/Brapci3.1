<?php

namespace App\Models\Api\Endpoint\Cited;

use App\Models\Pensador\CitedPensador;
use App\Models\Pensador\Pensador;

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

        $pensador = (new Pensador())->find($thinkerId);
        if (!$pensador) {
            http_response_code(404);
            return ['status' => 404, 'message' => 'Pensador não encontrado.'];
        }

        $citedPensador = new CitedPensador();
        $coautores = $citedPensador->coauthorsByThinker($thinkerId);

        return [
            'pensador' => $this->basicData($pensador),
            'coautores' => $coautores,
            'acoplamento' => $citedPensador->couplingByThinker($thinkerId),
        ];
    }

    /** GET /api/cited/coupling/{ID}/timeline; years belong to shared cited works. */
    public function timeline(string $id): array
    {
        $thinkerId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($thinkerId === false) {
            http_response_code(422);
            return ['status' => 422, 'message' => 'Informe um ID de pensador válido.'];
        }

        $pensador = (new Pensador())->find($thinkerId);
        if (!$pensador) {
            http_response_code(404);
            return ['status' => 404, 'message' => 'Pensador não encontrado.'];
        }

        $citedPensador = new CitedPensador();
        $coautores = $citedPensador->coauthorsByThinker($thinkerId);

        return [
            'pensador' => $this->basicData($pensador),
            'coautores' => $coautores,
            'acoplamento' => $citedPensador->couplingTimelineByThinker($thinkerId),
        ];
    }

    private function basicData(array $pensador): array
    {
        return [
            'id' => (int) $pensador['id'],
            'nome' => $pensador['nome'],
            'nome_citacao' => $pensador['nome_citacao'],
        ];
    }
}
