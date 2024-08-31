<?php
/*
@category API
@package Gev3nt - Eventos
@name
@author Rene Faustino Gabriel Junior <renefgj@gmail.com>
@copyright 2022 CC-BY
@access public/private/apikey
@example $PATH/api/g3vent/events
@abstract API Controle de eventos e certificados
*/

namespace App\Models\Api\Endpoint;

use CodeIgniter\Model;

class Gev3nt extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event';
    protected $primaryKey       = 'id_e';
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
                case 'schedule':
                    $d2 = round(sonumero('0'.$d2));
                    $EventsSchedule = new \App\Models\Gev3nt\EventsSchedule();
                    $RSP = $EventsSchedule->agenda($d2);
                    echo json_encode($RSP);
                    exit;
                    break;
                case 'checkEmail':
                    $dd = [];
                    $email = get("email");
                    $Socials = new \App\Models\Gev3nt\Users();
                    $da = $Socials->where('n_email', $email)->first();
                    if ($da != []) {
                        $dt['nome'] = $da['n_nome'];
                        $dt['email'] = $da['n_email'];
                        $dt['cracha'] = $da['n_cracha'];
                        $dt['afiliacao'] = $da['n_afiliacao'];
                        $dt['status'] = '200';
                        if ($da['apikey'] == '')
                            {
                                $dt['apikey'] = $Socials->createApikey($da['id_n']);
                            } else {
                                $dt['apikey'] = $da['apikey'];
                            }
                    } else {
                        $dt['status'] = '400';
                        $dt['message'] = 'e-mail not found';
                    }
                    echo json_encode($dt);
                    exit;
                    break;
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
                    if ($de['ei_cpf'] == $d3)
                        {
                            $Gev3ntInscritos->where('id_ei',$d2)->delete();
                        } else {
                            $RSP['message'] = 'CPF não confere';
                            $RSP['d3'] = $d3;
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

                case 'open':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $type = 1; //Eventos ativos
                    $RSP['events'] = $Gev3nt->events_open();
                    $RSP['uset'] = $_POST;
                    break;

                case 'subscribeType':
                    $Socials = new \App\Models\Gev3nt\Users();
                    $Events = new \App\Models\Gev3nt\Events();
                    $EventsInscritos = new \App\Models\Gev3nt\EventsInscritos();
                    $user = $Socials->getUserApi(get("apikey"));
                    $event = $Events->le(get("event"));
                    $userID = $user['id_n'];
                    $RSP['user'] = $user;
                    $RSP['event'] = $event;
                    $RSP['subscribe'] = $EventsInscritos->Subscribe(get("event"), $userID);
                    $RSP['stauts'] = 1;
                    break;

                case 'get':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $RSP['event'] = $Gev3nt->getId($d2);
                break;

                case 'resume':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $RSP['event'] = $Gev3nt->resume($d2);
                    break;

                case 'subscribed':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $RSP['event'] = $Gev3nt->subscribed($d2);
                    break;

                case 'sections':
                    $Gev3nt = new \App\Models\Gev3nt\Index();
                    $RSP['sections'] = $Gev3nt->subevents($d2,$d3);
                break;

                default:
                $RSP = $this->services($RSP);
                $RSP['actual'] = $d1;
                break;
            }
        echo json_encode($RSP);
        exit;
    }

    function services($RSP)
    {
        $srv = [];
        $srv = ['events','get','sections','resume', 'subscribed'];
        $RSP['services'] = $srv;
        return $RSP;
    }
}
