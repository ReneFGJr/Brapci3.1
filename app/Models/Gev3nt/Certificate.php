<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Events extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'events_inscritos';
    protected $primaryKey       = 'id_i';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_i',
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

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    function importWorks() {
    }

    function importListeners() {
        $RSP = [];
        $names = get("text");
        $names = explode(chr(13), $names);
        $nm = [];
        $HD = explode(';', $names[0]);
        if (($HD[0] == 'USUARIO') and ($HD[1] == 'EMAIL') and (count($HD) == 2)) {
            foreach ($names as $id => $name) {
                $name = troca($name, chr(13), '');
                $name = troca($name, chr(10), '');
            }
        }
        return $RSP;
    }
}
