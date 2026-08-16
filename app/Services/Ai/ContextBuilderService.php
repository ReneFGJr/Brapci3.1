<?php

namespace App\Services\Ai;

class ContextBuilderService
{
    public function build(array $chat, ?array $project, array $history, string $currentMessage): array
    {
        $messages = [];
        $globalPrompt = trim((string) env('OLLAMA_SYSTEM_PROMPT', ''));
        foreach ([$globalPrompt, trim((string) ($project['system_prompt'] ?? ''))] as $prompt) {
            if ($prompt !== '') {
                $messages[] = ['role' => 'system', 'content' => $prompt];
            }
        }
        $projectContext = trim((string) ($project['context'] ?? ''));
        if ($projectContext !== '') {
            $messages[] = ['role' => 'system', 'content' => "Contexto persistente do projeto:\n" . $projectContext];
        }
        $chatPrompt = trim((string) ($chat['system_prompt'] ?? ''));
        if ($chatPrompt !== '') {
            $messages[] = ['role' => 'system', 'content' => $chatPrompt];
        }

        $limit = max(1, (int) env('OLLAMA_HISTORY_MESSAGES', 40));
        foreach (array_slice($history, -$limit) as $message) {
            if (in_array($message['role'], ['user', 'assistant', 'tool'], true)) {
                $messages[] = ['role' => $message['role'], 'content' => $message['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $currentMessage];
        return $messages;
    }
}
