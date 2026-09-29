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

    public function citingWorks(int $normalizedId): array
    {

        $works = $this->db->table('cited_article a')
            ->select('a.ca_rdf, MIN(NULLIF(a.ca_year_origem, 0)) AS year', false)
            ->join('cited_normalize n', $this->citationMatch(), 'inner', false)
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

    private function candidatesQuery(array $pensador)
    {
        $id = (int) $pensador['id'];
        $surname = self::surname($pensador);
        $builder = $this->db->table('cited_normalize n')
            ->select('n.id_ca, n.ca_text, n.ca_authors, n.ca_year, n.ca_doi')
            ->join('cited_pensador p', 'p.cited_normalize_id = n.id_ca AND p.pensador_id = ' . $id, 'left')
            ->where('p.pensador_id', null);
        if ($surname === '') {
            return $builder->where('1 = 0', null, false);
        }
        return $builder->groupStart()->like('n.ca_text', $surname)->orLike('n.ca_authors', $surname)->groupEnd();
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
