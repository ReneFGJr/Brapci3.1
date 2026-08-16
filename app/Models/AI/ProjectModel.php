<?php

namespace App\Models\AI;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $DBGroup = 'brapci_ai';
    protected $table = 'ai_projects';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'name', 'description', 'system_prompt', 'context', 'default_model'];
}
