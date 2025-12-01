<?php

namespace App\Models\G3vent;

use CodeIgniter\Model;

class EventInscritosModel extends Model
{
    protected $DBGroup          = 'g3vent';
    protected $table            = 'event_inscritos';
    protected $primaryKey       = 'id_ein';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'ein_event',
        'ein_tipo',
        'ein_user',
        'ein_data',
        'ein_pago',
        'ein_pago_em',
        'ein_recibo'
    ];

    protected $validationRules = [
        'ein_event'  => 'required|integer',
        'ein_tipo'   => 'required|integer',
        'ein_user'   => 'required|integer',
        'ein_pago'   => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'ein_event' => [
            'required' => 'O ID do evento é obrigatório.'
        ],
        'ein_user' => [
            'required' => 'O usuário é obrigatório.',
        ]
    ];

    // Não usar created_at/updated_at pois a tabela possui campo próprio
    protected $useTimestamps = false;
}
