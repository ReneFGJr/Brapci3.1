<?php

namespace App\Models\Events;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event';
    protected $primaryKey       = 'id_ev';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'ev_name',
        'ev_place',
        'ev_ative',
        'ev_permanent',
        'ev_data_start',
        'ev_data_end',
        'ev_deadline',
        'ev_url',
        'ev_description',
        'ev_image',
        'ev_count'
    ];

    protected $returnType = 'array';

    protected $validationRules = [
        'ev_name'        => 'required|min_length[3]',
        'ev_place'       => 'required',
        'ev_data_start'  => 'required|valid_date',
        'ev_url'         => 'required|valid_url',
        'ev_description' => 'required'
    ];
}
