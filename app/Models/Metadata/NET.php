<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class NET extends Model
{
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
                $documentAuthors[$key] = $name;
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
}
