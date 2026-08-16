<?php

namespace App\Models\AI;

use CodeIgniter\Model;

class UserSettingModel extends Model
{
    protected $DBGroup = 'brapci_ai';
    protected $table = 'ai_user_settings';
    protected $primaryKey = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = '';
    protected $allowedFields = ['user_id', 'default_model', 'temperature', 'num_ctx', 'stream'];
}
