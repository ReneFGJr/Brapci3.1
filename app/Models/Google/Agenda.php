<?php

namespace App\Models\Google;

use CodeIgniter\Model;

class Agenda extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'agendas';
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

    // https://calendar.google.com/calendar/u/0/r/eventedit?ctz=America/Sao_Paulo&recur=null&dates=20230731T140000Z/20230731T160000Z&details=%EF%BB%BFEntre+de+um+PC,+Mac,+iPad,+iPhone+ou+dispositivo+Android:+%0A%09Clique+neste+URL+para+entrar.+https://us02web.zoom.us/w/86167548999?tk%3DzHqKsUsDWL-16B6zdEcOLn_Aj70M2e6l58fu_EC5xAI.DQMAAAAUD_x0RxZwMlhEN1NsNFNwdTJPalRJczVtRGpBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA%26pwd%3DRkxjakQvVzNRYjZNNDVaMDRHL3QzUT09%26uuid%3DWN_Btczead-SCyXA_aTI-bBhQ+%0A%09Senha+de+acesso:+906866%0A%09Descri%C3%A7%C3%A3o:+El+Archival+Resource+Keys+(ARKs)+es+un+sistema+de+PIDs+abierto+que+proporciona+referencias+confiables+para+objetos+de+informaci%C3%B3n+son+una+alternativa+de+bajo+costo+y+flexible+para+asignar+identificadores+persistentes.+En+este+seminario+presentamos+dos+experiencias+de+implementaci%C3%B3n+de+ARK+en+la+regi%C3%B3n+latinoamericana,+ARK-CAICYT,+una+plataforma+de+servicios+para+la+asignaci%C3%B3n+de+identificadores+persistentes+y+dARK,+un+servicio+de+PIDs+descentralizado+y+abierto+concebido+desde+el+principio+como+un+bien+p%C3%BAblico+para+el+ecosistema+de+Ciencia+Abierta.%0A%0ASe+espera+que+todas+las+personas+que+participen+en+eventos+promovidos+por+SciELO+sigan+nuestro+C%C3%B3digo+de+Conducta:+https://25.scielo.org/es/codigo-de-conducta/%0A_____________________________________________________________%0A%0ACoordinaci%C3%B3n:%0A-+Andrea+Mora+Campos,+LA+Referencia.%0A%0APonentes:%0A-+Lautaro+Matas,+LA+Referencia;%0A-+Diego+Ferreyra,+CAICYT-CONICET;%0A-+Carlos+Authier,+CAICYT-CONICET;%0A-+Washington+Segundo,+Ibict.%0AOu+entre+por+telefone%EF%BC%9A%0D%0A++++++++Estados+Unidos:+%2B1+301+715+8592++or+%2B1+305+224+1968++or+%2B1+309+205+3325++or+%2B1+312+626+6799++or+%2B1+346+248+7799++or+%2B1+360+209+5623++or+%2B1+386+347+5053++or+%2B1+507+473+4847++or+%2B1+564+217+2000++or+%2B1+646+558+8656++or+%2B1+646+931+3860++or+%2B1+669+444+9171++or+%2B1+669+900+9128++or+%2B1+689+278+1000++or+%2B1+719+359+4580++or+%2B1+253+205+0468++or+%2B1+253+215+8782+%0A%09ID+do+webinar:+861+6754+8999%0A%09Senha+de+acesso:+906866%0A%09N%C3%BAmeros+internacionais+dispon%C3%ADveis:+https://us02web.zoom.us/u/kjoCBWX5y+%0A&location=https://us02web.zoom.us/w/86167548999?tk%3DzHqKsUsDWL-16B6zdEcOLn_Aj70M2e6l58fu_EC5xAI.DQMAAAAUD_x0RxZwMlhEN1NsNFNwdTJPalRJczVtRGpBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA%26pwd%3DRkxjakQvVzNRYjZNNDVaMDRHL3QzUT09%26uuid%3DWN_Btczead-SCyXA_aTI-bBhQ&text=Seminario+LA+Referencia+-+SciELO+25+A%C3%B1os:+Identificadores+Persistentes+Alternativos:+Dos+soluciones+basadas+en+ARK+desarrolladas+en+Latinoam%C3%A9rica&pli=1

    function link()
        {
            $url = 'https://us02web.zoom.us/w/86167548999';
            $data = date("YMDTHis").'Z|' . date("YMDTHis") . 'Z';
            $url = 'https://calendar.google.com/calendar/u/0/r/eventedit?';
            $url .= 'ctz=America/Sao_Paulo';
            $url .= '&recur=null';
            $url .= '&dates=20230731T140000Z/20230731T160000Z';
            $url .= '&details=%EF%BB%BFEntre+de+um+PC,+Mac,+iPad,+iPhone+ou+dispositivo+Android:+%0A%09Clique+neste+URL+para+entrar.+https://us02web.zoom.us/w/86167548999?tk%3DzHqKsUsDWL-16B6zdEcOLn_Aj70M2e6l58fu_EC5xAI.DQMAAAAUD_x0RxZwMlhEN1NsNFNwdTJPalRJczVtRGpBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA%26pwd%3DRkxjakQvVzNRYjZNNDVaMDRHL3QzUT09%26uuid%3DWN_Btczead-SCyXA_aTI-bBhQ+%0A%09Senha+de+acesso:+906866%0A%09Descri%C3%A7%C3%A3o:+El+Archival+Resource+Keys+(ARKs)+es+un+sistema+de+PIDs+abierto+que+proporciona+referencias+confiables+para+objetos+de+informaci%C3%B3n+son+una+alternativa+de+bajo+costo+y+flexible+para+asignar+identificadores+persistentes.+En+este+seminario+presentamos+dos+experiencias+de+implementaci%C3%B3n+de+ARK+en+la+regi%C3%B3n+latinoamericana,+ARK-CAICYT,+una+plataforma+de+servicios+para+la+asignaci%C3%B3n+de+identificadores+persistentes+y+dARK,+un+servicio+de+PIDs+descentralizado+y+abierto+concebido+desde+el+principio+como+un+bien+p%C3%BAblico+para+el+ecosistema+de+Ciencia+Abierta.%0A%0ASe+espera+que+todas+las+personas+que+participen+en+eventos+promovidos+por+SciELO+sigan+nuestro+C%C3%B3digo+de+Conducta:+https://25.scielo.org/es/codigo-de-conducta/%0A_____________________________________________________________%0A%0ACoordinaci%C3%B3n:%0A-+Andrea+Mora+Campos,+LA+Referencia.%0A%0APonentes:%0A-+Lautaro+Matas,+LA+Referencia;%0A-+Diego+Ferreyra,+CAICYT-CONICET;%0A-+Carlos+Authier,+CAICYT-CONICET;%0A-+Washington+Segundo,+Ibict.%0AOu+entre+por+telefone%EF%BC%9A%0D%0A++++++++Estados+Unidos:+%2B1+301+715+8592++or+%2B1+305+224+1968++or+%2B1+309+205+3325++or+%2B1+312+626+6799++or+%2B1+346+248+7799++or+%2B1+360+209+5623++or+%2B1+386+347+5053++or+%2B1+507+473+4847++or+%2B1+564+217+2000++or+%2B1+646+558+8656++or+%2B1+646+931+3860++or+%2B1+669+444+9171++or+%2B1+669+900+9128++or+%2B1+689+278+1000++or+%2B1+719+359+4580++or+%2B1+253+205+0468++or+%2B1+253+215+8782+%0A%09ID+do+webinar:+861+6754+8999%0A%09Senha+de+acesso:+906866%0A%09N%C3%BAmeros+internacionais+dispon%C3%ADveis:+https://us02web.zoom.us/u/kjoCBWX5y+%0A';
            $url .= '&location='.$data.'?tk%3DzHqKsUsDWL-16B6zdEcOLn_Aj70M2e6l58fu_EC5xAI.DQMAAAAUD_x0RxZwMlhEN1NsNFNwdTJPalRJczVtRGpBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA%26pwd%3DRkxjakQvVzNRYjZNNDVaMDRHL3QzUT09%26uuid%3DWN_Btczead-SCyXA_aTI-bBhQ';
            $url .= '&text=Seminario+LA+Referencia+-+SciELO+25+A%C3%B1os:+Identificadores+Persistentes+Alternativos:+Dos+soluciones+basadas+en+ARK+desarrolladas+en+Latinoam%C3%A9rica';
            $url .= '&pli=1';
            echo $url;
        }

}
