<?php

namespace App\Models\DOI;

use CodeIgniter\Model;

class Cited_tmp extends Model
{
    protected $DBGroup = 'brapci_cited';
    protected $table = 'cited_temp';
    protected $primaryKey = 'id_cc';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['cc_text', 'ca_first', 'ca_doi', 'cc_status'];
    protected $useTimestamps = false;
    protected $beforeInsert = ['preencherCaFirst'];
    protected $beforeInsertBatch = ['preencherCaFirstLote'];

    /** Importa uma referência por linha, sem repetir textos já cadastrados. */
    public function importar(string $texto): array
    {
        $referencias = preg_split('/\R/u', $texto) ?: [];
        $counts = [
            'importadas' => 0,
            'existentes' => 0,
            'vazias' => 0,
            'similares' => [],
            'dois_completados' => 0,
        ];

        $lock = 'brapci_cited.cited_temp_import';
        $acquired = $this->db->query('SELECT GET_LOCK(?, 30) AS acquired', [$lock])->getRowArray();
        if ((int) ($acquired['acquired'] ?? 0) !== 1) {
            throw new \RuntimeException('Não foi possível iniciar a importação. Tente novamente.');
        }

        try {
            if (! $this->db->transBegin()) {
                throw new \RuntimeException('Falha ao iniciar a transação.');
            }

            $this->preencherPrimeirasPalavrasPendentes();

            $anteriores = [];
            $indiceExato = [];
            foreach ($this->select('id_cc, cc_text, ca_doi')->findAll() as $existente) {
                $anterior = $this->normalizarEspacos((string) $existente['cc_text']);
                $comparavel = $this->normalizarComparacao($anterior);
                if ($comparavel !== '' && ! isset($indiceExato[$comparavel])) {
                    $indiceExato[$comparavel] = count($anteriores);
                    $anteriores[] = [
                        'texto' => $anterior,
                        'comparavel' => $comparavel,
                        'tamanho' => strlen($comparavel),
                        'id_cc' => (int) $existente['id_cc'],
                        'indice_lote' => null,
                        'ca_doi' => trim((string) ($existente['ca_doi'] ?? '')),
                    ];
                }
            }

            $agora = [];
            foreach ($referencias as $referencia) {
                $doiAtual = $this->extrairDoi((string) $referencia);
                $referencia = $this->prepararReferencia((string) $referencia);
                if ($referencia === '') {
                    $counts['vazias']++;
                    continue;
                }

                $comparavel = $this->normalizarComparacao($referencia);
                $similar = null;
                if (isset($indiceExato[$comparavel])) {
                    $similar = [
                        'indice' => $indiceExato[$comparavel],
                        'registro' => $anteriores[$indiceExato[$comparavel]],
                        'percentual' => 100.0,
                    ];
                } else {
                    $similar = $this->localizarSimilar($comparavel, $anteriores);
                }

                if ($similar !== null) {
                    $doiCompletado = '';
                    if ($similar['percentual'] > 80 && $doiAtual !== ''
                        && $similar['registro']['ca_doi'] === '') {
                        if ($similar['registro']['id_cc'] !== 0) {
                            if (! $this->update($similar['registro']['id_cc'], ['ca_doi' => $doiAtual])) {
                                throw new \RuntimeException('Falha ao completar o DOI da referência existente.');
                            }
                        } else {
                            $agora[$similar['registro']['indice_lote']]['ca_doi'] = $doiAtual;
                        }
                        $anteriores[$similar['indice']]['ca_doi'] = $doiAtual;
                        $doiCompletado = $doiAtual;
                        $counts['dois_completados']++;
                    }

                    $counts['existentes']++;
                    $counts['similares'][] = [
                        'anterior' => $similar['registro']['texto'],
                        'atual' => $referencia,
                        'percentual' => $similar['percentual'],
                        'doi_completado' => $doiCompletado,
                    ];
                    continue;
                }

                $indiceLote = count($agora);
                $agora[] = [
                    'cc_text' => $referencia,
                    'ca_first' => $this->primeiraPalavra($referencia),
                    'ca_doi' => $doiAtual,
                    'cc_status' => 0,
                ];
                $indiceExato[$comparavel] = count($anteriores);
                $anteriores[] = [
                    'texto' => $referencia,
                    'comparavel' => $comparavel,
                    'tamanho' => strlen($comparavel),
                    'id_cc' => 0,
                    'indice_lote' => $indiceLote,
                    'ca_doi' => $doiAtual,
                ];
            }

            foreach (array_chunk($agora, 500) as $lote) {
                if ($this->insertBatch($lote) === false) {
                    throw new \RuntimeException('Falha ao importar as referências.');
                }
                $counts['importadas'] += count($lote);
            }

            if (! $this->db->transStatus() || ! $this->db->transCommit()) {
                throw new \RuntimeException('Falha ao concluir a importação.');
            }

            return $counts;
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', [$lock]);
        }
    }

