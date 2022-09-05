<?php

namespace App\Models\AI\Skos;

use CodeIgniter\Model;

class VClinks extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'brapci_chatbot.vc_link';
    protected $primaryKey           = 'id_lk';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [
        'lk_word_0', 'lk_word_1', 'lk_word_2',
        'lk_word_3', 'lk_word_4', 'lk_word_5',
        'lk_word_6', 'lk_word_7', 'lk_word_8',
        'lk_word_9',
        'lk_word_10', 'lk_word_11', 'lk_word_12',
        'lk_word_13', 'lk_word_14', 'lk_word_15',
        'lk_word_16',
        'lk_skos', 'lk_uri'
    ];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    function link($dd)
        {
            if (!isset($dd['lk_skos']))
                {
                    echo "OPS lk_skos not found<br>";
                    exit;
                }
            for ($r=0;$r < count($dd);$r++)
            {
                if (isset($dd['lk_word_'.$r]))
                    {
                        $vlr = round($dd['lk_word_' . $r]);
                        if ($vlr > 0)
                            {
                                $this->where('lk_word_' . $r, $vlr);
                            }
                    }
            }
            $dt = $this->find();

            if (count($dt) == 0)
                {
                    $this->insert($dd);
                }
        }
}
