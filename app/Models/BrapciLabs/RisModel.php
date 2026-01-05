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
}
