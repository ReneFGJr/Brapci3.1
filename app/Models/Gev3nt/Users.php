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
        'id_n',
        'n_nome',
        'b_cracha',
        'n_email',
        'n_cpf',
        'n_orcid',
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

    function importRegister()
    {
        $Certificate = new \App\Models\Gev3nt\Certificate();
        $names = get("text");
        $names = explode(chr(13), $names);
        $ID = get("id");
        $nn = [];
        foreach ($names as $line) {
            $its = explode(';',$line);
            $email = $its[0];
            $titulo = $its[1];
            if (isset($its[1])) {
                $titulo = $its[1];
            } else {
                $titulo = '';
            }

            if (isset($its[2])) {
                $autores = $its[2];
            } else {
                $autores = '';
            }

            if (isset($its[3]))
                {
                    $ch = $its[3];
                } else {
                    $ch = 0;
                }

            $dt = $this->where('n_email', $email)->first();
            if ($dt) {
                $idn = $dt['id_n'];

                if ($Certificate->Register($idn, $ID, $titulo, $autores, $ch) == 1) {
                    array_push($nn, $email . ' registrado');
                } else {
                    array_push($nn, $email . ' já registrado');
                }
            } else {
                array_push($nn, $email . ' não localizado');
            }
        }
        $RSP['data'] = $nn;
        return $RSP;
    }

    function importUserReferee()
        {
            $Certificate = new \App\Models\Gev3nt\Certificate();
            $names = get("text");
            $names = explode(chr(13), $names);
            $ID = get("id");
            $nn = [];
            foreach($names as $id=>$name)
                {
                    $dt = $this->where('n_email',$name)->first();
                    if ($dt)
                        {
                            $idn = $dt['id_n'];

                            if ($Certificate->Register($idn,$ID)==1)
                                {
                                    array_push($nn, $name . ' registrado');
                                } else {
                                    array_push($nn, $name . ' já registrado');
                                }


                        } else {
                            array_push($nn, $name.' não localizado');
                        }
                }
            $RSP['data'] = $nn;
            return $RSP;
        }

    function importUserList()
    {
        $names = get("text");
        $names = explode(chr(13), $names);
        $nm = [];
        $HD = explode(';', $names[0]);
        if (($HD[0] == 'USUARIO') and ($HD[1] == 'EMAIL') and (count($HD) == 2)) {
            foreach ($names as $id => $name) {
                $name = troca($name, chr(13), '');
                $name = troca($name, chr(10), '');

                $HD = explode(';', $name);
                if (count($HD) == 2) {
                    $email = $HD[1];
                    $nome = $HD[0];
                    $nome = nbr_author($nome,7);

                    if (($nome != 'USUARIO') AND ($email != 'EMAIL')) {
                        $dt = $this->where('n_email', $email)->first();
                        if (!$dt) {
                            $dd = [];
                            $dd['n_nome'] = $nome;
                            $dd['n_email'] = $email;
                            $this->set($dd)->insert();
                            array_push($nm, ['nome' => $nome, 'email' => $email, 'status' => 'inserted']);
                        } else {
                            array_push($nm, ['nome' => $nome, 'email' => $email, 'status' => 'already']);
                        }
                    }
                } else {
                    array_push($nm, ['nome' => $name, 'email' => '', 'status' => 'invalid']);
                }
                $RSP['status'] = '200';
                $RSP['users'] = $nm;
                $RSP['message'] = 'Success';
            }
        } else {
            $RSP['status'] = '500';
            $RSP['message'] = 'Dados em formato inválido use [USUARIO;EMAIL]';
            $RSP['Rul1'] = ($HD[0] == 'USUARIO');
            $RSP['Rul2'] = ($HD[1] == 'EMAIL');
            $RSP['Rul3'] = count($HD);
        }
        return $RSP;
    }

    function getUserApi($apikey)
    {
        $dt =
            $this
            ->Join('corporateBody', 'id_cb = n_afiliacao', 'LEFT')
            ->where('apikey', $apikey)
            ->first();
        return $dt;
    }

    function register(
        $name,
        $institution,
        $cpf,
        $orcid,
        $email,
        $cracha,
        $biografia = '',
        $apikey = ''
    ) {
        $dt = $this
            ->where('n_email', $email)
            ->first();

        if ($dt == []) {
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
            if ($apikey != '') {
                $this->set($dt)->where('apikey', $apikey)->update();
            }
        }
        return $dt;
    }
}
