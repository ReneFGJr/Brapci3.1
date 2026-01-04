<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class LiteratureCriteriaModel extends Model
{
    protected $DBGroup          = 'brapci_labs';
    protected $table            = 'literature_criteria';
    protected $primaryKey       = 'id';

    protected $allowedFields = [
        'project_id',
        'type',
        'code',
        'description'
    ];
}
