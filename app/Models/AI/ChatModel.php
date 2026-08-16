<?php

namespace App\Models\AI;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table = 'ai_chats';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'project_id', 'title', 'model', 'system_prompt', 'status'];
}
