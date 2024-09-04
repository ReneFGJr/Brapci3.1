<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;

class Users extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'events_names';
    protected $primaryKey       = 'id_n';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_n','n_nome','b_cracha','n_email',
        'n_cpf','n_orcid',
        'n_cracha',
        'n_afiliacao',
        'n_biografia',
        'apikey'
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

    function createApikey($id, $length = 32)
    {
        // Caracteres que serão usados na geração da chave
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $apiKey = '';

        // Gerar chave de API
        for ($i = 0; $i < $length; $i++) {
            $apiKey .= $characters[random_int(0, $charactersLength - 1)];
        }

        $dd = [];
        $dd['apikey'] = $apiKey;
        $this->set($dd)->where('id_n', $id)->update();

        return $apiKey;
    }

    function getUserApi($apikey) {
        $dt =
            $this
            ->Join('corporateBody', 'id_cb = n_afiliacao','LEFT')
            ->where('apikey', $apikey)
            ->first();
        return $dt;
    }

    function register(
            $name,$institution,
            $cpf,$orcid,
            $email,$cracha,
            $biografia='',$apikey='')
        {
            $dt = $this
            ->where('n_email', $email)
            ->first();

            if ($dt==[])
                {
                    $dt['n_nome'] = $name;
                    $dt['n_cracha'] = $cracha;
                    $dt['n_email'] = $email;
                    $dt['n_orcid'] = $orcid;
                    $dt['n_cpf'] = $cpf;
                    $dt['n_afiliacao'] = $institution;
                    $dt['n_biografia'] = $biografia;
                    $this->set($dt)->insert($dt);
                } else {
                    $dt['n_nome'] = $name;
                    $dt['n_cracha'] = $cracha;
                    $dt['n_email'] = $email;
                    $dt['n_orcid'] = $orcid;
                    $dt['n_cpf'] = $cpf;
                    $dt['n_afiliacao'] = $institution;
                    $dt['n_biografia'] = $biografia;
                    if ($apikey != '')
                        {
                            $this->set($dt)->where('apikey', $apikey)->update();
                        }

                }
            return $dt;
        }


}
