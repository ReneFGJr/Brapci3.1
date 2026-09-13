<?php

namespace App\Services;

class CitationHalfLifeAnalyzer
{
    private const TYPES = [
        'artigos',
        'eventos',
        'livros',
        'capitulos_de_livros',
        'sites',
        'teses',
        'dissertacoes',
        'outras_tipologias',
    ];

    private const LANGUAGES = ['portugues', 'ingles', 'espanhol', 'frances', 'nao_identificado'];

    public function analyze(string $text, ?int $currentYear = null): array
    {
        $currentYear ??= (int)date('Y');
        $lines = preg_split('/\R/u', html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?: [];
        $lines = array_values(array_filter(array_map('trim', $lines), static fn (string $line): bool => $line !== ''));

        $typologies = [];
        foreach (self::TYPES as $type) {
            $typologies[$type] = ['quantidade' => 0, 'percentual' => 0.0, 'referencias' => []];
        }
        $languages = [];
        foreach (self::LANGUAGES as $language) {
            $languages[$language] = ['quantidade' => 0, 'percentual' => 0.0, 'referencias' => []];
        }

        $references = [];
        $years = [];
        $withoutYear = 0;

        foreach ($lines as $line) {
            $analysisLine = $this->beforeAvailability($line);
            $year = $this->extractYear($analysisLine, $currentYear);
            $type = $this->identifyType($analysisLine);
            $language = $this->identifyLanguage($analysisLine);
            $item = [
                'referencia' => $analysisLine,
                'ano' => $year,
                'idade' => $year === null ? null : $currentYear - $year,
                'tipologia' => $type,
                'idioma' => $language,
            ];

            $references[] = $item;
            $typologies[$type]['quantidade']++;
            $typologies[$type]['referencias'][] = $analysisLine;
            $languages[$language]['quantidade']++;
            $languages[$language]['referencias'][] = $analysisLine;

            if ($year === null) {
                $withoutYear++;
            } else {
                $years[] = $year;
            }
        }

        $total = count($references);
        foreach ($typologies as &$typology) {
            $typology['percentual'] = $total > 0
                ? round(($typology['quantidade'] / $total) * 100, 2)
                : 0.0;
        }
        unset($typology);
        foreach ($languages as &$language) {
            $language['percentual'] = $total > 0
                ? round(($language['quantidade'] / $total) * 100, 2)
                : 0.0;
        }
        unset($language);

        sort($years, SORT_NUMERIC);
        $medianYear = $this->median($years);
        $distribution = array_count_values($years);
        ksort($distribution, SORT_NUMERIC);

        return [
            'ano_atual' => $currentYear,
            'total_referencias' => $total,
            'referencias_com_ano' => count($years),
            'referencias_sem_ano' => $withoutYear,
            'ano_mediano' => $medianYear,
            'meia_vida' => $medianYear === null ? null : round($currentYear - $medianYear, 2),
            'ano_mais_antigo' => $years === [] ? null : min($years),
            'ano_mais_recente' => $years === [] ? null : max($years),
            'distribuicao_por_ano' => $distribution,
            'tipologias' => $typologies,
            'idiomas' => $languages,
            'referencias' => $references,
        ];
    }

    private function extractYear(string $reference, int $currentYear): ?int
    {
        preg_match_all('/(?<!\d)(1[5-9]\d{2}|20\d{2})(?!\d)/u', $reference, $matches);
        $validYears = [];
        foreach ($matches[1] ?? [] as $year) {
            $year = (int)$year;
            if ($year <= $currentYear) {
                $validYears[] = $year;
            }
        }

        return $validYears === [] ? null : $validYears[array_key_last($validYears)];
    }

    private function beforeAvailability(string $reference): string
    {
        $parts = preg_split('/\bdispon[ií]vel\s+em\s*:?/iu', $reference, 2);
        return trim($parts[0] ?? $reference);
    }

    private function identifyType(string $reference): string
    {
        $normalized = $this->normalize($reference);

        if ($this->matches($normalized, ['dissertacao', 'dissertation', 'mestrado', 'master thesis'])) {
            return 'dissertacoes';
        }
        if ($this->matches($normalized, ['tese', 'thesis', 'doutorado', 'doctoral'])) {
            return 'teses';
        }
        if ($this->matches($normalized, ['capitulo', 'chapter', ' in:', ' in '])
            && $this->matches($normalized, ['livro', 'book', 'org.', 'editor', 'editora', ' p. '])) {
            return 'capitulos_de_livros';
        }
        if ($this->matches($normalized, [
            'anais', 'proceedings', 'congresso', 'conference', 'seminario', 'simposio',
            'encontro nacional', 'workshop', 'evento',
        ])) {
            return 'eventos';
        }
        if ($this->matches($normalized, [
            'isbn', 'editora', 'edition', 'edicao', ' ed.', 'publisher',
        ])) {
            return 'livros';
        }
        if ($this->matches($normalized, [
            'doi:', 'doi.org/', 'revista', 'journal', 'periodico', 'v.', 'vol.', 'volume',
            'n.', 'issue', 'p.', 'pp.',
        ])) {
            return 'artigos';
        }
        if ($this->matches($normalized, [
            'http://', 'https://', 'www.', 'disponivel em:', 'acesso em:', 'website', 'site:',
        ])) {
            return 'sites';
        }

        return 'outras_tipologias';
    }

    private function identifyLanguage(string $reference): string
    {
        $normalized = ' ' . $this->normalize($reference) . ' ';
        $markers = [
            'portugues' => [
                ' disponivel em:', ' acesso em:', ' edicao', ' revista ', ' universidade ',
                ' dissertacao', ' mestrado', ' doutorado', ' capitulo', ' livro ', ' anais ',
                ' artigo ', ' traducao', ' organizacao', ' numero ', ' paginas ',
            ],
            'ingles' => [
                ' available at:', ' accessed ', ' edition', ' journal ', ' university ',
                ' dissertation', ' master', ' doctoral', ' chapter', ' book ', ' proceedings',
                ' translation', ' volume ', ' issue ', ' pages ',
            ],
            'espanhol' => [
                ' disponible en:', ' consultado ', ' edicion', ' revista ', ' universidad ',
                ' tesis', ' maestria', ' doctorado', ' capitulo', ' libro ', ' congreso',
                ' traduccion', ' numero ', ' paginas ',
            ],
            'frances' => [
                ' disponible sur:', ' consulte ', ' edition', ' revue ', ' universite ',
                ' these', ' memoire', ' doctorat', ' chapitre', ' livre ', ' actes ',
                ' traduction', ' numero ', ' pages ',
            ],
        ];

        $scores = array_fill_keys(array_keys($markers), 0);
        foreach ($markers as $language => $terms) {
            foreach ($terms as $term) {
                if (str_contains($normalized, $term)) {
                    $scores[$language]++;
                }
            }
        }

        $original = mb_strtolower($reference, 'UTF-8');
        if (preg_match('/[ãõ]|ção|ções/u', $original)) {
            $scores['portugues']++;
        }
        if (preg_match('/[ñ¿¡]/u', $original)) {
            $scores['espanhol'] += 2;
        }
        if (preg_match('/[àèùëïÿœæ]/u', $original)) {
            $scores['frances'] += 2;
        }

        $highest = max($scores);
        if ($highest === 0 || count(array_keys($scores, $highest, true)) > 1) {
            return 'nao_identificado';
        }

        return (string)array_search($highest, $scores, true);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return $ascii === false ? $text : $ascii;
    }

    private function matches(string $text, array $terms): bool
    {
        foreach ($terms as $term) {
            if (str_contains($text, $term)) {
                return true;
            }
        }

        return false;
    }

    private function median(array $values): ?float
    {
        $count = count($values);
        if ($count === 0) {
            return null;
        }

        $middle = intdiv($count, 2);
        if ($count % 2 === 1) {
            return (float)$values[$middle];
        }

        return ($values[$middle - 1] + $values[$middle]) / 2;
    }
}
