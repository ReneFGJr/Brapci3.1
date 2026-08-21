<?php

namespace App\Models\Journals;

use CodeIgniter\Model;

class PublicationRanking extends Model
{
    protected $DBGroup          = 'journals';
    protected $table            = 'publication_rankings';
    protected $primaryKey       = 'id_publication_ranking';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_publication',
        'id_ranking_source',
        'period_start',
        'period_end',
        'stratum',
        'numeric_value',
        'evaluation_area',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
