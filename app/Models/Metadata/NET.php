<?php

namespace App\Models\Metadata;

use CodeIgniter\Model;

class NET extends Model
{
    protected $DBGroup = 'elastic';
    protected $table = 'dataset';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * Gera uma rede de coautoria no formato Pajek a partir dos IDs informados.
     *
     * @param array<int, int|string> $ids
     */
    public function net_authors(array $dt): string
    {
        $authors = [];
        foreach ($dt as $id=>$line) {
            pre($line['AUTHORS'],false);
            $authors[] = trim((string) $id);
        }
exit;
        $ids = array_values(array_unique(array_filter(
            $ids,
            static fn ($id): bool => is_scalar($id) && trim((string) $id) !== ''
        )));

        if ($ids === []) {
            return $this->emptyNetwork();
        }

        $records = $this->select('ID, json')
            ->whereIn('ID', $ids)
            ->findAll();

        $authors = [];
        $edges = [];

        foreach ($records as $record) {
            $metadata = json_decode((string) ($record['json'] ?? ''), true);
            if (! is_array($metadata)) {
                continue;
            }

            $documentAuthors = $this->extractAuthors($metadata['Authors'] ?? []);

            foreach ($documentAuthors as $key => $name) {
                if (! isset($authors[$key])) {
                    $authors[$key] = ['name' => $name, 'documents' => 0];
                }

                $authors[$key]['documents']++;
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

        uasort($authors, static fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));

        $vertexIds = [];
        $lines = ['*Vertices ' . count($authors)];
        $vertex = 1;

        foreach ($authors as $key => $author) {
            $vertexIds[$key] = $vertex;
            $label = str_replace(['\\', '"'], ['\\\\', '\\"'], $author['name']);
            $lines[] = $vertex . ' "' . $label . '"';
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

    /**
     * @return array<string, string>
     */
    private function extractAuthors(mixed $value): array
    {
        $authors = [];

        if (is_array($value)) {
            foreach ($value as $item) {
                $authors += $this->extractAuthors($item);
            }

            return $authors;
        }

        if (! is_scalar($value)) {
            return $authors;
        }

        $name = trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');
        if ($name === '') {
            return $authors;
        }

        $key = mb_strtolower($name, 'UTF-8');
        $authors[$key] = $name;

        return $authors;
    }

    private function emptyNetwork(): string
    {
        return "*Vertices 0\r\n*Edges\r\n";
    }
}
