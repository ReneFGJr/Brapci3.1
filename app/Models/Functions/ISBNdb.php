<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class ISBNdb extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'isbndbs';
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

    function _call($isbn)
    {
        $apiKey = getenv('ISBNdb');
        $ISBN = new \App\Models\Functions\ISBN();
        $isbn = $ISBN->format($isbn);
        $isbn10 = $ISBN->isbn13to10($isbn);

        $url = "https://api2.isbndb.com/book/$isbn";

        $header = array();
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: ' . $apiKey;

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $erro = curl_errno($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (!$erro) {
            $dt = (array)json_decode($head);
            $dt = (array)$dt['book'];
            $dt = $this->prepare($dt);
            return $dt;
        } else {
            echo 'Curl error: ' . curl_error($ch);
            return array();
        }
    }

    function prepare($dt)
    {
        $Lang = new \App\Models\Functions\Language();
        $dc = array();
        if (isset($dt['title_long'])) {
            $dc['title'] = $dt['title_long'];
        } else {
            $dc['title'] = $dt['title'];
        }

        /****************************** Authors */
        if (isset($dt['authors'])) {
            $dc['authors'] = (array)$dt['authors'];
        } else {
            $dc['authors'] = array();
        }

        /****************************** Date_published */
        if (isset($dt['date_published'])) {
            $dc['published'] = trim($dt['date_published']);
        } else {
            $dc['published'] = '';
        }

        /****************************** Pages */
        if (isset($dt['pages'])) {
            $dc['pages'] = trim($dt['pages']);
        } else {
            $dc['pages'] = '';
        }

        /****************************** Cover */
        if (isset($dt['image'])) {
            $dc['cover'] = trim($dt['image']);
        } else {
            $dc['cover'] = '';
        }

        /****************************** Cover */
        if (isset($dt['language'])) {
            $lang = $Lang->check(trim($dt['language']));
            $dc['lang'] = $lang;
        } else {
            $dc['lang'] = 'pt-BR';
        }


        /****************************** subjects */
        if (isset($dt['subjects'])) {
            $dc['subjects'] = $dt['subjects'];
        } else {
            $dc['subjects'] = array();
        }

        /****************************** dewey_decimal */
        if (isset($dt['dewey_decimal'])) {
            $dc['cdd'] = $dt['dewey_decimal'];
        } else {
            $dc['cdd'] = '';
        }
        $dc['cdu'] = '';

        /****************************** overview */
        if (isset($dt['synopsys'])) {
            $dc['abstract'] = $dt['synopsys'];
        } else {
            $dc['abstract'] = '';
        }

        //$dc['editora'] = '';

        return $dc;
    }
}