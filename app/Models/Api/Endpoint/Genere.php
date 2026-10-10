<?php
/*
@category API
@package Brapci Identificação de Generos (Masc. / Fem.)
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/gender/?name=RENE FAUSTINO GABRIEL JUNIOR
@abstract API para determinar o genero da pessoa pelo Nome
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Genere extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'genre';
    protected $primaryKey       = 'id_gn';
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
        if ($d2 === 'check_genere') {
            $name = get('name');
            $rdf = get('rdf');
            if (!is_string($name) || !is_scalar($rdf) || ($rdf !== '' && !ctype_digit((string) $rdf))
                || (trim($name) === '' && (int) $rdf <= 0)) {
                http_response_code(422);
                echo json_encode(['status' => '422', 'message' => 'Informe name ou rdf válidos.']);
                return '';
            }
            $gender = (new \App\Models\Authority\Person())->identifyGender((int) $rdf, $name);
            echo json_encode(['status' => '200', 'gender' => $gender]);
            return '';
        }
        $name = get("name");
        if ($name != '') {
            echo $this->getGenere($name);
            exit;
        } else {
            $name = 'NaN';
            echo $this->getGenere($name);
            exit;
        }
    }

    function getGenere($name)
    {
        $Genere = new \App\Models\AI\Person\Genere();
        return $Genere->getGenere($name);
    }

}
