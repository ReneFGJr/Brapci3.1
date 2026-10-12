<?php

namespace App\Models\Api\Endpoint;

use App\Models\Pensador\Pensador;
use App\Models\Pensador\WikiImport;

class Pensadores
{
    /**
     * Lista pública dos pensadores cadastrados, enriquecida com a primeira foto local disponível.
     *
     * @return array<int, array<string, mixed>>
     */
    public function index(string $id = ''): array
    {
        if ($id !== '') {
            return $this->findOne($id);
        }

        $pensadores = (new Pensador())
            ->orderBy('nome', 'ASC')
            ->findAll();
        $wiki = new WikiImport();

        foreach ($pensadores as &$pensador) {
            $pensador['foto'] = null;
            $id = (int) ($pensador['id'] ?? 0);

            if ($id < 1) {
                continue;
            }

            try {
                $dados = $wiki->read($id);
                $arquivo = $dados['fotos'][0]['arquivo'] ?? null;
                if (is_string($arquivo) && $arquivo !== '') {
                    $caminho = $wiki->directory($id) . $arquivo;
                    if (is_file($caminho)) {
                        $pensador['foto'] = base_url(
                            '_repository/thinkers/' . $id . '/' . rawurlencode($arquivo)
                        );
                    }
                }
            } catch (\RuntimeException $error) {
                log_message('warning', 'Foto do pensador {id}: {message}', [
                    'id' => $id,
                    'message' => $error->getMessage(),
                ]);
            }
        }
        unset($pensador);

        return $pensadores;
    }

    private function findOne(string $id): array
    {
        if (!ctype_digit($id) || (int) $id < 1) {
            http_response_code(404);
            return ['status' => 404, 'message' => 'Pensador não encontrado.'];
        }

        $pensador = (new Pensador())->find((int) $id);
        if (!$pensador) {
            http_response_code(404);
            return ['status' => 404, 'message' => 'Pensador não encontrado.'];
        }

        $wiki = new WikiImport();
        $pensador['foto'] = null;
        $pensador['fotos'] = [];
        $pensador['dados'] = [];

        try {
            $wikiData = $wiki->read((int) $id);
            $pensador['dados'] = $wikiData['dados'] ?? [];
            foreach ($wikiData['fotos'] ?? [] as $foto) {
                $arquivo = $foto['arquivo'] ?? null;
                if (is_string($arquivo) && $arquivo !== '' && is_file($wiki->directory((int) $id) . $arquivo)) {
                    $pensador['fotos'][] = base_url(
                        '_repository/thinkers/' . (int) $id . '/' . rawurlencode($arquivo)
                    );
                }
            }
            $pensador['foto'] = $pensador['fotos'][0] ?? null;
        } catch (\RuntimeException $error) {
            log_message('warning', 'Dados do pensador {id}: {message}', [
                'id' => (int) $id,
                'message' => $error->getMessage(),
            ]);
        }

        return $pensador;
    }
}
