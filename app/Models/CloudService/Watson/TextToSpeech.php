<?php

namespace App\Models\CloudService\Watson;

use CodeIgniter\Model;

class TextToSpeech extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'texttospeeches';
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

    function call($txt)
    {
        $apikey = getenv("watson.apikey");
        $url = getenv("watson.apikey");

        // Watson API credentials
        $username = 'renefgj@gmail.com';
        $password = 'U@$Rvr@ViF_ZHq6';
        $text = 'Olá mundo!';

        $apikey = 'o0RjshpGZFvLo5BM5HtdA9RFwXmOOLWgKpAUO9kpH2NK';
        $url = 'https://api.us-south.text-to-speech.watson.cloud.ibm.com/instances/3a3110b5-3c48-43cf-8f2c-a72264748f5c';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
        );

        $data = array(
                'text' => $text,
                'accept' => 'audio/wav',
                'voice' => 'pt-BR_IsabelaV3Voice'
            );
        $json_data = json_encode($data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);

        /*
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($curl, CURLOPT_USERPWD, "apikey:{$apikey}");
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        */
        $data = curl_exec($curl);
        $erro = curl_errno($curl);
        $file = '../.tmp/a.wav';
        curl_close($curl);
        if ($erro == 0) {
            echo "OK";
            file_put_contents($file, $data);
        } else {
            echo "ERRO CURL: " . $erro;
            echo '<br>' . $url;
        }

        return ($data);
    }
}
