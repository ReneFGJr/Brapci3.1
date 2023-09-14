<?php

namespace App\Models\Find\Books\Db;

helper('xml');

use CodeIgniter\Model;

class Z3950 extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'z3950s';
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

    // http://z3950.loc.gov:7090/voyager?version=1.1&operation=searchRetrieve&query=%22ciencia%20da%20informacao%22&maximumRecords=5&recordSchema=mods

    function str($string)
    {
        $string = xml_convert($string);
        return $string;
    }

    function index($d1, $d2)
    {
        switch ($d1) {
            case 'find':
                $xml = $this->database($d1);
                break;
            default:
                $xml = $this->server($d1);
        }
        $xmlString = $xml->saveXML();

        header("Content-type: text/xml");
        echo $xmlString;

        exit;
    }

    function server($database)
    {
        // CREATING XML OBJECT

        $xml = new \DOMDocument('1.0', "UTF-8");

        /* NameSpace */
        $root = $xml->createElementNS('http://docs.oasis-open.org/ns/search-ws/sruResponse', 'zs:explainResponse');
        $xml->appendChild($root);

        $root = $xml->createElement('zs:version', '2.0');


        // Crie um objeto DOMDocument
        $xml = new \DOMDocument('1.0', 'UTF-8');

        // Crie o elemento raiz
        $root = $xml->createElementNS('http://docs.oasis-open.org/ns/search-ws/sruResponse', 'zs:explainResponse');
        $xml->appendChild($root);

        // Crie elementos e adicione-os como filhos do elemento raiz
        $element1 = $xml->createElement('zs:version', '2.0');
        $root->appendChild($element1);

        $record = $xml->createElement('zs:record');
        $root->appendChild($record);

        // Crie subelementos e adicione-os como filhos de outros elementos
        $subelement = $xml->createElement('zs:recordSchema', 'http://explain.z3950.org/dtd/2.0/');
        $record->appendChild($subelement);

        $subelement = $xml->createElement('zs:recordXMLEscaping', 'xml');
        $record->appendChild($subelement);

        $data = $xml->createElement('zs:recordData');
        $record->appendChild($data);

        $explain = $xml->createElementNS('http://explain.z3950.org/dtd/2.0/', 'explain');
        $data->appendChild($explain);

        $serverInfo = $xml->createElement('serverInfo');
        $explain->appendChild($serverInfo);
        $att = $xml->createAttribute('protocol');
        $att->value = 'SRU';
        $serverInfo->appendChild($att);

        $host = $xml->createElement('host', 'cip.brapci.inf.br/api/');
        $serverInfo->appendChild($host);

        $host = $xml->createElement('port', '443');
        $serverInfo->appendChild($host);

        if ($database=='') { $database = 'Default'; }
        $host = $xml->createElement('database', $database);
        $serverInfo->appendChild($host);

        $total = $xml->createElement('zs:recordPosition',1);
        $record->appendChild($total);

        $xml = $this->parameter($xml,$root);

        return $xml;
    }

    function parameter($xml,$root)
    {
        if (count($_GET) > 0) {
            $fld = $_GET;

            $query = $xml->createElement('zs:echoedSearchRetrieveRequest');
            $root->appendChild($query);

            $_GET['recordPacking'] = 'xml';
            foreach ($_GET as $fld => $value) {
                $get = $xml->createElement('zs:' . $fld, $value);
                $query->appendChild($get);
            }
        }
        return $xml;
    }

    /******************************************************************* DATABASE */
    function database($d1)
    {
        // CREATING XML OBJECT

        $xml = new \DOMDocument('1.0', "UTF-8");

        /* NameSpace */
        $root = $xml->createElementNS('http://docs.oasis-open.org/ns/search-ws/sruResponse', 'zs:explainResponse');
        $xml->appendChild($root);

        $root = $xml->createElement('zs:version', '2.0');


        // Crie um objeto DOMDocument
        $xml = new \DOMDocument('1.0', 'UTF-8');

        // Crie o elemento raiz
        $root = $xml->createElementNS('http://docs.oasis-open.org/ns/search-ws/sruResponse', 'zs:explainResponse');
        $xml->appendChild($root);

        // Crie elementos e adicione-os como filhos do elemento raiz
        $element1 = $xml->createElement('zs:version', '2.0');
        $root->appendChild($element1);

        $record = $xml->createElement('zs:record');
        $root->appendChild($record);

        // Crie subelementos e adicione-os como filhos de outros elementos
        $subelement = $xml->createElement('zs:recordSchema', 'http://explain.z3950.org/dtd/2.0/');
        $record->appendChild($subelement);

        $subelement = $xml->createElement('zs:recordXMLEscaping', 'xml');
        $record->appendChild($subelement);

        $data = $xml->createElement('zs:recordData');
        $record->appendChild($data);

        $explain = $xml->createElementNS('http://explain.z3950.org/dtd/2.0/', 'explain');
        $data->appendChild($explain);

        $serverInfo = $xml->createElement('serverInfo');
        $explain->appendChild($serverInfo);

        $host = $xml->createElement('host', 'cip.brapci.inf.br/api/');
        $serverInfo->appendChild($host);

        $host = $xml->createElement('port', '443');
        $serverInfo->appendChild($host);

        $databaseInfo = $xml->createElement('databaseInfo');
        $explain->appendChild($databaseInfo);

        $title = $xml->createElement('title', 'FIND by Brapci');
        $databaseInfo->appendChild($title);

        $desc = $xml->createElement('description', 'Base interoperável de livros');
        $databaseInfo->appendChild($desc);

        $att = $xml->createAttribute('lang');
        $att->value = 'pt-BR';
        $desc->appendChild($att);

        $att = $xml->createAttribute('primart');
        $att->value = 'true';
        $desc->appendChild($att);

        $databaseInfo = $xml->createElement('databaseInfo');
        $explain->appendChild($databaseInfo);

        /*************************************************** indexInfo */
        $atd = $xml->createElement('indexInfo');
        $explain->appendChild($atd);
        $ii = [];
        $ii['cql'] = 'info:srw/cql-context-set/1/cql-v1.1';
        $ii['dc'] = 'info:srw/cql-context-set/1/dc-v1.1';
        $ii['bath'] = 'http://zing.z3950.org/cql/bath/2.0';
        $ii['local'] = 'http://zing.z3950.org/cql/local/1.1';
        foreach($ii as $idi=>$namei)
            {
                $ati = $xml->createElement('set');
                $atd->appendChild($ati);

                $att = $xml->createAttribute('identifier');
                $att->value = $namei;
                $ati->appendChild($att);

                $att = $xml->createAttribute('name');
                $att->value = $idi;
                $ati->appendChild($att);
            }

        if (count($_GET) > 0) {
            $fld = $_GET;

            $query = $xml->createElement('zs:echoedSearchRetrieveRequest');
            $root->appendChild($query);

            $_GET['recordPacking'] = 'xml';
            foreach ($_GET as $fld => $value) {
                $get = $xml->createElement('zs:' . $fld, $value);
                $query->appendChild($get);
            }
            }
        $schemaInfo = $xml->createElement('zs:schemaInfo');
        $data->appendChild($schemaInfo);
        $sch = [];
        $sc = [];
        $sc['name'] = 'marcxml';
        $sc['sort'] = 'false';
        $sc['identifier'] = 'http://www.loc.gov/MARC21/slim';
        $sc['title'] = 'MARCXML v 1.1';
        array_push($sch,$sc);

        $sc['name'] = 'dc';
        $sc['sort'] = 'false';
        $sc['identifier'] = 'http://purl.org/dc/elements/1.1/';
        $sc['title'] = 'Dublin Core v 1.1';
        array_push($sch, $sc);

        $sc['name'] = 'mods';
        $sc['sort'] = 'false';
        $sc['identifier'] = 'http://www.loc.gov/standards/mods/v3';
        $sc['title'] = 'MODS v 3.8';
        array_push($sch, $sc);

        foreach($sch as $id=>$dx)
            {
                $schemaNM = $xml->createElement('schema');
                foreach($dx as $idx=>$vlx)
                    {
                        if ($idx != 'title')
                        {
                            $schemaInfo->appendChild($schemaNM);
                            $att = $xml->createAttribute($idx);
                            $att->value = $vlx;
                            $schemaNM->appendChild($att);
                        } else {
                            $title = $xml->createElement('title',$vlx);
                            $schemaNM->appendChild($title);
                        }

                    }

            }



        return $xml;
    }

    function collections()
    {
        $dt = [];
        $dt['zs:version'] = 2.0;
        return $dt;
    }

    // XML BUILD RECURSIVE FUNCTION
    function array_to_xml($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild($key);
                    $this->array_to_xml($value, $subnode);
                } else {
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml->addChild($key, $value);
            }
        }
    }
}
