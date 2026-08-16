<?php

namespace App\Controllers\Ai;

use App\Services\Ai\ChatService;
use CodeIgniter\HTTP\Cors;
use Throwable;

class ChatController extends ApiController
{
    private ChatService $service;

    public function __construct()
    {
        $this->service = new ChatService();
    }

    public function index()
    {
        $projectId = $this->request->getGet('project_id');
        $data = $this->service->list(
            $this->userId(),
            $projectId === null ? null : (int) $projectId,
            (int) ($this->request->getGet('limit') ?? 50),
            (int) ($this->request->getGet('offset') ?? 0),
        );
        return $this->response->setJSON(['data' => $data]);
    }

    public function show($id = null)
    {
        $chat = $this->service->findOwned((int) $id, $this->userId());
        return $chat
            ? $this->response->setJSON(['data' => $chat])
            : $this->response->setStatusCode(404)->setJSON(['message' => 'Conversa nao encontrada.']);
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->validateData($data, ['title' => 'permit_empty|max_length[255]', 'model' => 'permit_empty|max_length[150]', 'project_id' => 'permit_empty|is_natural_no_zero'])) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $this->validator->getErrors()]);
        }
        try {
            return $this->response->setStatusCode(201)->setJSON(['data' => $this->service->create($this->userId(), $data)]);
        } catch (Throwable $exception) {
            return $this->failure($exception);
        }
    }

    public function update($id = null)
    {
        try {
            $chat = $this->service->update((int) $id, $this->userId(), $this->input());
            return $chat
                ? $this->response->setJSON(['data' => $chat])
                : $this->response->setStatusCode(404)->setJSON(['message' => 'Conversa nao encontrada.']);
        } catch (Throwable $exception) {
            return $this->failure($exception);
        }
    }

    public function delete($id = null)
    {
        return $this->service->delete((int) $id, $this->userId())
            ? $this->response->setStatusCode(204)
            : $this->response->setStatusCode(404)->setJSON(['message' => 'Conversa nao encontrada.']);
    }

    public function messages($id = null)
    {
        $messages = $this->service->messages(
            (int) $id,
            $this->userId(),
            (int) ($this->request->getGet('limit') ?? 100),
            (int) ($this->request->getGet('before_id') ?? 0),
        );
        return $messages !== null
            ? $this->response->setJSON(['data' => $messages])
            : $this->response->setStatusCode(404)->setJSON(['message' => 'Conversa nao encontrada.']);
    }

    public function message($id = null)
    {
        return $this->stream((int) $id, false);
    }

    public function regenerate($id = null)
    {
        return $this->stream((int) $id, true);
    }

    public function streamMethodNotAllowed($id = null)
    {
        return $this->response
            ->setStatusCode(405)
            ->setHeader('Allow', 'POST')
            ->setJSON([
                'error' => 'method_not_allowed',
                'message' => 'Use POST com corpo JSON para este endpoint de streaming.',
            ]);
    }

    private function stream(int $chatId, bool $regenerate)
    {
        $data = $this->input();
        if (! $regenerate && ! $this->validateData($data, ['content' => 'required|max_length[50000]', 'request_id' => 'permit_empty|max_length[64]'])) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $this->validator->getErrors()]);
        }

        $this->response->setHeader('Content-Type', 'text/event-stream');
        $this->response->setHeader('Cache-Control', 'no-cache, no-transform, no-store');
        $this->response->setHeader('X-Accel-Buffering', 'no');
        Cors::factory()->addResponseHeaders($this->request, $this->response);
        $this->response->sendHeaders();

        $emit = static function (array $event): void {
            echo 'event: ' . ($event['type'] ?? 'message') . "\n";
            echo 'data: ' . json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";
            if (ob_get_level() > 0) {
                @ob_flush();
            }
            flush();
        };

        try {
            $this->service->streamMessage(
                $chatId,
                $this->userId(),
                trim((string) ($data['content'] ?? '')),
                isset($data['request_id']) ? trim((string) $data['request_id']) : null,
                $emit,
                $regenerate,
            );
        } catch (Throwable $exception) {
            $errorId = bin2hex(random_bytes(6));
            $technicalMessage = sprintf(
                '[AI stream:%s] %s(%s): %s',
                $errorId,
                $exception::class,
                (string) $exception->getCode(),
                $exception->getMessage(),
            );
            log_message('error', $technicalMessage);
            error_log($technicalMessage);

            $event = [
                'type' => 'error',
                'error_id' => $errorId,
                'message' => $exception->getCode() >= 500
                    ? 'Falha interna durante a geracao.'
                    : $exception->getMessage(),
            ];
            if (ENVIRONMENT === 'development') {
                $event['detail'] = $exception::class . ': ' . $exception->getMessage();
            }
            $emit($event);
        }
        return $this->response;
    }
}
