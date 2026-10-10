<?php
/*
@category API
@package Brapci PQ Information Science Collection - ISC
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/pq/bolsa_ano
@example $PATH/api/pq/bolsa_ano_tipo
@abstract Mostra todas as fontes indexadas na Brapci, Parametros:<ul><li>Fontes: <a href="$PATH/api/source/">$PATH/api/source/</a></li><li>Coleções: <a href="$PATH/api/source/collections">$PATH/api/source/collections</a></li></ul>
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Pq extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bolsistas';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $Bolsas = new \App\Models\PQ\Bolsas();
        $type = get("type");

        switch($d2)
            {
                case 'actives':
                    $RSP = $Bolsas->activesPQ();
                    break;
                case 'bolsa_ano':
                    $RSP = $Bolsas->bolsa_ano();
                    break;
                case 'bolsa_ano_tipo':
                    $RSP =  $Bolsas->bolsa_ano_tipo();
                    break;
                case 'vigente':
                    return $Bolsas->collections($d2, $d3);
                    break;
                default:
                    $bolsasData = $Bolsas->activesPQ();
                    $bolsasAll = $Bolsas->allPQ();
                    $genders = [];
                    $bolsasData = $this->identifyMissingGenders($bolsasData, $genders);
                    $bolsasAll = $this->identifyMissingGenders($bolsasAll, $genders);
                    $RSP = [];
                    $RSP['status'] = '200';
                    $RSP['message'] = 'Resume';
                    $RSP['actives'] = count($bolsasData);
                    $RSP['institutions'] = count($Bolsas->institutionsData($bolsasData));
                    $RSP['data'] = $bolsasData;
                    $RSP['actives_by_year'] = $Bolsas->year_distribuition($bolsasAll);
                    $RSP['applications'] = $Bolsas->applications($bolsasAll);
                    break;
            }

            echo json_encode($RSP);
            exit;
    }

    public function identifyMissingGenders(array $scholars, array &$genders): array
    {
        $person = null;
        $bolsistas = null;
        $saved = [];
        foreach ($scholars as &$scholar) {
            if (trim((string) ($scholar['bs_genero'] ?? '')) !== '') {
                continue;
            }
            $rdf = (int) ($scholar['bs_rdf_id'] ?? 0);
            $name = (string) ($scholar['bs_nome'] ?? '');
            $key = $rdf . ':' . $name;
            if (!isset($genders[$key])) {
                $person ??= new \App\Models\Authority\Person();
                $genders[$key] = $person->identifyGender($rdf, $name);
            }
            $id = (int) ($scholar['id_bs'] ?? 0);
            if (array_key_exists('bs_genero', $scholar) && $scholar['bs_genero'] === null
                && $id > 0 && !isset($saved[$id])) {
                $bolsistas ??= new \App\Models\PQ\Bolsistas();
                if (!$bolsistas->saveMissingGender($id, $genders[$key])) {
                    throw new \RuntimeException('Não foi possível salvar o gênero do bolsista.');
                }
                $saved[$id] = true;
            }
            $scholar['bs_genero'] = $genders[$key];
        }
        unset($scholar);
        return $scholars;
    }

    function collections($d1,$d2)
        {
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");
            $Collections = new \App\Models\Base\Collections();

            echo $Collections->list('json');
            exit;


        }

    function all()
        {
            header('Access-Control-Allow-Origin: *');
            header("Content-type: application/json; charset=utf-8");

            $Sources = new \App\Models\Base\Sources();

            echo $Sources->list('json');
            exit;
        }

}
