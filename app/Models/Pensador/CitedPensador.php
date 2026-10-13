<?php

namespace App\Models\Pensador;

use CodeIgniter\Model;

class CitedPensador extends Model
{
    protected $DBGroup = 'brapci_cited';
    protected $table = 'cited_pensador';
    protected $allowedFields = ['pensador_id', 'cited_normalize_id'];
    protected $useTimestamps = false;
    protected string $datasetTable = 'brapci_elastic.dataset';
    protected string $authorDataTable = 'brapci_rdf.rdf_data';
    protected string $authorClassTable = 'brapci_rdf.rdf_class';
    protected string $thinkerTable = 'brapci.pensadores';

    public function unlinkWork(int $thinkerId, int $workId): void
    {
        $this->db->table('cited_pensador')->where('pensador_id', $thinkerId)
            ->where('cited_normalize_id', $workId)->delete();
    }

    public function groupLinkedWorks(int $thinkerId, array $ids, int $targetId): void
    {
        foreach ($ids as $id) {
            if (!is_scalar($id) || !preg_match('/^[1-9][0-9]*$/', (string) $id)) {
                throw new \InvalidArgumentException('Seleção inválida.');
            }
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (count($ids) < 2 || !in_array($targetId, $ids, true)) {
            throw new \InvalidArgumentException('Selecione ao menos duas obras e um ID de destino entre elas.');
        }
        $this->db->transBegin();
        try {
            $marks = implode(',', array_fill(0, count($ids), '?'));
            $linked = $this->db->query('SELECT cited_normalize_id FROM cited_pensador WHERE pensador_id = ? '
                . 'AND cited_normalize_id IN (' . $marks . ') FOR UPDATE', array_merge([$thinkerId], $ids))->getResultArray();
            if (count($linked) !== count($ids)) {
                throw new \InvalidArgumentException('Uma obra não está mais vinculada ao pensador.');
            }
            $sources = array_values(array_diff($ids, [$targetId]));
            $marks = implode(',', array_fill(0, count($sources), '?'));
            // Materializa também as correspondências por DOI antes de remover as duplicatas.
            $this->db->query('UPDATE cited_article a JOIN cited_normalize n ON ' . $this->citationMatch()
                . ' SET a.ca_normalized = ? WHERE n.id_ca IN (' . $marks . ')', array_merge([$targetId], $sources));
            $thinkers = $this->db->table('cited_pensador')->distinct()->select('pensador_id')
                ->whereIn('cited_normalize_id', $sources)->get()->getResultArray();
            foreach ($thinkers as $thinker) {
                $this->db->query('INSERT IGNORE INTO cited_pensador (pensador_id, cited_normalize_id) VALUES (?, ?)',
                    [$thinker['pensador_id'], $targetId]);
            }
            $this->db->table('cited_pensador')->whereIn('cited_normalize_id', $sources)->delete();
            $this->db->table('cited_normalize')->whereIn('id_ca', $sources)->delete();
            $this->refreshCitationCounts([$targetId]);
            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Não foi possível agrupar as obras.');
            }
            $this->db->transCommit();
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    public function thinkersCitedByAuthor(int $authorId): array
    {
        // EXISTS evita multiplicar as citações quando há vínculos RDF repetidos.
        $works = $this->db->table($this->authorDataTable . ' d')
            ->select('1', false)
            ->join($this->authorClassTable . ' c', 'c.id_c = d.d_p')
            ->where('c.c_class', 'hasAuthor')
            ->where('d.d_r2', $authorId)
            ->where('d.d_r1 = a.ca_rdf', null, false)
            ->getCompiledSelect();

        $rows = $this->db->table('cited_article a')
            ->select('t.id, t.nome, COUNT(DISTINCT a.id_ca) AS total', false)
            ->join('cited_normalize n', $this->citationMatch(), 'inner', false)
            ->join('cited_pensador p', 'p.cited_normalize_id = n.id_ca')
            ->join($this->thinkerTable . ' t', 't.id = p.pensador_id')
            ->where('a.ca_rdf >', 0)
            ->where('EXISTS (' . $works . ')', null, false)
            ->groupBy('t.id, t.nome')
            ->orderBy('total', 'DESC')->orderBy('t.nome', 'ASC')->orderBy('t.id', 'ASC')
            ->get()->getResultArray();
        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['total'] = (int) $row['total'];
        }
        unset($row);
        return $rows;
    }

    public function linkedWorks(int $pensadorId): array
    {
        return $this->db->table('cited_pensador p')
            ->select('n.id_ca, n.ca_text, n.ca_authors, n.ca_year, n.ca_doi')
            ->select($this->db->fieldExists('ca_cited', 'cited_normalize')
                ? 'n.ca_cited AS cited_by'
                : 'NULL AS cited_by', false)
            ->join('cited_normalize n', 'n.id_ca = p.cited_normalize_id')
            ->where('p.pensador_id', $pensadorId)
            ->orderBy('n.ca_year', 'DESC')->orderBy('n.id_ca', 'ASC')
            ->get()->getResultArray();
    }

    public function groupedReferences(int $normalizedId): array
    {
        return $this->db->table('cited_article a')
            ->select('a.id_ca, a.ca_rdf, a.ca_text, a.ca_doi, a.ca_year, a.ca_year_origem')
            ->join('cited_normalize n', 'n.id_ca = a.ca_normalized')
            ->where('n.id_ca', $normalizedId)
            ->orderBy('a.id_ca', 'ASC')->get()->getResultArray();
    }

    public function citingWorks(int $normalizedId): array
    {

        $works = $this->db->table('cited_article a')
            ->select('a.ca_rdf, MIN(NULLIF(a.ca_year_origem, 0)) AS year', false)
            ->join('cited_normalize n', 'n.id_ca = a.ca_normalized')
            ->where('n.id_ca', $normalizedId)->where('a.ca_rdf >', 0)
            ->groupBy('a.ca_rdf')
            ->orderBy('a.ca_rdf', 'ASC')
            ->get()->getResultArray();
        $metadata = [];
        foreach (array_chunk(array_column($works, 'ca_rdf'), 500) as $ids) {
            $rows = $this->db->table($this->datasetTable)
                ->select('ID, MAX(TITLE) AS title, MAX(AUTHORS) AS authors, MIN(NULLIF(YEAR, 0)) AS year', false)
                ->whereIn('ID', $ids)->groupBy('ID')->get()->getResultArray();
            foreach ($rows as $row) { $metadata[$row['ID']] = $row; }
        }
        foreach ($works as &$work) {
            $record = $metadata[$work['ca_rdf']] ?? [];
            $work['title'] = $record['title'] ?? null;
            $work['authors'] = $record['authors'] ?? null;
            $work['year'] = $record['year'] ?? $work['year'];
        }
        unset($work);
        // Ordena após combinar o ano do dataset com o ano de origem da citação.
        usort($works, static fn ($a, $b) => (($a['year'] ?? PHP_INT_MAX) <=> ($b['year'] ?? PHP_INT_MAX)) ?: ($a['ca_rdf'] <=> $b['ca_rdf']));
        return $works;
    }

    public function recalculateForThinker(int $id): void
    {
        if (!$this->db->fieldExists('ca_cited', 'cited_normalize')) {
            throw new \RuntimeException('A coluna ca_cited ainda não existe. Aplique a migration de citações antes de recalcular.');
        }
        $ids = array_column($this->db->table('cited_pensador')->select('cited_normalize_id')
            ->where('pensador_id', $id)->get()->getResultArray(), 'cited_normalize_id');
        $this->refreshCitationCounts($ids);
    }

    public function refreshCitationCounts(array $ids): void
    {
        // Compatibilidade com bases nas quais a migration ainda não foi aplicada.
        if ($ids === [] || !$this->db->fieldExists('ca_cited', 'cited_normalize')) {
            return;
        }
        foreach (array_chunk(array_unique(array_map('intval', $ids)), 500) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $this->db->query('UPDATE cited_normalize n SET ca_cited = (
                SELECT COUNT(DISTINCT a.ca_rdf) FROM cited_article a
                WHERE ' . $this->citationMatch() . ' AND a.ca_rdf > 0
            ) WHERE n.id_ca IN (' . $placeholders . ')', $chunk);
        }
    }
    private function citationMatch(): string
    {
        $doi = static function (string $field): string {
            $sql = 'LOWER(TRIM(' . $field . '))';
            foreach (['https://dx.doi.org/', 'http://dx.doi.org/', 'https://doi.org/', 'http://doi.org/', 'doi:'] as $prefix) {
                $sql = "REPLACE($sql, '$prefix', '')";
            }
            return 'TRIM(' . $sql . ')';
        };
        $articleDoi = $doi('a.ca_doi');
        $normalizedDoi = $doi('n.ca_doi');
        return '(a.ca_normalized = n.id_ca OR ((a.ca_normalized IS NULL OR a.ca_normalized = 0) '
            . "AND $normalizedDoi LIKE '10.%/%' AND $articleDoi = $normalizedDoi))";
    }

    public static function surname(array $pensador): string
    {
        $citation = trim((string) ($pensador['nome_citacao'] ?? ''));
        if (str_contains($citation, ',')) {
            return trim(explode(',', $citation, 2)[0]);
        }
        $name = trim((string) ($pensador['nome'] ?? $citation));
        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
        return $parts ? (string) end($parts) : '';
    }

    public static function citationName(array $pensador): string
    {
        return trim((string) ($pensador['nome_citacao'] ?? ''));
    }

    private function candidatesQuery(array $pensador)
    {
        $id = (int) $pensador['id'];
        $this->ensureDailyCache($pensador);
        $builder = $this->db->table('cited_normalize n')
            ->distinct()
            ->select('n.id_ca, n.ca_text, n.ca_authors, n.ca_year, n.ca_doi')
            ->join('cited_article a', $this->citationMatch(), 'inner', false)
            ->join('cached c', 'c.cited_article_id = a.id_ca AND c.pensador_id = ' . $id
                . ' AND c.cache_date = ' . $this->db->escape(date('Y-m-d')), 'inner', false)
            ->join('cited_pensador p', 'p.cited_normalize_id = n.id_ca AND p.pensador_id = ' . $id, 'left')
            ->where('p.pensador_id', null);
        return $builder;
    }

    /**
     * Gera uma vez ao dia a relação entre o pensador e as citações brutas que
     * contêm o nome de citação cadastrado para o pensador.
     */
    private function ensureDailyCache(array $pensador): void
    {
        $pensadorId = (int) ($pensador['id'] ?? 0);
        $searchTerm = self::citationName($pensador);
        if ($pensadorId <= 0 || $searchTerm === '') {
            return;
        }

        $today = date('Y-m-d');
        $exists = $this->db->table('cached')
            ->where('pensador_id', $pensadorId)
            ->where('cache_date', $today)
            ->where('search_term', $searchTerm)
            ->countAllResults() > 0;
        if ($exists) {
            return;
        }

        $this->db->transStart();
        $this->db->table('cached')->where('pensador_id', $pensadorId)
            ->groupStart()->where('cache_date !=', $today)->orWhere('search_term !=', $searchTerm)->groupEnd()
            ->delete();
        $this->db->query(
            'INSERT IGNORE INTO cached (pensador_id, cited_article_id, cache_date, search_term, created_at) '
            . 'SELECT ?, a.id_ca, ?, ?, NOW() FROM cited_article a WHERE a.ca_text LIKE ?',
            [$pensadorId, $today, $searchTerm, '%' . $searchTerm . '%']
        );
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Não foi possível gerar o cache diário de citações do pensador.');
        }
    }

