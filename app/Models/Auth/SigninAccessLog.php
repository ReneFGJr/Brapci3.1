<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class SigninAccessLog extends Model
{
    protected $DBGroup = 'click';
    protected $table = 'signin_access_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'username', 'successful', 'reason',
        'ip_address', 'user_agent', 'created_at',
    ];
}
