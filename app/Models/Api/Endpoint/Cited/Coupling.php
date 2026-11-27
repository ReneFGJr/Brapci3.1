<?php

namespace App\Models\Api\Endpoint\Cited;

use App\Models\Pensador\CitedPensador;
use App\Models\Pensador\Pensador;
use App\Models\Pensador\WikiImport;

class Coupling
{
    /** GET /api/cited/coupling/{ID}; returns the Pajek NET coupling graph. */
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
        $acoplamento = $citedPensador->couplingRankingByThinker($thinkerId);

        return [
            'pensador' => $this->basicData($pensador),
            'coautores' => $coautores,
            'acoplamento' => $acoplamento,
        ];
    }

    /**
     * @return array<string, mixed>|string
     */
    public function net(string $id)
    {
        $thinkerId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($thinkerId === false) {
            http_response_code(422);
            return ['status' => 422, 'message' => 'Informe um ID de pensador válido.'];
        }

        if (!(new Pensador())->find($thinkerId)) {
            http_response_code(404);
            return ['status' => 404, 'message' => 'Pensador não encontrado.'];
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

        $pensador = (new Pensador())->find($thinkerId);
        if (!$pensador) {
            http_response_code(404);
            return ['status' => 404, 'message' => 'Pensador não encontrado.'];
        }

        $citedPensador = new CitedPensador();
        $coautores = $citedPensador->coauthorsByThinker($thinkerId);
        $acoplamento = $this->addPhotos($citedPensador->couplingTimelineByThinker($thinkerId));

        return [
            'pensador' => $this->basicData($pensador),
            'coautores' => $coautores,
            'acoplamento' => $acoplamento,
        ];
    }

    private function basicData(array $pensador): array
    {
        $id = (int) $pensador['id'];

        return [
            'id' => $id,
            'nome' => $pensador['nome'],
            'nome_citacao' => $pensador['nome_citacao'],
            'foto' => $this->photoUrl($id),
        ];
    }

    private function photoUrl(int $id): ?string
    {
        $wiki = new WikiImport();
        try {
            foreach ($wiki->read($id)['fotos'] ?? [] as $photo) {
                $filename = $photo['arquivo'] ?? null;
                if (is_string($filename) && $filename !== ''
                    && is_file($wiki->directory($id) . $filename)) {
                    return base_url(
                        '_repository/thinkers/' . $id . '/' . rawurlencode($filename)
                    );
                }
            }
        } catch (\RuntimeException $error) {
            log_message('warning', 'Foto do pensador {id}: {message}', [
                'id' => $id,
                'message' => $error->getMessage(),
            ]);
        }

        return null;
    }

    private function addPhotos(array $thinkers): array
    {
        foreach ($thinkers as &$thinker) {
            $thinker['foto'] = $this->photoUrl((int) $thinker['id']);
        }
        unset($thinker);

        return $thinkers;
    }
}
