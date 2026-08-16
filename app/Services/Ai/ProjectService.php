<?php

namespace App\Services\Ai;

use App\Models\AI\ProjectModel;

class ProjectService
{
    public function __construct(private ?ProjectModel $projects = null)
    {
        $this->projects ??= new ProjectModel();
    }

    public function list(int $userId): array
    {
        return $this->projects->where('user_id', $userId)->orderBy('updated_at', 'DESC')->findAll();
    }

    public function findOwned(int $id, int $userId): ?array
    {
        return $this->projects->where(['id' => $id, 'user_id' => $userId])->first();
    }

    public function create(int $userId, array $data): array
    {
        $data['user_id'] = $userId;
        $id = $this->projects->insert($data, true);
        return $this->findOwned((int) $id, $userId);
    }

    public function update(int $id, int $userId, array $data): ?array
    {
        if (! $this->findOwned($id, $userId)) {
            return null;
        }
        unset($data['id'], $data['user_id']);
        $this->projects->update($id, $data);
        return $this->findOwned($id, $userId);
    }

    public function delete(int $id, int $userId): bool
    {
        return $this->findOwned($id, $userId) !== null && $this->projects->delete($id);
    }
}
