<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $DBGroup = 'kanban';
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'task_id','user_id','comment'
    ];
}
