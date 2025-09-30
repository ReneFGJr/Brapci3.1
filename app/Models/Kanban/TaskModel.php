<?php

namespace App\Models\Kanban;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $DBGroup = 'kanban';
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title','description','status','priority','assigned_to','deadline'
    ];
}
