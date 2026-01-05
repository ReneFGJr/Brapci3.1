<?php

if (!function_exists('meta')) {
    function meta(array $meta = []): string
    {
        $html = '';

        foreach ($meta as $name => $content) {
            $html .= '<meta name="' . esc($name) . '" content="' . esc($content) . '">' . PHP_EOL;
        }

        return $html;
    }
}
