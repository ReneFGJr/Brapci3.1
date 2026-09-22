<?php

namespace App\Models\DOI;

use CodeIgniter\Model;

class Cited_Normalize extends Model
{
    protected $DBGroup = 'brapci_cited';
    protected $table = 'cited_normalize';
    protected $primaryKey = 'id_ca';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'ca_doi', 'ca_journal', 'ca_year', 'ca_text', 'ca_authors',
        'ca_pages', 'ca_vol', 'ca_nr', 'ca_url', 'ca_status', 'ca_locked',
    ];

    public static function normalizeDoi(string $doi): string
    {
        return strtolower(trim(preg_replace('~^(?:https?://(?:dx\.)?doi\.org/|doi:\s*)~i', '', trim($doi))));
    }

    public static function cleanReferenceText(string $text): string
    {
        return trim((string) preg_replace('/\s*Dispon[ií]vel\b.*$/iu', '', $text));
    }
    public function findCandidates(array $references, string $query): array
    {
        $candidates = [];
        foreach ($references as $reference) {
            $doi = self::normalizeDoi((string) ($reference['ca_doi'] ?? ''));
            if ($doi === '') {
                continue;
            }
            foreach ($this->like('ca_doi', $doi)->limit(20)->findAll() as $candidate) {
                if (self::normalizeDoi((string) $candidate['ca_doi']) === $doi) {
                    $candidate['match_reason'] = 'Mesmo DOI';
                    $candidates[(int) $candidate['id_ca']] = $candidate;
                }
            }
        }

        $terms = preg_split('/\s+/u', trim($query), -1, PREG_SPLIT_NO_EMPTY);
        $terms = array_values(array_filter($terms, static fn ($term) => mb_strlen($term) >= 3));
        if ($terms !== []) {
            $builder = $this->select('*');
            foreach ($terms as $term) {
                $builder->like('ca_text', $term);
            }
            foreach ($builder->limit(20)->findAll() as $candidate) {
                $candidate['match_reason'] = $candidates[(int) $candidate['id_ca']]['match_reason'] ?? 'Texto semelhante';
                $candidates[(int) $candidate['id_ca']] = $candidate;
            }
        }

        return array_values($candidates);
    }
}
