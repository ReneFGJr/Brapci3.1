<?php

namespace App\Models\DOI;

use CodeIgniter\Model;

class Cited_Journals extends Model
{
    protected $DBGroup = 'brapci_cited';
    protected $table = 'cited_journal';
    protected $primaryKey = 'id_cj';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'cj_use', 'cj_name', 'cj_name_asc', 'cj_issn', 'cj_place',
        'cj_place_text', 'cj_issn_l', 'cj_rdf', 'cj_qualis', 'cj_journal',
    ];

    /** Call within the importer transaction and its MySQL lock. */
    public function getOrCreateFromCrossref(array $work): ?int
    {
        $issns = [];
        foreach (array_merge($work['ISSN'] ?? [], array_column($work['issn-type'] ?? [], 'value')) as $issn) {
            $issn = strtoupper(str_replace('-', '', trim($issn)));
            if (preg_match('/^[0-9]{7}[0-9X]$/', $issn)) {
                $issns[] = $issn;
            }
        }
        $issns = array_values(array_unique($issns));
        if ($issns) {
            $existing = $this->groupStart()
                ->whereIn("UPPER(REPLACE(TRIM(cj_issn), '-', ''))", $issns)
                ->orWhereIn("UPPER(REPLACE(TRIM(cj_issn_l), '-', ''))", $issns)
                ->groupEnd()->orderBy('id_cj')->first();
            if ($existing) {
                return (int) $existing['id_cj'];
            }
        }
        $name = trim(html_entity_decode($work['container-title'][0] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($name === '') {
            return null;
        }
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        $ascii = strtoupper($ascii === false ? $name : $ascii);
        $this->groupStart()->where('cj_name', $name)->orWhere('cj_name_asc', $ascii)->groupEnd();
        if ($issns) {
            $this->where('cj_issn', '');
        }
        $existing = $this->orderBy('id_cj')->first();
        $issn = $issns ? substr($issns[0], 0, 4) . '-' . substr($issns[0], 4) : '';
        if ($existing) {
            if ($issn !== '' && !$this->update($existing['id_cj'], ['cj_issn' => $issn])) {
                throw new \RuntimeException('Falha ao atualizar o ISSN.');
            }
            return (int) $existing['id_cj'];
        }
        $id = $this->insert(['cj_name' => $name, 'cj_name_asc' => $ascii,
            'cj_issn' => $issn, 'cj_qualis' => '']);
        if ($id === false) {
            throw new \RuntimeException('Falha ao cadastrar a revista.');
        }
        return (int) $id;
    }
}