    public function candidates(array $pensador): array
    {
        return $this->candidatesQuery($pensador)->orderBy('n.ca_year', 'DESC')->orderBy('n.id_ca', 'ASC')->get()->getResultArray();
    }

    public function linkSelected(array $pensador, array $ids): int
    {
        if ($ids === []) { return 0; }
        foreach ($ids as $id) {
            if (!is_scalar($id) || !preg_match('/^[1-9][0-9]*$/', (string) $id)) {
                throw new \InvalidArgumentException('Seleção de referências inválida.');
            }
        }
        $ids = array_values(array_unique(array_map('strval', $ids)));
        // Revalida no servidor: somente referências correspondentes e ainda não vinculadas.
        $rows = $this->candidatesQuery($pensador)->whereIn('n.id_ca', $ids)->get()->getResultArray();
        $count = 0;
        $this->db->transBegin();
        try {
            foreach ($rows as $row) {
                $ok = $this->db->query(
                    'INSERT INTO cited_pensador (pensador_id, cited_normalize_id) VALUES (?, ?) '
                    . 'ON DUPLICATE KEY UPDATE cited_normalize_id = VALUES(cited_normalize_id)',
                    [(int) $pensador['id'], $row['id_ca']]
                );
                if (!$ok) { throw new \RuntimeException('Não foi possível vincular as referências.'); }
                $count += $this->db->affectedRows() === 1 ? 1 : 0;
            }
            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Não foi possível vincular as referências.');
            }
            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
        return $count;
    }
}
