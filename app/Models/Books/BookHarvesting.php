<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class BookHarvesting extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'book_harvesting';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement  = false;
    protected $insertID         = 0;
    protected $returnType        = 'array';
    protected $useSoftDeletes    = false;
    protected $protectFields     = true;
    protected $allowedFields     = [
        'identifier',
        'datestamp',
        'setSpec',
        'title',
        'isbn',
        'creators',
        'subjects',
        'description',
        'publishers',
        'contributors',
        'dc_date',
        'dc_type',
        'format',
        'identifiers',
        'source',
        'language',
        'relation',
        'coverage',
        'rights',
        'raw_xml',
        'DOI',
        'ChaptherBook',
        'status'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function findByIdentifier(string $identifier): array
    {
        return $this->where('identifier', $identifier)->first() ?? [];
    }

    public function listByStatus($status, ?int $limit = null): array
    {
        $builder = $this->where('status', $status)->orderBy('datestamp', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function summaryByStatus(): array
    {
        return $this->select('status, COUNT(*) AS qtd')
            ->groupBy('status')
            ->orderBy('status', 'ASC')
            ->findAll();
    }

    public function updateByIdentifier(string $identifier, array $data): bool
    {
        return (bool) $this->where('identifier', $identifier)->set($data)->update();
    }

    public function setStatus(string $identifier, int $status): bool
    {
        return $this->updateByIdentifier($identifier, ['status' => $status]);
    }

    public function updateCoverageAndDoi(string $identifier, ?string $coverage = null, ?string $doi = null): bool
    {
        $data = [];

        if ($coverage !== null) {
            $data['coverage'] = $coverage;
        }

        if ($doi !== null) {
            $data['DOI'] = $doi;
        }

        if ($data === []) {
            return true;
        }

        return $this->updateByIdentifier($identifier, $data);
    }

    public function saveChapterBook(string $identifier, string $chapterBookJson): bool
    {
        return $this->updateByIdentifier($identifier, [
            'ChaptherBook' => $chapterBookJson,
        ]);
    }
}
