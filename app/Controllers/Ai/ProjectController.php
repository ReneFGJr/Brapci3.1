<?php

namespace App\Controllers\Ai;

use App\Services\Ai\ProjectService;
use Throwable;

class ProjectController extends ApiController
{
    private ProjectService $service;

    public function __construct()
    {
        $this->service = new ProjectService();
    }

    public function index()
    {
        return $this->response->setJSON(['data' => $this->service->list($this->userId())]);
    }

    public function show($id = null)
    {
        $project = $this->service->findOwned((int) $id, $this->userId());
        return $project
            ? $this->response->setJSON(['data' => $project])
            : $this->response->setStatusCode(404)->setJSON(['message' => 'Projeto nao encontrado.']);
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->validateData($data, ['name' => 'required|max_length[150]', 'default_model' => 'permit_empty|max_length[150]'])) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $this->validator->getErrors()]);
        }
        try {
            return $this->response->setStatusCode(201)->setJSON(['data' => $this->service->create($this->userId(), $this->fields($data))]);
        } catch (Throwable $exception) {
            return $this->failure($exception);
        }
    }

    public function update($id = null)
    {
        $data = $this->input();
        if (isset($data['name']) && ! $this->validateData($data, ['name' => 'required|max_length[150]', 'default_model' => 'permit_empty|max_length[150]'])) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $this->validator->getErrors()]);
        }
        $project = $this->service->update((int) $id, $this->userId(), $this->fields($data));
        return $project
            ? $this->response->setJSON(['data' => $project])
            : $this->response->setStatusCode(404)->setJSON(['message' => 'Projeto nao encontrado.']);
    }

    public function delete($id = null)
    {
        return $this->service->delete((int) $id, $this->userId())
            ? $this->response->setStatusCode(204)
            : $this->response->setStatusCode(404)->setJSON(['message' => 'Projeto nao encontrado.']);
    }

    private function fields(array $data): array
    {
        return array_intersect_key($data, array_flip(['name', 'description', 'system_prompt', 'context', 'default_model']));
    }
}
