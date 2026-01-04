<?php

namespace App\Models\BrapciLabs;

use CodeIgniter\Model;

class LiteratureClassificationCriteriaModel extends Model
{
    protected $DBGroup          = 'brapci_labs';
    protected $table            = 'literature_classification_criteria';
    protected $primaryKey       = 'id';

    protected $allowedFields = [
        'classification_id',
        'criterion_id'
    ];
}
