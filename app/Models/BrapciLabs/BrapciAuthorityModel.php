<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

$session = \Config\Services::session();

class BrapciAuthorityModel extends Model
{
    protected $DBGroup          = 'brapci_labs';
    protected $table            = 'brapci_authority';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'brapci_id',
        'brapci_xml',
        'brapci_source',
    ];

    protected $useTimestamps = false;

    public function updateFromApi(int $brapciId, $type = 'BRP', $force = false): array
    {
        $data = $this
            ->where('brapci_id', $brapciId)
            ->where('brapci_source', $type)
            ->first();
        if ($data && !$force) {
            return [
                'status'  => 'skipped',
                'message' => 'Registro já existe e força não foi solicitada',
                'data'    =>  (array)(json_decode($data['brapci_xml'], true))
            ];
        }
        $apiUrl = "https://cip.brapci.inf.br/api/brapci/get/v1/" . $brapciId;

        try {
            // =========================
            // CHAMADA À API
            // =========================
            $client = \Config\Services::curlrequest([
                'timeout' => 30,
                'verify'  => false,
            ]);

            $response = $client->get($apiUrl);

            if ($response->getStatusCode() !== 200) {
                return [
                    'status'  => 'error',
                    'message' => 'Erro ao acessar a API BRAPCI',
                ];
            }

            $xmlContent = trim($response->getBody());

            $xmlArray = (array)(json_decode($xmlContent, true));

            if (empty($xmlContent)) {
                return [
                    'status'  => 'error',
                    'message' => 'Resposta vazia da API BRAPCI',
                ];
            }

            // =========================
            // VERIFICA SE JÁ EXISTE
            // =========================
            $exists = $this
                ->where('brapci_id', $brapciId)
                ->where('brapci_source', $type)
                ->first();

            if ($exists) {
                // UPDATE
                $this->update($exists['id'], [
                    'brapci_xml' => $xmlContent,
                ]);

                return [
                    'status'  => 'updated',
                    'message' => 'Registro atualizado com sucesso',
                    'data'    => $xmlArray
                ];
            }

            // =========================
            // INSERT
            // =========================
            $this->insert([
                'brapci_id'  => $brapciId,
                'brapci_xml' => $xmlContent,
                'brapci_source' => $type,
            ]);

            return [
                'status'  => 'inserted',
                'message' => 'Registro inserido com sucesso',
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'exception',
                'message' => $e->getMessage(),
            ];
        }
    }


    function view($id = 0)
    {
        $ProjectAuthorModel = new \App\Models\BrapciLabs\ProjectAuthorModel();
        $data = $ProjectAuthorModel->find($id);
        if ($data['brapci_id'] == 0) {
            echo 'Autor não possui BRAPCI ID definido.';
            return 0;
        }

        $brapci = $this->where('brapci_id', $data['brapci_id'])->first();
        $data['brapci'] = (array)json_decode($brapci['brapci_xml'] ?? null);

        echo view('BrapciLabs/widget/authors/author', ['author' => $data, 'project' => null, 'data' => $data]);

        // Exemplo de implementação para retornar um ID de projeto
        // Substitua isso pela lógica real conforme necessário
        return 0; // Retorna um ID fixo para demonstração
    }

    function list()
    {
        $ProjectAuthorModel = new \App\Models\BrapciLabs\ProjectAuthorModel();
        $projectModel = new \App\Models\BrapciLabs\ResearchProjectModel();
        //$ownerId  = $Project->getProjectsID();
        $ownerId  = 1; // temporário
        $projectId = $projectModel->getProjectsID();

        $q = get('q');

        // =============================
        // BUILDER PARA PAGINAÇÃO
        // =============================
        $ProjectAuthorModel->where('project_id', $projectId);

        if (!empty($q)) {
            $ProjectAuthorModel->like('nome', $q);
        }
        $ProjectAuthorModel->orderBy('nome', 'ASC');

        $authors = $ProjectAuthorModel->paginate(25);
        $pager   = $ProjectAuthorModel->pager;

        // =============================
        // BUILDER PARA CONTAGEM TOTAL
        // =============================
        $countModel = clone $ProjectAuthorModel;
        $countModel->where('project_id', $projectId);

        if (!empty($q)) {
            $countModel->like('nome', $q);
        }

        $total = $countModel->countAllResults();

        // =============================
        // DATA
        // =============================
        $data = [
            'current' => $projectId,
            'project' => $projectModel->find($projectId),
            'authors' => $authors,
            'pager'   => $pager,
            'q'       => $q,
            'total'   => $total
        ];

        return view('BrapciLabs/widget/authors/view', $data);
        // Exemplo de implementação para listar autoridades
        // Substitua isso pela lógica real conforme necessário
        return []; // Retorna um array vazio para demonstração
    }
}
