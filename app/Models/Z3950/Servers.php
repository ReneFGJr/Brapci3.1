<?php
namespace App\Models\Z3950;

use CodeIgniter\Model;

class Servers extends Model
{
    protected $DBGroup = 'z3950';
    protected $table = 'z3950_servers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'code', 'name', 'host', 'port', 'database_name', 'record_syntax',
        'last_active', 'active',
    ];
}
