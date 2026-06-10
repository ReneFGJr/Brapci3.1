<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IdentifyModel;
use App\Models\OaiListSetsModel;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

define("URL", getenv("app.baseURL"));
define("PATH", getenv("app.baseURL") . '/');
define("MODULE", '');
define("PREFIX", '');
define("COLLECTION", 'oai-server');

class OaiServer extends BaseController
{
    /**
     * Cabeçalho padrão para as páginas OAI-Server
     */
    private function cab()
    {
        $menu = [];
        $data['menu'] = $menu;
        $data['page_title'] = 'OAI-Server | Brapci';
        $data['bg'] = 'bg-oai-server';

        $sx = '';
        $sx .= view('Brapci/Headers/header', $data);
        return $sx;
    }

    /**
     * Método principal para OAI-Server
     */
    public function index($path = '', $act = '', $subact = '', $id = '', $id2 = '', $id3 = '', $id4 = '', $id5 = '')
    {

        $verb = get("verb");
        if ($verb != '') {
            header('Content-Type: application/xml; charset=UTF-8');
        }
        if ($path == '')
            {
                $this->oaiError('badArgument', 'Parâmetro obrigatório ausente: path');
            } else {
                // Checa se o path corresponde a um repositório configurado
                if (!$this->validateRepository($path)) {
                    $this->oaiError('badArgument', 'Repositório não encontrado: ' . htmlspecialchars($path));
                }
            }

        switch ($verb) {
            case 'Identify':
                $response = $this->identify($path);
                break;

            case 'ListMetadataFormats':
                $response = $this->listMetadataFormats($path);
                break;

            case 'ListSets':
                $response = $this->listSets($path);
                break;

            case 'ListIdentifiers':
                $response = $this->listIdentifiers($path);
                break;

            case 'ListRecords':
                $response = $this->listRecords($path);
                break;

            case 'GetRecord':
                $response = $this->getRecord($path);
                break;

            default:
                $sx = $this->cab();
                $sx .= view('Oai/server', ['path' => $path]);
                return $sx;
        }

        echo $response;
        exit;
    }

    /**
     * Validar se o repositório existe na tabela identify
     */
    private function validateRepository($path)
    {
        $model = new IdentifyModel();
        $repository = $model->getByPath($path);
        return !is_null($repository) && $repository !== false;
    }

    /**
     * Implementar resposta Identify
     */
    private function identify($path = '')
    {
        $model = new IdentifyModel();

        // Se path foi fornecido, filtrar por path; caso contrário, retornar o primeiro registro
        if (!empty($path)) {
            $identify = $model->getByPath($path);
        } else {
            $identify = $model->getFirst();
        }

        if (!$identify) {
            $this->oaiError('noMetadataFormats', 'Nenhum registro de Identify configurado para o repositório solicitado');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">';
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '<request verb="Identify">' . htmlspecialchars($identify['base_url']) . '?verb=Identify</request>';
        $xml .= '<Identify>';
        $xml .= '<repositoryName>' . htmlspecialchars($identify['repository_name']) . '</repositoryName>';
        $xml .= '<baseURL>' . htmlspecialchars($identify['base_url']) . '</baseURL>';
        $xml .= '<protocolVersion>' . htmlspecialchars($identify['protocol_version']) . '</protocolVersion>';

        if ($identify['admin_email']) {
            $xml .= '<adminEmail>' . htmlspecialchars($identify['admin_email']) . '</adminEmail>';
        }

        $xml .= '<earliestDatestamp>' . date('Y-m-d\TH:i:s\Z', strtotime($identify['earliest_datestamp'])) . '</earliestDatestamp>';
        $xml .= '<deletedRecord>' . htmlspecialchars($identify['deleted_record']) . '</deletedRecord>';
        $xml .= '<granularity>' . htmlspecialchars($identify['granularity']) . '</granularity>';

        if ($identify['compression']) {
            $compressions = explode(',', $identify['compression']);
            foreach ($compressions as $compression) {
                $xml .= '<compression>' . htmlspecialchars(trim($compression)) . '</compression>';
            }
        }

        if ($identify['description']) {
            $xml .= '<description>' . htmlspecialchars($identify['description']) . '</description>';
        }

        $xml .= '</Identify>';
        $xml .= '</OAI-PMH>';

        return $this->outputXml($xml);
    }

    /**
     * Implementar resposta ListMetadataFormats
     */
    private function listMetadataFormats($path = '')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">';
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '<request verb="ListMetadataFormats">' . getenv('app.baseURL') . '/oai-server?verb=ListMetadataFormats</request>';
        $xml .= '<ListMetadataFormats>';

        // Formato Dublin Core (oai_dc)
        $xml .= '<metadataFormat>';
        $xml .= '<metadataPrefix>oai_dc</metadataPrefix>';
        $xml .= '<schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>';
        $xml .= '<metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>';
        $xml .= '</metadataFormat>';

        // Formato Dublin Core Qualificado (qualified)
        $xml .= '<metadataFormat>';
        $xml .= '<metadataPrefix>qualified</metadataPrefix>';
        $xml .= '<schema>http://dublincore.org/schemas/xmls/qdc/2008/02/11/qualifieddc.xsd</schema>';
        $xml .= '<metadataNamespace>http://purl.org/dc/terms/</metadataNamespace>';
        $xml .= '</metadataFormat>';

        // Formato Dublin Core Simples (dc)
        $xml .= '<metadataFormat>';
        $xml .= '<metadataPrefix>dc</metadataPrefix>';
        $xml .= '<schema>http://dublincore.org/schemas/xmls/simpledc20021212.xsd</schema>';
        $xml .= '<metadataNamespace>http://purl.org/dc/elements/1.1/</metadataNamespace>';
        $xml .= '</metadataFormat>';

        $xml .= '</ListMetadataFormats>';
        $xml .= '</OAI-PMH>';

        echo $this->outputXml($xml);
        exit;
    }

