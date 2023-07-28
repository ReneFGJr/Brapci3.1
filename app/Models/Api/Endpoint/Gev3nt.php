<?php
/*
@category API
@package Eventos - Gev3nt
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/gev3nt/events
@abstract API Controle de eventos e certificados
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Gev3nt extends Model
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
        header('Access-Control-Allow-Origin: *');
        $RSP = [];
        $RSP['status'] = '200';
        switch($d1)
            {
                case 'cancel_register':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $Gev3ntInscritos = new \App\Models\Gev3nt\Inscritos();                    
                    $de = $Gev3ntInscritos
                        ->join('event_sections','ei_sub_event = id_es')
                        ->find($d2);
                    $ev = 0;
                    if (isset($de['es_event']))
                        {
                            $ev = $de['es_event'];
                        }   
                    if ($de['ei_cpf'] == $d2)
                        {
                            $Gev3ntInscritos->where('id_ei',$d1)->delete();
                        } else {
                            $RSP['message'] = 'CPF não confere';
                            $RSP['d2'] = $d2;
                            $RSP['d1'] = $d1;
                        }

                    $RSP['events'] = $Gev3nt->events($ev);
                    $RSP['action'] = 'delete '.$d2;
                    break;

                case 'event_register':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $Gev3ntInscritos = new \App\Models\Gev3nt\Inscritos();
                    $sec = get("id");
                    $cpf = get("cpf");
                    $sta = get("sta");
                    $ev = get("ev");
                    $Gev3ntInscritos->register($cpf,$sec,$sta);
                    $RSP['events'] = $Gev3nt->events($ev);
                    $RSP['action'] = 'update';
                break;

                case 'events':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $type = 1; //Eventos ativos
                    $RSP['events'] = $Gev3nt->events($type);
                break;

                case 'get':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $RSP['event'] = $Gev3nt->getId($d2);
                break;
                
                case 'sections':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $RSP['sections'] = $Gev3nt->subevents($d2,$d3);
                break;

                default:
                $RSP = $this->services($RSP);
                break;
            }  
        echo json_encode($RSP);
        exit;      
    }
    function services($RSP)
    {
        $srv = [];
        $srv = ['events','get','sections'];
        $RSP['services'] = $srv;
        return $RSP;
    }    
}
   