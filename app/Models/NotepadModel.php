<?php

namespace App\Models;

class NotepadModel
{
    private string $storagePath;

    public function __construct()
    {
        $this->storagePath = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '_repository' . DIRECTORY_SEPARATOR . 'notepad' . DIRECTORY_SEPARATOR;
        if (!is_dir($this->storagePath)) {
            @mkdir($this->storagePath, 0775, true);
        }
    }

    public function sanitizeSlug(string $slug): string
    {
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-_');

        if ($slug === '') {
            return '';
        }

        return substr($slug, 0, 120);
    }

    public function exists(string $slug): bool
    {
        return is_file($this->getFilePath($slug));
    }

    public function read(string $slug): array
    {
        $file = $this->getFilePath($slug);
        if (!is_file($file)) {
            return [
                'slug' => $slug,
                'content' => '',
                'created_at' => null,
                'updated_at' => null,
            ];
        }

        $json = @file_get_contents($file);
        $data = json_decode((string) $json, true);
        if (!is_array($data)) {
            return [
                'slug' => $slug,
                'content' => '',
                'created_at' => null,
                'updated_at' => null,
            ];
        }

        return [
            'slug' => $slug,
            'content' => (string) ($data['content'] ?? ''),
            'created_at' => $data['created_at'] ?? null,
            'updated_at' => $data['updated_at'] ?? null,
        ];
    }

    public function write(string $slug, string $content): array
    {
        $current = $this->read($slug);
        $now = date('Y-m-d H:i:s');

        $payload = [
            'slug' => $slug,
            'content' => $content,
            'created_at' => $current['created_at'] ?? $now,
            'updated_at' => $now,
        ];

        $ok = (bool) @file_put_contents(
            $this->getFilePath($slug),
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );

        if (!$ok) {
            throw new \RuntimeException('Nao foi possivel salvar o bloco de notas.');
        }

        return $payload;
    }

    private function getFilePath(string $slug): string
    {
        return $this->storagePath . $slug . '.json';
    }
}
