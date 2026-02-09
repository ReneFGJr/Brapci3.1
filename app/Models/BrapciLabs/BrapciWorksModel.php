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
            'data' =>$dt,
            'data_cited' => $dtcited,
            'IDbrapci' => $IDbrapci

        ]);
        // Exemplo de implementação para retornar um ID de projeto
        // Substitua isso pela lógica real conforme necessário
        return 0; // Retorna um ID fixo para demonstração
    }
}
