<?php

use Parsedown;

if (!function_exists('markdown_to_html')) {
    function markdown_to_html(string $markdown): string
    {
        $parsedown = new Parsedown();
        return $parsedown->text($markdown);
    }
}
