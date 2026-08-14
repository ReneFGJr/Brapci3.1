<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class RisModel extends Model
{
    protected $DBGroup    = 'brapci_labs';
    protected $table      = 'brapci_ris';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'ris_type',
        'title',
        'authors',
        'journal',
        'year',
        'url',
        'doi',
        'project_id',
        'abstract',
        'keywords',
        'status',
        'raw_hash'
    ];

    protected $useTimestamps = false;

    /**
     * Verifica se já existe um registro pelo hash
     */

    public function brapciID($url)
    {
        $url = str_replace('https://hdl.handle.net/20.500.11959/brapci/','',$url);
        $url = round($url);
        return $url;
    }
    public function existsHash(string $hash, int $projectId): bool
    {
        return $this->where('raw_hash', $hash)
            ->where('project_id', $projectId)
            ->countAllResults() > 0;
    }

    public function countByProject(int $projectId): int
    {
        return $this
            ->where('project_id', $projectId)
            ->where('status >= 0')
            ->countAllResults();
    }

    public function countJournalsByProject(int $projectId): int
    {
        $row = $this->db->table($this->table)
            ->select('COUNT(DISTINCT TRIM(journal)) AS total', false)
            ->where('project_id', $projectId)
            ->where('status >=', 0)
            ->where('journal IS NOT NULL', null, false)
            ->where("TRIM(journal) != ''", null, false)
            ->get()
            ->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    public function journalsByProject(int $projectId): array
    {
        return $this->db->table($this->table)
            ->select('MIN(id) AS journal_id, TRIM(journal) AS journal, COUNT(*) AS works_count', false)
            ->where('project_id', $projectId)
            ->where('status >=', 0)
            ->where('journal IS NOT NULL', null, false)
            ->where("TRIM(journal) != ''", null, false)
            ->groupBy('TRIM(journal)', false)
            ->orderBy('journal', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function journalById(int $journalId, int $projectId): ?array
    {
        $reference = $this->db->table($this->table)
            ->select('TRIM(journal) AS journal', false)
            ->where('id', $journalId)
            ->where('project_id', $projectId)
            ->where('status >=', 0)
            ->get()
            ->getRowArray();

        if (! $reference || trim((string) ($reference['journal'] ?? '')) === '') {
            return null;
        }

        $journal = trim($reference['journal']);
        $works = $this->db->table($this->table)
            ->select('id, title, authors, year, doi, url')
            ->where('project_id', $projectId)
            ->where('status >=', 0)
            ->where(
                'TRIM(journal) = ' . $this->db->escape($journal),
                null,
                false
            )
            ->orderBy('year', 'DESC')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'id' => $journalId,
            'journal' => $journal,
            'works' => $works,
            'works_count' => count($works),
        ];
    }

    /**
     * Recupera trabalhos da BRAPCI pelo ID armazenado no dataset do Elastic.
     */
    public function findDatasetByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $elastic = db_connect('elastic');

        return $elastic->table('brapci_elastic.dataset AS dataset')
            ->select(
                'dataset.ID AS brapci_id, dataset.DOI AS doi, dataset.CLASS AS class, '
                . 'dataset.JOURNAL AS journal, dataset.PUBLICATION AS publication, '
                . 'dataset.TITLE AS title, dataset.AUTHORS AS authors, '
                . 'dataset.KEYWORDS AS keywords, dataset.ABSTRACTS AS abstracts, '
                . 'dataset.YEAR AS year, dataset.URL AS url',
                false
            )
            ->whereIn('dataset.ID', $ids)
            ->get()
            ->getResultArray();
    }
}
