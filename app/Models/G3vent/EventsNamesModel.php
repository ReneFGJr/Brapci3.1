<?php

namespace App\Models\G3vent;

use CodeIgniter\Model;

class EventsNamesModel extends Model
{
    protected $DBGroup          = 'g3vent';
    protected $table            = 'events_names';
    protected $primaryKey       = 'id_n';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'n_nome',
        'n_cracha',
        'n_email',
        'n_password',
        'n_created',
        'n_cpf',
        'n_orcid',
        'n_afiliacao',
        'n_biografia',
        'apikey'
    ];

    // Eventos automáticos
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // Validações
    protected $validationRules = [];

    protected $validationMessages = [];

    /**
     * Evento automático para hash da senha
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['n_password']) && !empty($data['data']['n_password'])) {
            $data['data']['n_password'] = password_hash($data['data']['n_password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['n_password']); // evita sobrescrever para NULL
        }

        return $data;
    }


}
