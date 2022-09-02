<?php

namespace App\Models\AI\Chatbot;

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
        'lk_word_9'
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
            if (count($dd) > 1)
                {
                    sort($dd);
                    $r = 0;
                    $dr = array();
                    foreach($dd as $id=>$word)
                        {
                            $dr['lk_word_' . $r] = $id;
                            $this->where('lk_word_'.$r, $word);
                            $r++;
                        }
                    $dt = $this->findAll();
                    echo '<tt><br>'.$this->getlastquery().'</tt>';

                    if (count($dt) == 0)
                        {
                            $this->insert($dr);
                            echo "Inserted<br>";
                        }
                }

        }
}
