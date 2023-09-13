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
            default:
                $RSP = $this->collections();
        }
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

                    $host = $xml->createElement('host','cip.brapci.inf.br/api/');
                    $serverInfo->appendChild($host);

                    $host = $xml->createElement('port', '443');
                    $serverInfo->appendChild($host);

                $databaseInfo = $xml->createElement('databaseInfo');
                $explain->appendChild($databaseInfo);

                    $title = $xml->createElement('title', 'FIND by Brapci');
                    $databaseInfo->appendChild($title);


        // Saída do XML
        $xmlString = $xml->saveXML();

        header("Content-type: text/xml");
        echo $xmlString;

        exit;
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
