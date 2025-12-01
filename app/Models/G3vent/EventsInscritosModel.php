<?php

namespace App\Models\G3vent;

use CodeIgniter\Model;

class EventsInscritosModel extends Model
{
    protected $DBGroup          = 'g3vent';
    protected $table            = 'events_inscritos';
    protected $primaryKey       = 'id_i';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Campos que podem ser gravados
    protected $allowedFields = [
        'i_evento',
        'i_date_in',
        'i_user',
        'i_status',
        'i_date_out',
        'i_certificado',
        'i_titulo_trabalho',
        'i_autores',
        'i_carga_horaria',
        'i_cracha'
    ];

    // Regras de validação
    protected $validationRules = [
        'i_evento'  => 'required|integer',
        'i_user'    => 'required|integer',
        'i_status'  => 'required|integer',
        'i_carga_horaria' => 'permit_empty|numeric'
    ];

    protected $validationMessages = [
        'i_evento' => [
            'required' => 'O ID do evento é obrigatório.'
        ],
        'i_user' => [
            'required' => 'O ID do usuário é obrigatório.'
        ],
        'i_status' => [
            'required' => 'O status da inscrição é obrigatório.'
        ]
    ];

    // A tabela usa timestamps próprios
    protected $useTimestamps = false;
}
