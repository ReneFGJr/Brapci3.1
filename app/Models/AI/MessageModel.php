<?php

namespace App\Models\AI;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $DBGroup = 'brapci_ai';
    protected $table = 'ai_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $updatedField = '';
    protected $allowedFields = [
        'chat_id', 'role', 'content', 'model', 'tokens_input', 'tokens_output',
        'generation_time_ms', 'status', 'request_id',
    ];
}