    private function normalizarEspacos(string $texto): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $texto));
    }

    /** Remove o campo de disponibilidade e tudo o que vier depois dele. */
    private function prepararReferencia(string $referencia): string
    {
        $referencia = (string) preg_replace('/\s*dispon[ií]vel\s+em\b.*$/iu', '', $referencia);
        return $this->normalizarEspacos($referencia);
    }

    private function normalizarComparacao(string $texto): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        return strtolower($ascii === false ? $texto : $ascii);
    }

    /** Extrai somente o identificador DOI, sem URL ou prefixo. */
    private function extrairDoi(string $referencia): string
    {
        if (! preg_match('/10\.\d{4,9}\/[-._;()\/:a-z0-9]+/iu', $referencia, $resultado)) {
            return '';
        }

        return strtolower(rtrim($resultado[0], '.,;:'));
    }

    /** Extrai a primeira palavra da referência que tenha mais de dois caracteres. */
    private function primeiraPalavra(string $referencia): string
    {
        if (! preg_match_all('/[\p{L}\p{N}]+/u', $referencia, $palavras)) {
            return '';
        }

        foreach ($palavras[0] as $palavra) {
            if (mb_strlen($palavra, 'UTF-8') > 2) {
                return $palavra;
            }
        }

        return '';
    }

    /** Garante ca_first também nas inserções unitárias feitas diretamente pelo model. */
    protected function preencherCaFirst(array $evento): array
    {
        if (! empty($evento['data']['cc_text']) && empty($evento['data']['ca_first'])) {
            $evento['data']['ca_first'] = $this->primeiraPalavra((string) $evento['data']['cc_text']);
        }

        return $evento;
    }

    /** Garante ca_first em todos os itens de uma inserção em lote. */
    protected function preencherCaFirstLote(array $evento): array
    {
        foreach ($evento['data'] ?? [] as &$registro) {
            if (! empty($registro['cc_text']) && empty($registro['ca_first'])) {
                $registro['ca_first'] = $this->primeiraPalavra((string) $registro['cc_text']);
            }
        }
        unset($registro);

        return $evento;
    }

    /** Completa registros antigos criados antes da existência da coluna ca_first. */
    private function preencherPrimeirasPalavrasPendentes(): void
    {
        $pendentes = $this->select('id_cc, cc_text')
            ->groupStart()
                ->where('ca_first IS NULL', null, false)
                ->orWhere('ca_first', '')
            ->groupEnd()
            ->findAll();

        foreach (array_chunk($pendentes, 500) as $lote) {
            $atualizacoes = [];
            foreach ($lote as $registro) {
                $atualizacoes[] = [
                    'id_cc' => $registro['id_cc'],
                    'ca_first' => $this->primeiraPalavra((string) $registro['cc_text']),
                ];
            }

            if ($atualizacoes !== [] && $this->updateBatch($atualizacoes, 'id_cc') === false) {
                throw new \RuntimeException('Falha ao preencher a primeira palavra das referências existentes.');
            }
        }
    }

    /** Retorna a referência mais parecida somente quando a similaridade for maior que 70%. */
    private function localizarSimilar(string $atual, array $anteriores): ?array
    {
        $tamanhoAtual = strlen($atual);
        $melhor = null;

        foreach ($anteriores as $indice => $anterior) {
            $maiorTamanho = max($tamanhoAtual, $anterior['tamanho']);
            if ($maiorTamanho === 0 || min($tamanhoAtual, $anterior['tamanho']) / $maiorTamanho <= 0.70) {
                continue;
            }

            $percentual = (1 - levenshtein($atual, $anterior['comparavel']) / $maiorTamanho) * 100;
            if ($percentual > 70 && ($melhor === null || $percentual > $melhor['percentual'])) {
                $melhor = [
                    'indice' => $indice,
                    'registro' => $anterior,
                    'percentual' => round($percentual, 2),
                ];
            }
        }

        return $melhor;
    }
}
