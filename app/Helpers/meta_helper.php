<?php

if (!function_exists('meta')) {

    function meta(array $meta = []): string
    {
        $html = '';

        foreach ($meta as $name => $content) {

            /* Caso 1: keywords como array */
            if (is_array($content) && $name === 'keywords') {
                $content = implode(', ', $content);
            }

            /* Caso 2: meta complexa (og:, twitter:) */
            if (is_array($content)) {

                $property = $content['property'] ?? $name;
                $value    = $content['content']  ?? '';

                if ($value !== '') {
                    $html .= '<meta property="' . esc($property) .
                        '" content="' . esc($value) . '">' . PHP_EOL;
                }
            } else {

                /* Caso 3: meta simples */
                $html .= '<meta name="' . esc($name) .
                    '" content="' . esc($content) . '">' . PHP_EOL;
            }
        }

        return $html;
    }
}
