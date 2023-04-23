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
        $name = get("name");
        if ($name != '') {
            return $this->getGenere($name);
        }
        return "NnN";

        $Genere = new \App\Models\Authority\Genere();
    }
    function getGenere($name)
    {
        $debug = get("debug");
        $name = ASCII($name);
        $name = troca($name, '-', ' ');
        $name = trim(nbr_author($name, 7));
        $name = mb_strtoupper($name);
        $p = null;
        $g = 'Genero indefinido';

        $nm = explode(' ', $name);
        $f = 0;
        $m = 0;
        $p = 100;
        $tm = 0;
        $tf = 0;
        if ($debug != '') {
            pre($nm, false);
        }
        $abs = 0;
        foreach ($nm as $id => $n) {
            if ($abs == 0) {
                $table = ['genre', 'genre_sp'];
                foreach ($table as $idt => $tb) {
                    if ($abs == 0) {
                        $sql = "select * from " . $tb . " where gn_first_name = '$n' ";

                        $rlt = $this->db->query($sql);
                        $rlt = (array)$rlt->getResult();
                        if (isset($rlt[0])) {
                            $dt = (array)$rlt[0];
                            $f = $f + $dt['gn_frequency_female'] * $p;
                            $m = $m + $dt['gn_frequency_male'] * $p;
                            $rel = $f / ($m + $f);
                            if (($f / ($m + $f)) > 0.9) {
                                $abs = 1;
                                break;
                            }
                            if (($m / ($m + $f)) > 0.9) {
                                $abs = 1;
                                break;
                            }

                            if ($debug != '') {
                                echo "<tt>$n - M: $m  F:$f  P:$p R:$rel</tt><br>";
                            }
                        }
                    }
                }
            }
            if ($abs == 1) {
                break;
            }
            $p = $p / 10;
        }
        $t = $f + $m;
        if($t > 0)
            {
                $tm = round($m / $t * 100);
                $tf = round($f / $t * 100);
            }

        if (($f + $m) < 100000) {
            $tf = 0;
            $tm = 0;
        }

        if ($debug != '') {
            echo "<tt>TM: $tm  TF:$tf</tt><br>";
        }

        if ($tf > 90) {
            $g = "feminino";
        } elseif ($tm > 90) {
            $g = "masculino";
        } else {
            $g = "indefinido";
        }
        echo $g;
        exit;
    }
}