    /**
     * Implementar resposta ListSets
     */
    private function listSets($path = '')
    {
        $identifyModel = new IdentifyModel();

        // Verificar repositório
        $repo = $identifyModel->getByPath($path);
        if (!$repo) {
            $this->oaiError('badArgument', 'Repositório não encontrado: ' . htmlspecialchars($path));
        }


        // Query com filtro por repository
        $OAIArticle = new \App\Models\OaiArtigosModel();
        $sets = $OAIArticle
            ->select('s_description, s_cod')
            ->join('oai_listsets ls', 'oai_artigos.section = ls.id_s', 'left')
            ->groupBy('s_description, s_cod')
            ->findAll();

        // Construir XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<OAI-PMH ';
        $xml .= 'xmlns="http://www.openarchives.org/OAI/2.0/" ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        $xml .= 'xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd"';
        $xml .= '>'."\n";
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '<request verb="ListSets">' . getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=ListSets</request>' . "\n";
        $xml .= '<ListSets>' . "\n";

        if (!empty($sets)) {
            foreach ($sets as $set) {

                $xml .= '<set>' . "\n";
                $xml .= '<setSpec>' . htmlspecialchars($set['s_cod']) . '</setSpec>' . "\n";
                $xml .= '<setName>' . htmlspecialchars($set['s_cod']) . '</setName>' . "\n";
                if (!empty($set['s_description'])) {
                    $xml .= '<dc:description>' . htmlspecialchars($set['s_description']) . '</dc:description>' . "\n";
                } else {
                    $xml .= '<dc:description></dc:description>' . "\n";
                }
                $xml .= '</set>' . "\n";
            }
        }

        $xml .= '</ListSets>' . "\n";
        $xml .= '</OAI-PMH>' . "\n";

        return $this->outputXml($xml);
        exit;
    }

    /**
     * Implementar resposta ListIdentifiers
     */
    private function listIdentifiers($path = '')
    {
        $identifyModel = new IdentifyModel();

        // Verificar repositório
        $repo = $identifyModel->getByPath($path);
        if (!$repo) {
            header('Content-Type: application/xml; charset=UTF-8');
            $this->oaiError('badArgument', 'Repositório não encontrado: ' . htmlspecialchars($path));
        }

        // Obter parâmetros opcionais
        $setSpec = get('set');
        $from = get('from');
        $until = get('until');
        $metadataPrefix = get('metadataPrefix') ?? 'oai_dc';

        // Validar metadataPrefix
        $validPrefixes = ['oai_dc', 'qualified', 'dc'];
        if (!in_array($metadataPrefix, $validPrefixes)) {
            $this->oaiError('cannotDisassembleFormat', 'Metadata format não suportado: ' . htmlspecialchars($metadataPrefix));
        }

        // Query para obter identificadores
        $OAIArticle = new \App\Models\OaiArtigosModel();

        // Query com filtro por repository
        $OAIArticle = new \App\Models\OaiArtigosModel();
        $identifiers = $OAIArticle
            ->select('s_description, s_cod, created_at, id')
            ->join('oai_listsets ls', 'oai_artigos.section = ls.id_s', 'left')
            ->groupBy('s_description, s_cod, created_at, id')
            ->findAll();

        // Construir XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<OAI-PMH ';
        $xml .= 'xmlns="http://www.openarchives.org/OAI/2.0/" ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        $xml .= 'xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd"';
        $xml .= '>' . "\n";

        $xml .= 'http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">';
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '<request verb="ListIdentifiers"';

        if ($setSpec) {
            $xml .= ' set="' . htmlspecialchars($setSpec) . '"';
        }
        if ($metadataPrefix) {
            $xml .= ' metadataPrefix="' . htmlspecialchars($metadataPrefix) . '"';
        }

        $xml .= '>' . getenv('app.baseURL') . '/oai-server/' . htmlspecialchars($path) . '?verb=ListIdentifiers</request>';
        $xml .= '<ListIdentifiers>';

        if (!empty($identifiers)) {
            foreach ($identifiers as $item) {
                $identifier = 'oai:' . $path . ':article:' . $item['id'];
                $datestamp = date('Y-m-d\TH:i:s\Z', strtotime($item['created_at']));

                $xml .= '<header>';
                $xml .= '<identifier>' . htmlspecialchars($identifier) . '</identifier>';
                $xml .= '<datestamp>' . htmlspecialchars($datestamp) . '</datestamp>';

                if (!empty($item['s_cod'])) {
                    $xml .= '<setSpec>' . htmlspecialchars($item['s_cod']) . '</setSpec>';
                }

                $xml .= '</header>';
            }
        }

        $xml .= '</ListIdentifiers>';
        $xml .= '</OAI-PMH>';

        echo $this->outputXml($xml);
        exit;
    }

