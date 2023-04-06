<?php

namespace App\Models\Text-to-speech;

use CodeIgniter\Model;

class Watson extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'watsons';
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


    function sample()
        {
            $txt = 'Bem vindo a Brapci';
            $url = "https://api.us-south.text-to-speech.watson.cloud.ibm.com/instances/3a3110b5-3c48-43cf-8f2c-a72264748f5c";
            $url = '/v1/synthesize?accept=audio%2Fwav&text=hola%20mundo&voice=es-ES_EnriqueV3Voice';
            $apiKey = "2NKo0RjshpGZFvLo5BM5HtdA9RFwXmOOLWgKpAUO9kpH2NK";
            $post = ['apikey' => $api];

            /*********************************************************** */
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);

            // close the connection, release resources used
            curl_close($ch);

            // do anything you want with your response
            var_dump($response);
        }
}