<?php

namespace App\Models\G3vent;

use CodeIgniter\Model;

class EventsModel extends Model
{
    protected $DBGroup          = 'g3vent';
    protected $table            = 'events';
    protected $primaryKey       = 'id_e';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'e_name',
        'e_event',
        'e_data_i',
        'e_data_f',
        'e_status',
        'e_texto',
        'e_keywords',
        'e_data',
        'e_ass_none_1',
        'e_ass_cargo_1',
        'e_ass_none_2',
        'e_ass_cargo_2',
        'e_ass_none_3',
        'e_ass_cargo_3',
        'e_cidade',
        'e_ass_img',
        'e_background',
        'e_templat',
        'e_location'
    ];

    protected $validationRules = [

    ];
}
