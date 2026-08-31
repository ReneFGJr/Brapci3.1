<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class NET extends Model
{
    /**
     * Calcula indicadores de uma rede de coautoria criada a partir do campo Author.
     *
     * @param array<int, array<string, mixed>|string> $dt
     * @return array<string, mixed>
     */
    public function net_indicatores(array $dt): array
    {
        $graph = $this->buildGraph($dt, 'Author');
        $nodes = array_keys($graph);
        $nodeCount = count($nodes);
        $edgeCount = (int) (array_sum(array_map('count', $graph)) / 2);

        if ($nodeCount === 0) {
            return [
                'network' => ['nodes' => 0, 'edges' => 0, 'density' => 0.0, 'modularity' => 0.0],
                'authors' => [],
            ];
        }

        $betweenness = $this->betweenness($graph);
        $closeness = $this->closeness($graph);
        $eigenvector = $this->eigenvector($graph);
        [$communities, $modularity] = $this->communities($graph);
        $authors = [];

        foreach ($nodes as $node) {
            $authors[] = [
                'author' => $node,
                'degree' => count($graph[$node]),
                'weighted_degree' => array_sum($graph[$node]),
                'betweenness' => round($betweenness[$node], 8),
                'closeness' => round($closeness[$node], 8),
                'eigenvector' => round($eigenvector[$node], 8),
                'community' => $communities[$node] + 1,
            ];
        }

        usort($authors, static fn (array $a, array $b): int => $b['betweenness'] <=> $a['betweenness']);

        return [
            'network' => [
                'nodes' => $nodeCount,
                'edges' => $edgeCount,
                'density' => $nodeCount > 1 ? round((2 * $edgeCount) / ($nodeCount * ($nodeCount - 1)), 8) : 0.0,
                'modularity' => round($modularity, 8),
            ],
            'authors' => $authors,
        ];
    }

    /**
     * Gera uma rede de coautoria no formato Pajek.
     *
     * Cada item pode ser uma string de autores separados por ponto e vírgula
     * ou um registro que possua o campo AUTHORS.
     *
     * @param array<int, string|array<string, mixed>> $dt
     */
    public function net_authors(array $dt): string
    {
        $authors = [];
        $edges = [];

        foreach ($dt as $line) {
            $authorLine = is_array($line) ? ($line['AUTHORS'] ?? '') : $line;
            if (! is_scalar($authorLine)) {
                continue;
            }

            $documentAuthors = [];
            foreach (explode(';', (string) $authorLine) as $authorName) {
                $name = trim(preg_replace('/\s+/u', ' ', $authorName) ?? '');
                if ($name === '') {
                    continue;
                }

                $key = mb_strtolower($name, 'UTF-8');
                $key = nbr_author($key,2);
                $documentAuthors[$key] = $key;
            }

            foreach ($documentAuthors as $key => $name) {
                if (! isset($authors[$key])) {
                    $authors[$key] = ['name' => $name, 'frequency' => 0];
                }
                $authors[$key]['frequency']++;
            }

            $keys = array_keys($documentAuthors);
            for ($left = 0, $total = count($keys); $left < $total; $left++) {
                for ($right = $left + 1; $right < $total; $right++) {
                    $pair = [$keys[$left], $keys[$right]];
                    sort($pair, SORT_STRING);
                    $edgeKey = $pair[0] . "\0" . $pair[1];

                    if (! isset($edges[$edgeKey])) {
                        $edges[$edgeKey] = ['from' => $pair[0], 'to' => $pair[1], 'weight' => 0];
                    }
                    $edges[$edgeKey]['weight']++;
                }
            }
        }

        if ($authors === []) {
            return "*Vertices 0\r\n*Edges\r\n";
        }

        uasort($authors, static fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));

        $maximumFrequency = max(array_column($authors, 'frequency'));
        $vertexIds = [];
        $lines = ['*Vertices ' . count($authors)];
        $vertex = 1;

        foreach ($authors as $key => $author) {
            $vertexIds[$key] = $vertex;
            $label = str_replace(['\\', '"'], ['\\\\', '\\"'], $author['name']);
            $size = number_format(1 + (9 * $author['frequency'] / $maximumFrequency), 2, '.', '');
            $lines[] = $vertex . ' "' . $label . '" ellipse'
                . ' x_fact ' . $size . ' y_fact ' . $size;
            $vertex++;
        }

        $lines[] = '*Edges';
        usort($edges, static function (array $a, array $b) use ($vertexIds): int {
            return [$vertexIds[$a['from']], $vertexIds[$a['to']]]
                <=> [$vertexIds[$b['from']], $vertexIds[$b['to']]];
        });

        foreach ($edges as $edge) {
            $lines[] = $vertexIds[$edge['from']] . ' '
                . $vertexIds[$edge['to']] . ' '
                . $edge['weight'];
        }

        return implode("\r\n", $lines) . "\r\n";
    }

    /** @return array<string, array<string, float>> */
    private function buildGraph(array $data, string $field): array
    {
        $graph = [];
        foreach ($data as $line) {
            $value = is_array($line)
                ? ($line[$field] ?? $line['AUTHORS'] ?? '')
                : $line;
            $items = is_array($value) ? $value : explode(';', (string) $value);
            $documentAuthors = [];

            array_walk_recursive($items, function ($name) use (&$documentAuthors): void {
                if (! is_scalar($name)) {
                    return;
                }
                $name = trim(preg_replace('/\s+/u', ' ', (string) $name) ?? '');
                if ($name !== '') {
                    $documentAuthors[$name] = $name;
                }
            });

            $keys = array_keys($documentAuthors);
            foreach ($keys as $key) {
                $graph[$key] ??= [];
            }
            for ($i = 0, $total = count($keys); $i < $total; $i++) {
                for ($j = $i + 1; $j < $total; $j++) {
                    $a = $keys[$i];
                    $b = $keys[$j];
                    $graph[$a][$b] = ($graph[$a][$b] ?? 0) + 1;
                    $graph[$b][$a] = ($graph[$b][$a] ?? 0) + 1;
                }
            }
        }
        ksort($graph, SORT_NATURAL | SORT_FLAG_CASE);
        return $graph;
    }

    /** @return array<string, float> */
    private function betweenness(array $graph): array
    {
        $nodes = array_keys($graph);
        $centrality = array_fill_keys($nodes, 0.0);
        foreach ($nodes as $source) {
            $stack = [];
            $predecessors = array_fill_keys($nodes, []);
            $paths = array_fill_keys($nodes, 0.0);
            $distance = array_fill_keys($nodes, INF);
            $paths[$source] = 1.0;
            $distance[$source] = 0.0;
            $queue = new \SplPriorityQueue();
            $queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
            $queue->insert($source, 0.0);

            while (! $queue->isEmpty()) {
                $item = $queue->extract();
                $node = $item['data'];
                $currentDistance = -$item['priority'];
                if ($currentDistance > $distance[$node] + 1.0e-12) {
                    continue;
                }
                $stack[] = $node;
                foreach ($graph[$node] as $neighbor => $weight) {
                    $candidate = $distance[$node] + (1 / $weight);
                    if ($candidate < $distance[$neighbor] - 1.0e-12) {
                        $distance[$neighbor] = $candidate;
                        $queue->insert($neighbor, -$candidate);
                        $paths[$neighbor] = $paths[$node];
                        $predecessors[$neighbor] = [$node];
                    } elseif (abs($candidate - $distance[$neighbor]) <= 1.0e-12) {
                        $paths[$neighbor] += $paths[$node];
                        $predecessors[$neighbor][] = $node;
                    }
                }
            }

            $dependency = array_fill_keys($nodes, 0.0);
            while (($node = array_pop($stack)) !== null) {
                foreach ($predecessors[$node] as $previous) {
                    if ($paths[$node] > 0) {
                        $dependency[$previous] += ($paths[$previous] / $paths[$node]) * (1 + $dependency[$node]);
                    }
                }
                if ($node !== $source) {
                    $centrality[$node] += $dependency[$node];
                }
            }
        }

        $n = count($nodes);
        foreach ($centrality as $node => $value) {
            $centrality[$node] = $n > 2 ? $value / (($n - 1) * ($n - 2)) : 0.0;
        }
        return $centrality;
    }

    /** @return array<string, float> */
    private function closeness(array $graph): array
    {
        $result = [];
        $n = count($graph);
        foreach (array_keys($graph) as $source) {
            $distance = array_fill_keys(array_keys($graph), INF);
            $distance[$source] = 0.0;
            $queue = new \SplPriorityQueue();
            $queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
            $queue->insert($source, 0.0);
            while (! $queue->isEmpty()) {
                $item = $queue->extract();
                $node = $item['data'];
                $current = -$item['priority'];
                if ($current > $distance[$node] + 1.0e-12) continue;
                foreach ($graph[$node] as $neighbor => $weight) {
                    $candidate = $current + (1 / $weight);
                    if ($candidate < $distance[$neighbor]) {
                        $distance[$neighbor] = $candidate;
                        $queue->insert($neighbor, -$candidate);
                    }
                }
            }
            $finite = array_filter($distance, static fn ($d): bool => is_finite($d) && $d > 0);
            $reachable = count($finite);
            $result[$source] = ($reachable > 0 && $n > 1)
                ? ($reachable / array_sum($finite)) * ($reachable / ($n - 1))
                : 0.0;
        }
        return $result;
    }

    /** @return array<string, float> */
    private function eigenvector(array $graph): array
    {
        $nodes = array_keys($graph);
        $values = array_fill_keys($nodes, 1 / sqrt(max(1, count($nodes))));
        for ($iteration = 0; $iteration < 1000; $iteration++) {
            $next = array_fill_keys($nodes, 0.0);
            foreach ($nodes as $node) {
                foreach ($graph[$node] as $neighbor => $weight) {
                    $next[$node] += $weight * $values[$neighbor];
                }
            }
            $norm = sqrt(array_sum(array_map(static fn ($v): float => $v * $v, $next)));
            if ($norm == 0.0) return array_fill_keys($nodes, 0.0);
            foreach ($next as $node => $value) $next[$node] = $value / $norm;
            $difference = max(array_map(static fn ($node): float => abs($next[$node] - $values[$node]), $nodes));
            $values = $next;
            if ($difference < 1.0e-10) break;
        }
        return $values;
    }

    /** @return array{0: array<string, int>, 1: float} */
    private function communities(array $graph): array
    {
        $nodes = array_keys($graph);
        $community = array_combine($nodes, array_keys($nodes));
        $degree = [];
        foreach ($graph as $node => $neighbors) $degree[$node] = array_sum($neighbors);
        $twiceWeight = array_sum($degree);
        if ($twiceWeight == 0.0) return [$community, 0.0];
        $totals = [];
        foreach ($community as $node => $group) $totals[$group] = $degree[$node];

        for ($pass = 0; $pass < 100; $pass++) {
            $moved = false;
            foreach ($nodes as $node) {
                $old = $community[$node];
                $neighborWeights = [];
                foreach ($graph[$node] as $neighbor => $weight) {
                    $group = $community[$neighbor];
                    $neighborWeights[$group] = ($neighborWeights[$group] ?? 0) + $weight;
                }
                $totals[$old] -= $degree[$node];
                $best = $old;
                $bestGain = 0.0;
                foreach ($neighborWeights as $group => $insideWeight) {
                    $gain = $insideWeight - (($totals[$group] ?? 0) * $degree[$node] / $twiceWeight);
                    if ($gain > $bestGain + 1.0e-12) {
                        $bestGain = $gain;
                        $best = $group;
                    }
                }
                $community[$node] = $best;
                $totals[$best] = ($totals[$best] ?? 0) + $degree[$node];
                $moved = $moved || $best !== $old;
            }
            if (! $moved) break;
        }

        $groups = array_values(array_unique($community));
        sort($groups);
        $renumber = array_flip($groups);
        foreach ($community as $node => $group) $community[$node] = $renumber[$group];

        $modularity = 0.0;
        foreach ($nodes as $a) {
            foreach ($nodes as $b) {
                if ($community[$a] === $community[$b]) {
                    $modularity += ($graph[$a][$b] ?? 0) - ($degree[$a] * $degree[$b] / $twiceWeight);
                }
            }
        }
        return [$community, $modularity / $twiceWeight];
    }
}
