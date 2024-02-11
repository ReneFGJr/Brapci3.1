<?php
/*
@category API
@package Eventos
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2024 CC-BY
@access public
@example $URL/api/event/
@abstract API para consulta de eventos ativos na área
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Event extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event';
    protected $primaryKey       = 'id_ev';
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

    function index($d1, $d2, $d3)
    {
        header('Access-Control-Allow-Origin: *');
        if (get("test") == '') {
            header("Content-Type: application/json");
        }
        switch ($d1) {
            default:
                $this->list($d2);
        }
    }

    function list($status = 0)
    {
        $dt = date("Y-m-d");
        $dt = "2023-01-01";
        $cp = 'ev_name as name, ev_place as place, ';
        $cp .= 'ev_data_start as start, ev_data_end as end, id_ev as URL, ';
        $cp .= 'ev_image as logo';
        $dt = $this
            ->select($cp)
            ->where("ev_data_end >= '" . $dt . "'")
            ->orderby('ev_data_start')
            ->findAll();
        foreach ($dt as $id => $line) {
            $img = $line['logo'];
            if (substr($img, 0, 4) != 'http') {
                $logo = PATH . $img;
                $dt[$id]['logo'] = $logo;
            }
            $dt[$id]['date'] = $this->format_date($line['start'], $line['end']);
            $dt[$id]['URL'] = PATH.'/api/event/redirect/'.$line['URL'];
        }
        echo json_encode($dt);
        exit;
    }

    function format_date($ini, $end)
    {
        $day1 = substr($ini,8,2);
        $day2 = substr($end, 8, 2);

        $month1 = sonumero(substr($ini, 5, 2));
        $month2 = sonumero(substr($end, 5, 2));

        $year1 = substr($ini, 0, 4);
        $year2 = substr($end, 0, 4);

        $RSP = '';
        if (($year1 == $year2) and ($month1 == $month2)) {
            if ($day1 == $day2)
                {
                    $RSP = $day1;
                } else {
                    $RSP = $day1.'-'.$day2;
                }
                $RSP .= ' '. mes_abreviado($month1).' '.$year1;
        } else {
            if ($year1 == $year2)
                {
                    $RSP = $day1 . '/' . mes_abreviado($month1);
                    $RSP .= ' à ';
                    $RSP .= $day2 . '/' . mes_abreviado($month2);
                    $RSP .= ' '.$year1;
                } else {
                    $RSP = 'D1:' . $day1 . ' ' . $month1 . ' ' . $year1;
                    $RSP .= '<br>';
                    $RSP .= 'D2:' . $day2 . ' ' . $month2 . ' ' . $year2;
                }

        }
        return $RSP;
    }
}
