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

    /**************************** Search */
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
    function check_ids($projectId)
    {
        $this->select('id, nome, lattes_id, brapci_id, project_id')
        //->where('brapci_id','')
        ->where('project_id', $projectId);
        $authors = $this->findAll();

        // Lógica para checar IDs dos autores vinculados a projetos
        foreach ($authors as $ln)
        {
            if ($ln['brapci_id'] == '')
            {
                // Buscar BRAPCI ID via API
                $url = 'https://cip.brapci.inf.br/api/authority/search?term='.$ln['nome'];
                echo '<br>: '.$url;
                $dt = $this->searchAuthority($ln['nome']);
                $IDB = 0;
                foreach($dt['data']['item'] as $aut)
                {
                    if ($this->normalizeString($aut['Term']) == $this->normalizeString($ln['nome']))
                    {
                        $IDB = $aut['use'];
                    }
                }
                if ($IDB != 0)
                {
                    echo '<br>-- Atualizando ID do autor '.$ln['nome'].' para '.$IDB;
                    $this->update($ln['id'], ['brapci_id'=>$IDB]);
                }
            }
        }

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
