<?php
/*
@category API
@package Brapci Identificação de Author pelo DOI
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/doiToFormation/?doi=10.1086/430801
@abstract API para determinar o autor e formacao
*/

namespace App\Models\Api\Endpoint;

use App\Database\Migrations\LattesDados;
use App\Models\LattesExtrator\LattesProducao;
use CodeIgniter\Model;

class DoiLattesAuthor extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'doilattesauthors';
    protected $primaryKey       = 'id';
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
        $name = get("doi");
        if ($name != '') {
            return $this->doiAuthor($name);
        }
        return "NnN";
    }

    function doiAuthor($name)
        {
            $LattesProducao = new \App\Models\LattesExtrator\LattesProducao();
            $cp = 'f_curso,lt_name,f_type,f_situacao';
            $dt = $LattesProducao
                ->select($cp)
                ->join('lattesdados', 'lt_id = lp_author')
                ->join('lattesformacao', 'f_id = lp_author')
                ->where('lp_doi',$name)
                ->findAll();

            $nome = '';
            $grad='';
            $mest='';
            $dout='';
            foreach($dt as $id=>$line)
                {
                    $nome = $line['lt_name'];
                    $n = $line['f_type'];
                    switch($n)
                        {
                            case 'G':
                                $grad = $line['f_curso'];
                                break;
                            case 'M':
                                $mest = $line['f_curso'];
                                break;
                            case 'D':
                                $dout = $line['f_curso'];
                                break;
                        }
                }
            echo $nome.';'.$grad.';'.$mest.';'.$dout;
            exit;
        }
}
