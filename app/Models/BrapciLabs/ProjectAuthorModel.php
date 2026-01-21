<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class ProjectAuthorModel extends Model
{
    protected $DBGroup    = 'brapci_labs';
    protected $table      = 'project_authors';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    //protected $useTimestamps = true;
    //protected $createdField  = 'created_at';
    //protected $updatedField  = null;

    protected $allowedFields = [
        'nome',
        'lattes_id',
        'brapci_id',
        'project_id',
        'created_at'
    ];

    protected $validationRules = [];

    protected $validationMessages = [];

    private function getProjectsID()
    {
        $projectId = session('project_id');
        if ($projectId) {
            return $projectId;
        } else {
            return 0;
        }
    }

    /**************************** Search */
    public function searchAuthorityLattes(string $term): array
    {
        $url = 'http://200.130.0.48:5000/persons/nome/' . strtolower(urlencode($term));
        $url = str_replace('+', '%20', $url);
        $client = \Config\Services::curlrequest([
            'verify'  => false,   // 🔴 DESATIVA SSL (apenas desenvolvimento)
            'timeout' => 10
        ]);

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                pre('Erro na API CIP',false);
                pre($response,false);
                flush();
                exit;
                return [];
            }

            return json_decode($response->getBody(), true) ?? [];
        } catch (\Throwable $e) {
            log_message('error', 'Erro na API CIP: ' . $e->getMessage());
            return [];
        }
    }


    public function searchAuthority(string $term): array
    {
        $url = 'https://cip.brapci.inf.br/api/authority/search?term=' . urlencode($term);

        $client = \Config\Services::curlrequest([
            'verify'  => false,   // 🔴 DESATIVA SSL (apenas desenvolvimento)
            'timeout' => 10
        ]);

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                return [];
            }

            return json_decode($response->getBody(), true) ?? [];
        } catch (\Throwable $e) {
            log_message('error', 'Erro na API CIP: ' . $e->getMessage());
            return [];
        }
    }



    /******************************** */
    public function check_ids($projectId,$data)
    {
        set_time_limit(600); // 10 minutos

        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', false);
        @ini_set('implicit_flush', true);
        ob_implicit_flush(true);

        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        ob_implicit_flush(true);


        // Views iniciais
        echo view('BrapciLabs/layout/header');
        echo view('BrapciLabs/layout/sidebar');
        echo '<div class="content">';
        echo view('BrapciLabs/widget/projects/header', $data);
        echo view('BrapciLabs/widget/process/index');
        echo '</div>';

        echo str_repeat(' ', 1024);
        flush();

        // Buscar autores
        $this->select('id, nome, lattes_id, brapci_id, project_id')
            ->where('project_id', $projectId);

        $authors = $this->findAll();
        $total   = count($authors);
        $step    = 0;

        foreach ($authors as $ln) {
            $step++;

            echo "
        <script>
            document.getElementById('process').innerHTML =
            'Processando {$step} de {$total}: {$ln['nome']}';
        </script>
        ";
            echo str_repeat(' ', 1024);
            flush();

            /***************** Lattes ID */
            if ($ln['lattes_id'] == '') {

                $dt = $this->searchAuthorityLattes($ln['nome']);
                echo '<div class="col-12 card mb-4 shadow-sm border-0"><div class="card-body">';
                $IDB = '';

                foreach ($dt as $key => $dti) {
                    $IDB = $key;
                    echo "<strong>Lattes ID encontrado:</strong> $IDB <br>";
                }
                echo '</div></div>';

                if ($IDB) {
                    $this->update($ln['id'], [
                        'lattes_id' => $IDB
                    ]);
                }
                sleep(1); // simula tempo (opcional)
            } else {
                usleep(10000); // simula tempo (opcional)
            }

            /***************** Brapci ID */
            if ($ln['brapci_id'] == '') {

                $dt = $this->searchAuthority($ln['nome']);

                if (!isset($dt['data']['item'])) {
                    continue;
                }

                $IDB = 0;
                foreach ($dt['data']['item'] as $aut) {
                    if (
                        $this->normalizeString($aut['Term']) ===
                        $this->normalizeString($ln['nome'])
                    ) {
                        $IDB = $aut['use'];
                        break;
                    }
                }

                if ($IDB) {
                    $this->update($ln['id'], [
                        'brapci_id' => $IDB
                    ]);
                }
                sleep(1); // simula tempo (opcional)
            } else {
                usleep(10000); // simula tempo (opcional)
            }
        }

        // Finalização
        echo "
    <script>
        document.getElementById('process').innerHTML =
        '<strong>Processo concluído com sucesso.</strong>';
    </script>
    ";
        echo str_repeat(' ', 1024);
        flush();

        exit;
    }


    private function normalizeString(string $str): string
    {
        // Converte para UTF-8
        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');

        // Remove acentos
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);

        // Remove caracteres especiais (mantém letras, números e espaço)
        $str = preg_replace('/[^a-zA-Z0-9\s]/', '', $str);

        // Minúsculas
        $str = strtolower($str);

        // Remove espaços extras
        $str = preg_replace('/\s+/', ' ', trim($str));

        return $str;
    }


    /* =========================================
       MÉTODOS ÚTEIS
    ========================================= */

    public function countByProject(int $projectId): int
    {
        return $this->where('project_id', $projectId)
            ->countAllResults();
    }

    /**
     * Retorna autores vinculados a um projeto
     */
    public function getByProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->orderBy('nome', 'ASC')
            ->findAll();
    }

    /**
     * Retorna autor pelo BRAPCI ID
     */
    public function getByBrapciId(int $brapciId): ?array
    {
        return $this->where('brapci_id', $brapciId)->first();
    }

    /**
     * Verifica se autor já está vinculado ao projeto
     */
    public function existsInProject(string $nome, int $projectId): bool
    {
        $rsp = $this->where([
            'project_id' => $projectId,
            'nome'       => $nome
        ])->countAllResults() > 0;
        return $rsp;
    }
}
