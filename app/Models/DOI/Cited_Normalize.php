<?php

namespace App\Models\DOI;

use CodeIgniter\Model;

class Cited_Normalize extends Model
{
    protected $DBGroup = 'brapci_cited';
    protected $table = 'cited_normalize';
    protected $primaryKey = 'id_ca';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'ca_doi', 'ca_journal', 'ca_year', 'ca_text', 'ca_authors',
        'ca_pages', 'ca_url', 'ca_status', 'ca_locked',
    ];
}
