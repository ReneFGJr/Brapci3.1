<?php

namespace App\Models\Journals;

use CodeIgniter\Model;

class Publication extends Model
{
    protected $DBGroup          = 'journals';
    protected $table            = 'publications';
    protected $primaryKey       = 'id_publication';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'issn',
        'rdf_id',
        'issn_l',
        'title',
        'key_title',
        'abbreviated_key_title',
        'id_country',
        'city',
        'start_year',
        'end_year',
        'status',
        'notes',
        'id_frequency',
        'source',
        'source_url',
        'source_updated_at',
        'harvested_at',
        'raw_metadata',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
