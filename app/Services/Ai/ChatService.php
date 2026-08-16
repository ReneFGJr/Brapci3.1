<?php

namespace App\Services\Ai;

use App\Models\AI\ChatModel;
use App\Models\AI\MessageModel;
use App\Models\AI\ProjectModel;
use App\Models\AI\UserSettingModel;
use RuntimeException;

class ChatService
{
    public function __construct(
        private ?ChatModel $chats = null,
        private ?MessageModel $messages = null,
        private ?ProjectModel $projects = null,
        private ?UserSettingModel $settings = null,
        private ?ContextBuilderService $contextBuilder = null,
        private ?OllamaService $ollama = null,
    ) {
        $this->chats ??= new ChatModel();
        $this->messages ??= new MessageModel();
        $this->projects ??= new ProjectModel();
        $this->settings ??= new UserSettingModel();
        $this->contextBuilder ??= new ContextBuilderService();
        $this->ollama ??= new OllamaService();
    }

    public function list(int $userId, ?int $projectId = null, int $limit = 50, int $offset = 0): array
    {
        $query = $this->chats->where('user_id', $userId)->where('status !=', 'deleted');
        if ($projectId !== null) {
            $query->where('project_id', $projectId);
        }
        return $query->orderBy('updated_at', 'DESC')->findAll(min(max($limit, 1), 100), max($offset, 0));
    }

    public function findOwned(int $id, int $userId): ?array
    {
        return $this->chats->where(['id' => $id, 'user_id' => $userId])->where('status !=', 'deleted')->first();
    }

    public function create(int $userId, array $data): array
    {
        $project = $this->ownedProject($data['project_id'] ?? null, $userId);
        $setting = $this->settings->find($userId);
        $model = trim((string) ($data['model'] ?? $project['default_model'] ?? $setting['default_model'] ?? env('OLLAMA_DEFAULT_MODEL', '')));
        if ($model === '') {
            throw new RuntimeException('Selecione um modelo para criar a conversa.', 422);
        }
        $id = $this->chats->insert([
            'user_id' => $userId,
            'project_id' => $project['id'] ?? null,
            'title' => trim((string) ($data['title'] ?? 'Nova conversa')) ?: 'Nova conversa',
            'model' => $model,
            'system_prompt' => $data['system_prompt'] ?? null,
            'status' => 'active',
        ], true);
        return $this->findOwned((int) $id, $userId);
    }

    public function update(int $id, int $userId, array $data): ?array
    {
        if (! $this->findOwned($id, $userId)) {
            return null;
        }
        if (array_key_exists('project_id', $data)) {
            $this->ownedProject($data['project_id'], $userId);
        }
        $allowed = array_intersect_key($data, array_flip(['project_id', 'title', 'model', 'system_prompt', 'status']));
        if (isset($allowed['status']) && ! in_array($allowed['status'], ['active', 'archived'], true)) {
            unset($allowed['status']);
        }
        $this->chats->update($id, $allowed);
        return $this->findOwned($id, $userId);
    }

    public function delete(int $id, int $userId): bool
    {
        return $this->findOwned($id, $userId) !== null && $this->chats->update($id, ['status' => 'deleted']);
    }

    public function messages(int $chatId, int $userId, int $limit = 100, int $beforeId = 0): ?array
    {
        if (! $this->findOwned($chatId, $userId)) {
            return null;
        }
        $query = $this->messages->where('chat_id', $chatId);
        if ($beforeId > 0) {
            $query->where('id <', $beforeId);
        }
        return array_reverse($query->orderBy('id', 'DESC')->findAll(min(max($limit, 1), 200)));
    }

    public function streamMessage(int $chatId, int $userId, string $content, ?string $requestId, callable $emit, bool $regenerate = false): void
    {
        $chat = $this->findOwned($chatId, $userId);
        if (! $chat) {
            throw new RuntimeException('Conversa nao encontrada.', 404);
        }
        $history = $this->messages->where('chat_id', $chatId)->orderBy('id', 'ASC')->findAll();
        if ($regenerate) {
            $lastUser = null;
            foreach (array_reverse($history) as $message) {
                if ($message['role'] === 'user') {
                    $lastUser = $message;
                    break;
                }
            }
            if (! $lastUser) {
                throw new RuntimeException('Nao ha mensagem para regenerar.', 422);
            }
            $content = $lastUser['content'];
            $history = array_values(array_filter($history, static fn (array $item): bool => (int) $item['id'] < (int) $lastUser['id']));
        } else {
            if ($requestId && $this->messages->where(['chat_id' => $chatId, 'request_id' => $requestId])->first()) {
                throw new RuntimeException('Esta mensagem ja foi processada.', 409);
            }
            $this->messages->insert(['chat_id' => $chatId, 'role' => 'user', 'content' => $content, 'request_id' => $requestId]);
        }

        $project = $chat['project_id'] ? $this->projects->find($chat['project_id']) : null;
        $setting = $this->settings->find($userId) ?? [];
        $prompt = $this->contextBuilder->build($chat, $project, $history, $content);
        $full = '';
        $started = microtime(true);
        $result = $this->ollama->streamChat($chat['model'], $prompt, [
            'temperature' => (float) ($setting['temperature'] ?? 0.7),
            'num_ctx' => (int) ($setting['num_ctx'] ?? 8192),
        ], static function (array $event) use (&$full, $emit): void {
            $piece = (string) ($event['message']['content'] ?? '');
            if ($piece !== '') {
                $full .= $piece;
                $emit(['type' => 'token', 'content' => $piece]);
            }
        });

        $messageId = $this->messages->insert([
            'chat_id' => $chatId,
            'role' => 'assistant',
            'content' => $full,
            'model' => $chat['model'],
            'tokens_input' => $result['prompt_eval_count'] ?? null,
            'tokens_output' => $result['eval_count'] ?? null,
            'generation_time_ms' => (int) round((microtime(true) - $started) * 1000),
            'status' => 'completed',
        ], true);
        $this->chats->update($chatId, ['status' => $chat['status']]);
        $emit(['type' => 'done', 'message_id' => (int) $messageId, 'model' => $chat['model']]);
    }

    private function ownedProject($projectId, int $userId): ?array
    {
        if ($projectId === null || $projectId === '') {
            return null;
        }
        $project = $this->projects->where(['id' => (int) $projectId, 'user_id' => $userId])->first();
        if (! $project) {
            throw new RuntimeException('Projeto nao encontrado.', 404);
        }
        return $project;
    }
}
