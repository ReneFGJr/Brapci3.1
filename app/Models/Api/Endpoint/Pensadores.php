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
    public function index(): array
    {
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
}
