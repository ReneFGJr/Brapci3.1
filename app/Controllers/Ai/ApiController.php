<?php

namespace App\Controllers\Ai;

use App\Controllers\BaseController;
use Throwable;

abstract class ApiController extends BaseController
{
    protected function userId(): int
    {
        return (int) session()->get('user_id');
    }

    protected function input(): array
    {
        $json = $this->request->getJSON(true);
        return is_array($json) ? $json : $this->request->getPost();
    }

    protected function failure(Throwable $exception)
    {
        $status = $exception->getCode();
        $status = is_int($status) && $status >= 400 && $status < 600 ? $status : 500;
        log_message('error', '[AI API] {class}: {message}', ['class' => $exception::class, 'message' => $exception->getMessage()]);
        return $this->response->setStatusCode($status)->setJSON([
            'error' => $status >= 500 ? 'service_error' : 'request_error',
            'message' => $status >= 500 ? 'Nao foi possivel concluir a solicitacao.' : $exception->getMessage(),
        ]);
    }
}
