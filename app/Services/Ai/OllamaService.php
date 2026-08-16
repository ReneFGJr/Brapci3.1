<?php

namespace App\Services\Ai;

use Throwable;

class OllamaService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) env('OLLAMA_URL', 'http://localhost:11434'), '/');
        $this->timeout = max(1, (int) env('OLLAMA_TIMEOUT', 120));
    }

    public function models(): array
    {
        try {
            $response = service('curlrequest')->get($this->baseUrl . '/api/tags', [
                'timeout' => min($this->timeout, 15),
                'http_errors' => false,
            ]);
        } catch (Throwable $exception) {
            throw new OllamaException('Ollama indisponivel.', 503, $exception);
        }
        if ($response->getStatusCode() !== 200) {
            throw new OllamaException('Ollama indisponivel.', 503);
        }
        $payload = json_decode($response->getBody(), true);
        return array_values(array_map(static fn (array $model): array => [
            'name' => $model['name'],
            'size' => $model['size'] ?? null,
            'modified_at' => $model['modified_at'] ?? null,
            'details' => $model['details'] ?? null,
        ], $payload['models'] ?? []));
    }

    public function streamChat(string $model, array $messages, array $options, callable $onEvent): array
    {
        if (! function_exists('curl_init')) {
            throw new OllamaException('A extensao cURL do PHP e obrigatoria.', 500);
        }
        $result = [];
        $buffer = '';
        $ch = curl_init($this->baseUrl . '/api/chat');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['model' => $model, 'messages' => $messages, 'stream' => true, 'options' => $options], JSON_THROW_ON_ERROR),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk) use (&$buffer, &$result, $onEvent): int {
                $buffer .= $chunk;
                while (($position = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $position));
                    $buffer = substr($buffer, $position + 1);
                    if ($line !== '') {
                        $event = json_decode($line, true);
                        if (is_array($event)) {
                            $result = $event + $result;
                            $onEvent($event);
                        }
                    }
                }
                return strlen($chunk);
            },
        ]);
        $ok = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($ok === false || $status >= 400) {
            throw new OllamaException($error ?: 'Falha ao gerar resposta no Ollama.', $status === 404 ? 422 : 503);
        }
        return $result;
    }
}
