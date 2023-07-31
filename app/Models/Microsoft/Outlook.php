<?php

namespace App\Models\Microsoft;

use CodeIgniter\Model;

class Outlook extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'outlooks';
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

    /*
        BEGIN:VCALENDAR
        PRODID:-//zoom.us//iCalendar Event//EN
        VERSION:2.0
        CALSCALE:GREGORIAN
        METHOD:PUBLISH
        CLASS:PUBLIC
        BEGIN:VTIMEZONE
        TZID:America/Sao_Paulo
        LAST-MODIFIED:20230407T050750Z
        TZURL:https://www.tzurl.org/zoneinfo-outlook/America/Sao_Paulo
        X-LIC-LOCATION:America/Sao_Paulo
        BEGIN:STANDARD
        TZNAME:-03
        TZOFFSETFROM:-0300
        TZOFFSETTO:-0300
        DTSTART:19700101T000000
        END:STANDARD
        END:VTIMEZONE
        BEGIN:VEVENT
        DTSTAMP:20230731T145141Z
        DTSTART;TZID=America/Sao_Paulo:20230731T110000
        DTEND;TZID=America/Sao_Paulo:20230731T130000
        SUMMARY:Seminario LA Referencia - SciELO 25 Años: Identificadores Persist
        entes Alternativos: Dos soluciones basadas en ARK desarrolladas en Latin
        oamérica
        UID:20230731T145141Z-86167548999@fe80:0:0:0:aa:aff:fe1d:ac8ens5
        TZID:America/Sao_Paulo
        DESCRIPTION:﻿Entre de um PC\, Mac\, iPad\, iPhone ou dispositivo Android:
        \n	Clique neste URL para entrar. https://us02web.zoom.us/w/86167548999?
        tk=zHqKsUsDWL-16B6zdEcOLn_Aj70M2e6l58fu_EC5xAI.DQMAAAAUD_x0RxZwMlhEN1NsN
        FNwdTJPalRJczVtRGpBAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA&pwd=RkxjakQvVzNRYjZNND
        VaMDRHL3QzUT09&uuid=WN_Btczead-SCyXA_aTI-bBhQ \n	Senha de acesso: 906866
        \n	Descrição: El Archival Resource Keys (ARKs) es un sistema de PIDs abi
        erto que proporciona referencias confiables para objetos de información
        son una alternativa de bajo costo y flexible para asignar identificadore
        s persistentes. En este seminario presentamos dos experiencias de implem
        entación de ARK en la región latinoamericana\, ARK-CAICYT\, una platafor
        ma de servicios para la asignación de identificadores persistentes y dAR
        K\, un servicio de PIDs descentralizado y abierto concebido desde el pri
        ncipio como un bien público para el ecosistema de Ciencia Abierta.\n\nSe
        espera que todas las personas que participen en eventos promovidos por
        SciELO sigan nuestro Código de Conducta: https://25.scielo.org/es/codigo
        -de-conducta/\n_________________________________________________________
        ____\n\nCoordinación:\n- Andrea Mora Campos\, LA Referencia.\n\nPonentes
        :\n- Lautaro Matas\, LA Referencia\;\n- Diego Ferreyra\, CAICYT-CONICET\
        ;\n- Carlos Authier\, CAICYT-CONICET\;\n- Washington Segundo\, Ibict.\nO
        u entre por telefone：\n        Estados Unidos: +1 301 715 8592  or +1 30
        5 224 1968  or +1 309 205 3325  or +1 312 626 6799  or +1 346 248 7799
        or +1 360 209 5623  or +1 386 347 5053  or +1 507 473 4847  or +1 564 21
        7 2000  or +1 646 558 8656  or +1 646 931 3860  or +1 669 444 9171  or +
        1 669 900 9128  or +1 689 278 1000  or +1 719 359 4580  or +1 253 205 04
        68  or +1 253 215 8782 \n	ID do webinar: 861 6754 8999\n	Senha de acesso
        : 906866\n	Números internacionais disponíveis: https://us02web.zoom.us/u
        /kjoCBWX5y \n
        LOCATION:https://us02web.zoom.us/w/86167548999?tk=zHqKsUsDWL-16B6zdEcOLn_
        Aj70M2e6l58fu_EC5xAI.DQMAAAAUD_x0RxZwMlhEN1NsNFNwdTJPalRJczVtRGpBAAAAAAA
        AAAAAAAAAAAAAAAAAAAAAAA&pwd=RkxjakQvVzNRYjZNNDVaMDRHL3QzUT09&uuid=WN_Btc
        zead-SCyXA_aTI-bBhQ
        BEGIN:VALARM
        TRIGGER:-PT10M
        ACTION:DISPLAY
        DESCRIPTION:Reminder
        END:VALARM
        END:VEVENT
        END:VCALENDAR
    */
}
