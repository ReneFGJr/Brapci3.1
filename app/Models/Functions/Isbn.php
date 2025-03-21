<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class Isbn extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'isbns';
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

    /************* ISBNS ***********************************/
    function check($isbn)
        {
            $isbn = sonumero($isbn);
            $isbn = $this->isbns($isbn);
            $check2 = $this->genchksum13(substr($isbn['isbn13'],0,12));
            $check1 = substr($isbn['isbn13'],12,1);

            return ($check1 == $check2);
        }

    function isbns($isbn)
    {
        if (is_array($isbn)) {
            $isbn = $isbn['isbn13'];
        }
        $isbn = troca($isbn, '-', '');
        $isbn = troca($isbn, '.', '');
        $isbn = sonumero($isbn);
        $isbn = trim($isbn);

        if (substr($isbn, 0, 2) == '97') {
            $isbn = substr($isbn, 0, 13);
        }
        if (substr($isbn, 0, 2) == '85') {
            $isbn = substr($isbn, 0, 10);
        }
        $rsp = array();

        if (strlen($isbn) == 13) {
            $rsp['isbn13'] = $isbn;
            $rsp['isbn10'] = $this->isbn13to10($isbn);
        } else {
            $rsp['isbn10'] = $isbn;
            $rsp['isbn13'] = $this->isbn10to13($isbn);
        }

        $rsp['isbn10f'] = substr($rsp['isbn10'], 0, 2) . '-' . substr($rsp['isbn10'], 2, 5) . '-' . substr($rsp['isbn10'], 7, 2) . '-' . substr($rsp['isbn10'], 9, 1);
        $rsp['isbn13f'] = substr($rsp['isbn13'], 0, 3) . '-' . substr($rsp['isbn13'], 3, 4) . '-' . substr($rsp['isbn13'], 7, 5) . '-' . substr($rsp['isbn13'], 12, 1);
        return ($rsp);
    }

    function format($isbn)
    {
        $isbn = strtoupper($isbn);
        $s = sonumero($isbn);
        if (substr($isbn, strlen($isbn) - 1, 1) == 'X') {
            $s .= 'X';
        }
        if (strlen($isbn) != 13) {
            $s = $this->isbn10to13($isbn);
        }
        return ($s);
    }

    function isbn10to13($isbn)
    {
        $isbn = trim(sonumero($isbn));
        if (strlen($isbn) == 12) { // if number is UPC just add zero
            $isbn13 = '0' . $isbn;
        } else {
            $isbn2 = sonumero(substr("978" . trim($isbn), 0, -1));
            $sum13 = $this->genchksum13($isbn2);
            $isbn13 = "$isbn2$sum13";
        }
        return ($isbn13);
    }

    function isbn13to10($isbn)
    {
        if (preg_match('/^\d{3}(\d{9})\d$/', $isbn, $m)) {
            $sequence = $m[1];
            $sum = 0;
            $mul = 10;
            for ($i = 0; $i < 9; $i++) {
                $sum = $sum + ($mul * (int)$sequence[$i]);
                $mul--;
            }
            $mod = 11 - ($sum % 11);
            if ($mod == 10) {
                $mod = "X";
            } else if ($mod == 11) {
                $mod = 0;
            }
            $isbn = $sequence . $mod;
        }
        return $isbn;
    }

    function genchksum13($isbn)
    {
        $isbn = sonumero($isbn);
        if (substr($isbn,0,2) != '97')
            {
                return 'X';
            }
        $isbn = trim($isbn);
        $tb = 0;
        for ($i = 0; $i <= strlen($isbn); $i++) {
            $tc = substr($isbn, -1, 1);
            $isbn = substr($isbn, 0, -1);
            $ta = ($tc * 3);
            $tci = substr($isbn, -1, 1);
            $isbn = substr($isbn, 0, -1);
            $tb = $tb + $ta + $tci;
        }

        $tg = ($tb / 10);
        $tint = intval($tg);
        if ($tint == $tg) {
            return 0;
        }
        $ts = substr($tg, -1, 1);
        $tsum = (10 - $ts);
        return $tsum;
    }
}