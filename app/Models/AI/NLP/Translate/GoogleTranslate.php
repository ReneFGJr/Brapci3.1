<?php

namespace App\Models\AI\NLP\Translate;

use CodeIgniter\Model;

class GoogleTranslate extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'googletranslates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    //https://cloud.google.com/translate/docs/reference/rest/?apix=true

    function translate($txt,$ori,$target)
        {
            $url = 'https://translation.googleapis.com/language/translate/v2';
            $url .= '?q='.html_entity_decode($txt);
            $url .= '&target='.$target;
            $url .= '&source='.$ori;
            $url .= '&key='.$_ENV['google_apikey_translate'];



        }
}