    /**
     * Implementar resposta ListRecords
     */
    private function listRecords($path = '')
    {
        // TODO: Implementar lógica ListRecords
        return $this->oaiResponse('ListRecords');
    }

    /**
     * Implementar resposta GetRecord
     */
    private function getRecord($path = '')
    {
        $identifyModel = new IdentifyModel();

        // Verificar repositório
        header('Content-Type: application/xml; charset=UTF-8');
        $repo = $identifyModel->getByPath($path);
        if (!$repo) {
            $this->oaiError('badArgument', 'Repositório não encontrado: ' . htmlspecialchars($path));
        }

        // Obter parâmetros obrigatórios
        $identifier = get('identifier');
        $metadataPrefix = get('metadataPrefix');

        // Validar parâmetros obrigatórios
        if (!$identifier) {
            $this->oaiError('badArgument', 'Parâmetro obrigatório ausente: identifier');
        }

        if (!$metadataPrefix) {
            $this->oaiError('badArgument', 'Parâmetro obrigatório ausente: metadataPrefix');
        }

        // Validar metadataPrefix
        $validPrefixes = ['oai_dc', 'qualified', 'dc'];
        if (!in_array($metadataPrefix, $validPrefixes)) {
            $this->oaiError('cannotDisassembleFormat', 'Metadata format não suportado: ' . htmlspecialchars($metadataPrefix));
        }

        // Extrair ID do identifier (formato: oai:repository:article:id)
        $identifierParts = explode(':', $identifier);
        if (count($identifierParts) < 4 || $identifierParts[0] !== 'oai' || $identifierParts[2] !== 'article') {
            $this->oaiError('badArgument', 'Formato de identificador inválido: ' . htmlspecialchars($identifier));
        }
        $recordId = $identifierParts[3];

        // Query para obter o registro
        $db = \Config\Database::connect('oai_server');
        $OAIArticle = new \App\Models\OaiArtigosModel();
        $record = $OAIArticle
            ->join('oai_listsets ls', 'oai_artigos.section = ls.id_s', 'left')
            ->where('id',$recordId)
            ->first();


        if (!$record) {
            $this->oaiError('idDoesNotExist', 'Registro não encontrado para o identificador: ' . htmlspecialchars($identifier));
        }

        // Construir XML
        // Construir XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<OAI-PMH ';
        $xml .= 'xmlns="http://www.openarchives.org/OAI/2.0/" ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        $xml .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        $xml .= 'xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd"';
        $xml .= '>' . "\n";

        $xml .= 'http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">' . "\n";
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>' . "\n";

        //$xml = '<request verb="GetRecord" metadataPrefix="oai_dc">https://seer.ufrgs.br/index.php/EmQuestao/oai</request>' . "\n";
        $xml .= '<request verb="GetRecord" identifier="' . htmlspecialchars($identifier) . '" metadataPrefix="' . htmlspecialchars($metadataPrefix) . '">'
                . '</request>'. "\n";

        $xml .= '<GetRecord>'."\n";

        // Header
        $xml .= '<record>' . "\n";
        $xml .= '<header>' . "\n";
        $xml .= '<identifier>' . htmlspecialchars($identifier) . '</identifier>' . "\n";
        $xml .= '<dc:identifier>' . htmlspecialchars($identifier) . '</dc:identifier>' . "\n";
        $xml .= '<datestamp>' . date('Y-m-d\TH:i:s\Z', strtotime($record['created_at'])) . '</datestamp>' . "\n";

        if (!empty($record['s_cod'])) {
            $xml .= '<setSpec>' . htmlspecialchars($record['s_cod']) . '</setSpec>' . "\n";
        }

        $xml .= '</header>' . "\n";

        // Metadata
        $xml .= '<metadata>' . "\n";
        $xml .= '<oai_dc:dc' . "\n";
        $xml .= 'xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"' . "\n";
        $xml .= 'xmlns:dc="http://purl.org/dc/elements/1.1/"' . "\n";
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= 'xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/';
        $xml .= 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd">'."\n";

        // Título
        if (!empty($record['title'])) {
            $xml .= '<dc:title xml:lang="pt-BR">' . htmlspecialchars($record['title']) . '</dc:title>' . "\n";
        }

        // Autores
        if (!empty($record['authors'])) {
            $authors = explode(';', $record['authors']);
            foreach ($authors as $author) {
                $author = trim($author);
                if (!empty($author)) {
                    $xml .= '<dc:creator>' . htmlspecialchars($author) . '</dc:creator>';
                }
            }
        }

        // Resumo
        if (!empty($record['abstract'])) {
            $xml .= '<dc:description>' . htmlspecialchars($record['abstract']) . '</dc:description>';
        }

        $xml .= '<dc:source>' . htmlspecialchars('ISKO Brasil, 2023, v. 7') . '</dc:source>' . "\n";
        $xml .= '<dc:publisher xml:lang="pt-BR">IKSO Brasil</dc:publisher>'."\n";
        $xml .= '<dc:date>2023-06-01</dc:date>'."\n";
        $xml .= '<dc:type>info:eu-repo/semantics/article</dc:type>'."\n";
        $xml .= '<dc:relation>https://cip.brapci.inf.br/oai-server/download/'.htmlspecialchars(sonumero($identifier)).'</dc:relation>'."\n";
        $xml .= '<dc:rights xml:lang="en">https://creativecommons.org/licenses/by/4.0/</dc:rights>'."\n";

        // Palavras-chave
        if (!empty($record['keywords'])) {
            $keywords = troca($record['keywords'],'.',';');
            $keywords = explode(';', $keywords);
            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (!empty($keyword)) {
                    $xml .= '<dc:subject>' . htmlspecialchars($keyword) . '</dc:subject>';
                }
            }
        }

