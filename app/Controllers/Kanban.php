<?php

namespace App\Controllers;

use App\Models\Kanban\TaskModel;
use App\Models\CommentModel;
use App\Models\UserModel;

helper(['boostrap', 'url', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);

class Kanban extends BaseController
{
    public function index()
    {
        $taskModel = new TaskModel();
        $tasks = $taskModel->findAll();

        return view('Kanban/index', ['tasks' => $tasks]);
    }

    public function update($id)
    {
        $taskModel = new TaskModel();
        $taskModel->update($id, $this->request->getPost());
        return redirect()->to('/kanban');
    }

    public function create()
    {
        return view('Kanban/create');
    }

    public function store()
    {
        $taskModel = new TaskModel();
        $taskModel->save($this->request->getPost());

        return redirect()->to('/kanban');
    }

    public function addComment($taskId)
    {
        $commentModel = new CommentModel();
        $commentModel->save([
            'task_id' => $taskId,
            'user_id' => 1, // trocar pelo usuário logado
            'comment' => $this->request->getPost('comment')
        ]);

        return redirect()->to('/kanban');
    }
}
