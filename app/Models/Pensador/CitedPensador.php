<?php

namespace App\Models\Pensador;

use CodeIgniter\Model;

class CitedPensador extends Model
{
    protected $DBGroup = 'brapci_cited';
    protected $table = 'cited_pensador';
    protected $allowedFields = ['pensador_id', 'cited_normalize_id'];
    protected $useTimestamps = false;

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
