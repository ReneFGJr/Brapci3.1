<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class BrapciWorksModel extends Model
{
    protected $DBGroup    = 'brapci_cited';
    protected $table      = 'cited_works';
    protected $primaryKey = 'id';

    protected $allowedFields = [];

    protected $useTimestamps = false;

    function search()
    {
        $type = get("type");
        switch ($type) {
            case 'smart':
                echo $this->search_smart();
                break;
            case 'ris':
                echo $this->search_base_ris();
                break;
            default:
                echo '========================================================> ' . $type;
                break;
        }
    }

    public function updateVC()
    {
        // Caminho absoluto do script
        $PATH = $_SERVER['DOCUMENT_ROOT'];

        echo '<div class="content">';
        if (strpos($PATH, 'www/Brapci3.1')) {
            $root = dirname($_SERVER['DOCUMENT_ROOT']); // sobe um nível

            $PRG = $root . DIRECTORY_SEPARATOR . 'bots'
                        . DIRECTORY_SEPARATOR . 'AI'
                        . DIRECTORY_SEPARATOR . 'SmartRetriavel'
                        . DIRECTORY_SEPARATOR . 'updateVC.py';

            $PYTHON = $root . DIRECTORY_SEPARATOR . 'bots'
                            . DIRECTORY_SEPARATOR . 'AI'
                            . DIRECTORY_SEPARATOR . 'SmartRetriavel'
                            . DIRECTORY_SEPARATOR . 'venv'
                            . DIRECTORY_SEPARATOR . 'Scripts'
                            . DIRECTORY_SEPARATOR . 'python.exe';            
            $CMD = $PYTHON . ' ' . $PRG;
        } else {
            $PRG = troca($PATH, 'public', 'bots/AI/SmartRetriavel/updateVC.py');
            $PYTHON = troca($PATH, 'public', 'bots/AI/SmartRetriavel/venv/bin/python');
            $CMD = $PYTHON . ' ' . $PRG;
        }
        echo '<h5>' . $CMD . '</h5>';

        if (!file_exists($PRG)) {
            echo json_encode([
                'status' => 'error',
                'message' => "Script não encontrado: $PRG"
            ]);
            exit;
        }
        if (!file_exists($PATH)) {
            echo json_encode([
                'status' => 'error',
                'message' => "Python não encontrado: $PATH"
            ]);
            exit;
        }

        // Comando
        $command = "$CMD";

        // Executa
        $output = shell_exec($command);

        // Decodifica JSON retornado pelo Python
        pre($output);
    }
    public function search_smart()
    {
        $query = get('q');

        if (!$query) {
            return json_encode([
                'status' => 'error',
                'message' => 'Parâmetro q é obrigatório'
            ]);
        }

        // Caminho absoluto do script
        $PATH = $_SERVER['DOCUMENT_ROOT'];

        echo '<div class="content">';
        if (strpos($PATH, 'www/Brapci3.1')) {
            $PRG =      troca($PATH, 'public', 'bots/AI/SmartRetriavel/smartretriavel.py');
            $PYTHON =   troca($PATH, 'public', 'bots/AI/SmartRetriavel/venv/Scripts/python.exe');
            $VC =       troca($PATH, 'public', 'bots/AI/SmartRetriavel/data/');
            $CMD = $PYTHON . ' ' . $PRG;
        } else {
            $PRG = troca($PATH, 'public', 'bots/AI/SmartRetriavel/smartretriavel.py');
            $PYTHON = troca($PATH, 'public', 'bots/AI/SmartRetriavel/venv/bin/python');
            $VC = troca($PATH, 'public', 'bots/AI/SmartRetriavel/data/');
            $CMD = $PYTHON . ' ' . $PRG;
        }
        echo '<h4>' . $CMD . '</h4>';

        if (!file_exists($PRG)) {
            echo json_encode([
                'status' => 'error',
                'message' => "Script não encontrado: $PRG"
            ]);
            exit;
        }
        if (!file_exists($PATH)) {
            echo json_encode([
                'status' => 'error',
                'message' => "Python não encontrado: $PATH"
            ]);
            exit;
        }

        /******************* Load VC */
        $VC .= 'thesa_6_terms.json';
        $vocabulary = $this->loadThesaurusTerms($VC);

        // Escapa o parâmetro para segurança
        $escapedQuery = escapeshellarg($query);

        // Comando
        $command = "$CMD $escapedQuery";

        // Executa
        $output = shell_exec($command);

        // Decodifica JSON retornado pelo Python
        $data = json_decode($output, true);
        $net = '';
        $this->process_smartretriavel($data,$vocabulary,$net);
        pre($data);
    }

    /**
     * ***************************** SmartRetriavel 
     */

    function process_smartretriavel($data,$vc,$net)
    {
                $T = [];
                $t = $data['conceitos_interpretados_pelo_llm'];
                $t = array_merge($t,$data['termos_autorizados_alinhados']);

                foreach($t as $id=>$term)
                        {
                            $term = ascii($term);
                            $exist = false;
                            $termo = [];
                            foreach($vc as $idv=>$ivc)
                                {
                                    $IDc = $ivc['concept'];
                                    $termo = $ivc['term'].'@'.$ivc['lang'];
                                    if (!isset($T[$IDc]))
                                            {
                                                $T[$IDc][] = [];
                                            }
                                    $T[$IDc][] = $termo;
                                    
                                }
                        }
                pre($T, false);
                pre($data,false);
                pre($vc,false);
                pre($t);

    }

    function loadThesaurusTerms(string $filePath): array
    {
        // Verifica se o arquivo existe
        if (!file_exists($filePath)) {
            pre("Arquivo não encontrado: " . $filePath);
        }

        // Lê o conteúdo do arquivo
        $jsonContent = file_get_contents($filePath);

        if ($jsonContent === false) {
            throw new Exception("Erro ao ler o arquivo: " . $filePath);
        }

        // Decodifica o JSON
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg());
        }

        return $data;
    }

    function search_base_ris()
    {
        echo '<div class="alert alert-info content">Busca avançada</div>';
        echo '<div class="content">';

        $RisModel = new \App\Models\BrapciLabs\RisModel();
        $ElasticSearchModel = new \App\Models\Api\Endpoint\Brapci();
        $ResearchProjectModel = new \App\Models\BrapciLabs\ResearchProjectModel();

        $projectID = $ResearchProjectModel->getProjectsID();
        $W = $RisModel->where('project_id', $projectID)->findAll();
        $Works = [];
        foreach ($W as $row) {
            $BrapciID = $RisModel->brapciID($row['url']);
            $Works[$BrapciID] = $row;
        }

        $dt['search'] = get("search");
        $dt['type'] = get("type");
        $q = get("q");
        if ($q == '') {
            $q = $dt['search'];
            $_POST['q'] = $q;
        }
        $sx = '<div class="content">';
        $sx = '';

        if ($q == '') {

            $sx .= 'Busca vazia';
            $sx .= '</div>';
            return $sx;
        } else {
            $_POST['user'] = session()->get('apikey');
            $Elastic = new \App\Models\ElasticSearch\Search();
            $RSP = (array)$Elastic->searchAdvancedFull();

            $Corpus = [];
            $type = get("type");
            if ($type == 'ris') {
                foreach ($RSP['works'] as $row) {
                    $idw = $row['id'];
                    if (isset($Works[$idw])) {
                        $Corpus[] = $Works[$idw];
                        unset($Works[$idw]);
                    } else {
                        /************* Não localizado */
                    }
                }
            }
            /**************************************************** Corpus */
            $sx .= view('BrapciLabs/ris/search_result_ris', ['Works' => $Corpus, 'title' => 'Resultados encontrados', 'class' => 'text-primary']);
            $sx .= view('BrapciLabs/ris/search_result_ris', ['Works' => $Works, 'title' => 'Resultados não encontrados no <i>corpus</i> do projeto', 'class' => 'text-warning']);
        }
        return $sx;
    }

    function cloud_keys($id)
    {
        $sx = '';

        $RisModel = new \App\Models\BrapciLabs\RisModel();
        $cp = 'keywords';

        $dt = $RisModel
            ->select($cp)
            ->where('project_id', $id)
            ->findAll(5);

        /************************************ */
        // ===== Processamento =====
        $tags = [];

        foreach ($dt as $row) {
            if (empty($row['keywords'])) {
                continue;
            }

            $terms = explode(';', $row['keywords']);

            foreach ($terms as $term) {
                $term = trim($term);
                if ($term === '') {
                    continue;
                }

                $key = mb_strtolower($term, 'UTF-8');

                if (!isset($tags[$key])) {
                    $tags[$key] = [
                        'label' => $term,
                        'count' => 0
                    ];
                }

                $tags[$key]['count']++;
            }
        }

        // ===== Normalização para tamanho visual =====
        $counts = array_column($tags, 'count');
        $min = min($counts);
        $max = max($counts);

        foreach ($tags as &$tag) {
            $tag['size'] = $this->scale($tag['count'], $min, $max, 0.8, 2.5);
        }

        // Ordena por frequência
        usort($tags, fn($a, $b) => $b['count'] <=> $a['count']);

        $sx .= view('BrapciLabs/graph/tagcloud', [
            'tags' => $tags
        ]);

        /********** HighChart */
        $freq = [];

        foreach ($dt as $row) {
            if (empty($row['keywords'])) continue;

            foreach (explode(';', $row['keywords']) as $term) {
                $term = trim($term);
                if ($term === '') continue;

                $key = mb_strtolower($term, 'UTF-8');

                if (!isset($freq[$key])) {
                    $freq[$key] = [
                        'name' => $term,
                        'weight' => 0
                    ];
                }
                $freq[$key]['weight']++;
            }
        }

        $sx .= view('BrapciLabs/graph/highchart_tagcloud', [
            'data' => array_values($freq)
        ]);
        return $sx;
    }

    private function scale($value, $min, $max, $minScale, $maxScale)
    {
        if ($max === $min) {
            return ($minScale + $maxScale) / 2;
        }
        return $minScale + (($value - $min) * ($maxScale - $minScale)) / ($max - $min);
    }

    function show_cited_work($id)
    {
        $RisModel = new \App\Models\BrapciLabs\RisModel();
        $dt = $RisModel->where('id', $id)->first();
        $IDbrapci = $dt['url'];
        $IDbrapci = str_replace("https://hdl.handle.net/20.500.11959/brapci/", "", $IDbrapci);

        $CitedArticleModel = new \App\Models\BrapciLabs\CitedArticleModel();
        $dtcited = $CitedArticleModel
            ->where('ca_rdf', $IDbrapci)
            ->orderBy('ca_tipo', 'ASC')
            ->orderBy('ca_year', 'DESC')

            ->findAll();

        return view('BrapciLabs/ref/view', [
            'work_id' => $id,
            'data' => $dt,
            'data_cited' => $dtcited,
            'IDbrapci' => $IDbrapci

        ]);
        // Exemplo de implementação para retornar um ID de projeto
        // Substitua isso pela lógica real conforme necessário
        return 0; // Retorna um ID fixo para demonstração
    }
}
