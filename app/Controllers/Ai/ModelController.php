<?php

namespace App\Controllers\Ai;

use App\Services\Ai\OllamaService;
use Throwable;

class ModelController extends ApiController
{
    public function index()
    {
        try {
            return $this->response->setJSON(['data' => (new OllamaService())->models()]);
        } catch (Throwable $exception) {
            return $this->failure($exception);
        }
    }
}
