<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class BrapciLabAbs extends BaseController
{
    public function index($act = '', $id = '', $id2 = '')
    {
        switch ($act) {
            case 'viewVC':
                return $this->viewVC($id);
            default:
                return $this->response->setStatusCode(404)->setBody('Ação não encontrada.');
        }
    }

    private function viewVC(string $id)
    {
        if ($id === '' || !ctype_digit($id)) {
            return $this->response->setStatusCode(400)->setBody('ID do vocabulário inválido.');
        }

        $basePath = ROOTPATH . 'bots/AI/SmartRetriavel/data/';
        $netPath = $basePath . 'thesa_' . $id . '.net';

        $termsPath = $basePath . 'thesa_' . $id . '_terms.json';
        if (!is_file($termsPath)) {
            $fallback = $basePath . 'thesa_' . $id . '_terns.json';
            if (is_file($fallback)) {
                $termsPath = $fallback;
            }
        }

        if (!is_file($netPath)) {
            return $this->response->setStatusCode(404)->setBody('Arquivo .net não encontrado: ' . $netPath);
        }

        if (!is_file($termsPath)) {
            return $this->response->setStatusCode(404)->setBody('Arquivo de termos não encontrado: ' . $termsPath);
        }

        $graph = $this->parseNet($netPath);
        $termsByConcept = $this->loadTermsByConcept($termsPath);

        $hierarchy = $this->buildHierarchy($graph['nodes'], $graph['children'], $graph['roots']);

        $data = [
            'vocabularyId' => $id,
            'netPath' => $netPath,
            'termsPath' => $termsPath,
            'nodes' => $graph['nodes'],
            'children' => $graph['children'],
            'parentsCount' => $graph['parentsCount'],
            'roots' => $graph['roots'],
            'hierarchy' => $hierarchy,
            'termsByConcept' => $termsByConcept,
        ];

        return view('Abs/view_vc', $data);
    }

    private function parseNet(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $nodes = [];
        $children = [];
        $parentsCount = [];

        $inVertices = false;
        $inArcs = false;

        foreach ($lines as $lineRaw) {
            $line = trim($lineRaw);
            if ($line === '') {
                continue;
            }

            if (stripos($line, '*Vertices') === 0) {
                $inVertices = true;
                $inArcs = false;
                continue;
            }

            if (stripos($line, '*Arcs') === 0 || stripos($line, '*Edges') === 0) {
                $inVertices = false;
                $inArcs = true;
                continue;
            }

            if ($inVertices) {
                if (preg_match('/^(\d+)\s+"([^"]+)"$/', $line, $m)) {
                    $nodeId = (int)$m[1];
                    $label = $m[2];
                    $nodes[$nodeId] = $label;
                    if (!isset($children[$nodeId])) {
                        $children[$nodeId] = [];
                    }
                    if (!isset($parentsCount[$nodeId])) {
                        $parentsCount[$nodeId] = 0;
                    }
                }
                continue;
            }

            if ($inArcs) {
                if (preg_match('/^(\d+)\s+(\d+)(?:\s+.+)?$/', $line, $m)) {
                    $from = (int)$m[1];
                    $to = (int)$m[2];

                    if (!isset($children[$from])) {
                        $children[$from] = [];
                    }
                    $children[$from][] = $to;

                    if (!isset($parentsCount[$to])) {
                        $parentsCount[$to] = 0;
                    }
                    $parentsCount[$to]++;

                    if (!isset($parentsCount[$from])) {
                        $parentsCount[$from] = 0;
                    }

                    if (!isset($nodes[$from])) {
                        $nodes[$from] = 'Termo ' . $from;
                    }
                    if (!isset($nodes[$to])) {
                        $nodes[$to] = 'Termo ' . $to;
                    }
                }
            }
        }

        ksort($nodes);
        foreach ($children as &$list) {
            $list = array_values(array_unique($list));
            sort($list);
        }
        unset($list);

        $roots = [];
        foreach ($nodes as $nodeId => $_label) {
            if (($parentsCount[$nodeId] ?? 0) === 0) {
                $roots[] = $nodeId;
            }
        }
        sort($roots);

        $rsp = [
            'nodes' => $nodes,
            'children' => $children,
            'parentsCount' => $parentsCount,
            'roots' => $roots,
        ];
        pre($rsp);
        return $rsp;
    }

    private function loadTermsByConcept(string $path): array
    {
        $json = file_get_contents($path);
        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            return [];
        }

        $termsByConcept = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }

            $concept = isset($row['concept']) ? (int)$row['concept'] : 0;
            $term = isset($row['term']) ? trim((string)$row['term']) : '';
            $lang = isset($row['lang']) ? trim((string)$row['lang']) : '';

            if ($concept <= 0 || $term === '') {
                continue;
            }

            if (!isset($termsByConcept[$concept])) {
                $termsByConcept[$concept] = [
                    'preferred' => '',
                    'terms' => [],
                    'byLang' => [],
                ];
            }

            if (!isset($termsByConcept[$concept]['byLang'][$lang])) {
                $termsByConcept[$concept]['byLang'][$lang] = [];
            }

            if (!in_array($term, $termsByConcept[$concept]['byLang'][$lang], true)) {
                $termsByConcept[$concept]['byLang'][$lang][] = $term;
            }

            if (!in_array($term, $termsByConcept[$concept]['terms'], true)) {
                $termsByConcept[$concept]['terms'][] = $term;
            }
        }

        foreach ($termsByConcept as $concept => $data) {
            $preferred = '';
            foreach (['por', 'eng', 'spa'] as $lang) {
                if (!empty($data['byLang'][$lang][0])) {
                    $preferred = $data['byLang'][$lang][0];
                    break;
                }
            }

            if ($preferred === '' && !empty($data['terms'][0])) {
                $preferred = $data['terms'][0];
            }

            $termsByConcept[$concept]['preferred'] = $preferred;
            sort($termsByConcept[$concept]['terms']);
            ksort($termsByConcept[$concept]['byLang']);
        }

        ksort($termsByConcept);
        return $termsByConcept;
    }

    private function buildHierarchy(array $nodes, array $children, array $roots): array
    {
        $hierarchy = [];
        $renderedAsRoot = [];

        foreach ($roots as $root) {
            $root = (int)$root;
            $renderedAsRoot[$root] = true;
            $hierarchy[] = $this->buildNodeTree($root, $children, []);
        }

        foreach ($nodes as $nodeId => $_label) {
            $nodeId = (int)$nodeId;
            if (!isset($renderedAsRoot[$nodeId])) {
                $hierarchy[] = $this->buildNodeTree($nodeId, $children, []);
            }
        }

        return $hierarchy;
    }

    private function buildNodeTree(int $nodeId, array $children, array $path): array
    {
        if (in_array($nodeId, $path, true)) {
            return [
                'id' => $nodeId,
                'cycle' => true,
                'children' => [],
            ];
        }

        $path[] = $nodeId;
        $branch = [
            'id' => $nodeId,
            'cycle' => false,
            'children' => [],
        ];

        foreach ($children[$nodeId] ?? [] as $childId) {
            $branch['children'][] = $this->buildNodeTree((int)$childId, $children, $path);
        }

        return $branch;
    }
}
