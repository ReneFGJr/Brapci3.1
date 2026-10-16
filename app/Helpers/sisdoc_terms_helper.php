<?php

if (! function_exists('termStopwords')) {
    /** @return array<string, true> */
    function termStopwords(): array
    {
        $words = [
            'a', 'as', 'ao', 'aos', 'com', 'da', 'das', 'de', 'do', 'dos',
            'e', 'em', 'na', 'nas', 'no', 'nos', 'o', 'os', 'ou', 'para',
            'por', 'que', 'se', 'um', 'uma', 'the', 'of', 'and', 'in', 'to',
        ];

        $file = defined('FCPATH') ? FCPATH . '_ia/domain_stopword.txt' : '';
        if ($file !== '' && is_file($file) && is_readable($file)) {
            $contents = file_get_contents($file);
            if (is_string($contents)) {
                preg_match_all("/\\\$sw\\['([^']+)'\\]/", $contents, $matches);
                $words = array_merge($words, $matches[1] ?? []);
            }
        }

        return array_fill_keys(array_unique($words), true);
    }
}

if (! function_exists('singularizeTerm')) {
    /** Converte plurais regulares em portugues para o singular. */
    function singularizeTerm(string $term): string
    {
        $invariableTerms = [
            'atlas', 'campus', 'corpus', 'gratis', 'lapis', 'oasis', 'onibus',
            'pires', 'status', 'tenis', 'virus',
        ];

        if (strlen($term) < 4 || in_array($term, $invariableTerms, true)) {
            return $term;
        }

        $rules = [
            '/oes$/' => 'ao',
            '/aes$/' => 'ao',
            '/ais$/' => 'al',
            '/eis$/' => 'el',
            '/ois$/' => 'ol',
            '/uis$/' => 'ul',
            '/ns$/' => 'm',
            '/([rz])es$/' => '$1',
            '/s$/' => '',
        ];

        return preg_replace(array_keys($rules), array_values($rules), $term, 1) ?? $term;
    }
}

if (! function_exists('normalizeTerm')) {
    /**
     * Padroniza um termo sem acentos ou caracteres especiais, mantendo apenas
     * a primeira letra em maiusculo.
     */
    function normalizeTerm(string $term): string
    {
        $term = strtr($term, [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I',
            'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O',
            'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
            'Ü' => 'U', 'Ý' => 'Y', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y',
        ]);
        $term = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $term) ?: $term;
        $term = preg_replace('/[^A-Za-z0-9]+/', ' ', $term) ?? '';
        $term = trim(preg_replace('/\s+/', ' ', $term) ?? '');

        return ucfirst(strtolower($term));
    }
}

if (! function_exists('normalizeTerm')) {
    /**
     * Padroniza um termo sem acentos ou caracteres especiais, mantendo apenas
     * a primeira letra em maiusculo.
     */
    function normalizeTerm(string $term): string
    {
        $term = strtr($term, [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I',
            'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O',
            'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
            'Ü' => 'U', 'Ý' => 'Y', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y',
        ]);
        $term = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $term) ?: $term;
        $term = preg_replace('/[^A-Za-z0-9]+/', ' ', $term) ?? '';
        $term = trim(preg_replace('/\s+/', ' ', $term) ?? '');

        return ucfirst(strtolower($term));
    }
}
