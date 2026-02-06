<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class BrapciWorksModel extends Model
{
    protected $DBGroup    = 'brapci_cited';
    protected $table      = 'cited_works';
    protected $primaryKey = 'id';

    protected $allowedFields = [

    ];

    protected $useTimestamps = false;

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
            'data' =>$dt,
            'data_cited' => $dtcited,
            'IDbrapci' => $IDbrapci

        ]);
        // Exemplo de implementação para retornar um ID de projeto
        // Substitua isso pela lógica real conforme necessário
        return 0; // Retorna um ID fixo para demonstração
    }
}