        // Idioma padrão
        $xml .= '<dc:language>pt-BR</dc:language>';

        // Identificador permanente
        $xml .= '<dc:identifier>' . htmlspecialchars($identifier) . '</dc:identifier>';

        // Data de publicação
        $xml .= '<dc:issued>' . date('Y-m-d', strtotime($record['created_at'])) . '</dc:issued>';

        $xml .= '</oai_dc:dc>' . "\n";
        $xml .= '</metadata>' . "\n";

        $xml .= '</record>' . "\n";
        $xml .= '</GetRecord>' . "\n";
        $xml .= '</OAI-PMH>' . "\n";

        return $this->outputXml($xml);
        exit;
    }

    /**
     * Gerar resposta OAI-PMH genérica
     */
    private function oaiResponse($verb)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">';
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '<request verb="' . htmlspecialchars($verb) . '">' . getenv('app.baseURL') . '/oai-server' . '</request>';
        $xml .= '<' . htmlspecialchars($verb) . '>';
        $xml .= '<!-- ' . htmlspecialchars($verb) . ' content -->';
        $xml .= '</' . htmlspecialchars($verb) . '>';
        $xml .= '</OAI-PMH>';

        return $xml;
    }

    /**
     * Retornar resposta XML com headers corretos
     */
    private function outputXml($xml)
    {
        header('Content-Type: application/xml; charset=UTF-8');
        return $xml;
    }

    /**
     * Gerar resposta de erro OAI-PMH
     */
    private function oaiError($code, $message)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">';
        $xml .= '<responseDate>' . date('Y-m-d\TH:i:s\Z') . '</responseDate>';
        $xml .= '<request verb="' . (get('verb') ? htmlspecialchars(get('verb')) : 'Unknown') . '">' . getenv('app.baseURL') . '/oai-server</request>';
        $xml .= '<error code="' . htmlspecialchars($code) . '">' . htmlspecialchars($message) . '</error>';
        $xml .= '</OAI-PMH>';

        echo $this->outputXml($xml);
        exit;
    }
}
