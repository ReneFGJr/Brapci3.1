<?php

namespace App\Models\AI\NLP;

use CodeIgniter\Model;

class Levenshtein extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'levenshteins';
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

    function test2()
        {
            $sx = '';
            $sim = similar_text('bafoobar', 'barfoo', $perc);
            $sx .= "similarity: $sim ($perc %)\n";
            $sim = similar_text('barfoo', 'bafoobar', $perc);
            $sx .= "similarity: $sim ($perc %)\n";

            return $sx;
        }

    function test()
    {
        $sx = '';
        $input = 'nanaba';

        // array of words to check against
        $words  = array(
            'apple', 'pineapple', 'banana', 'orange',
            'radish', 'carrot', 'pea', 'bean', 'potato'
        );

        // no shortest distance found, yet
        $shortest = -1;

        // loop through words to find the closest
        foreach ($words as $word) {

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($input, $word);

            // check for an exact match
            if ($lev == 0) {

                // closest word is this one (exact match)
                $closest = $word;
                $shortest = 0;

                // break out of the loop; we've found an exact match
                break;
            }

            // if this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // set the closest match, and shortest distance
                $closest  = $word;
                $shortest = $lev;
            }
        }

        $sx .= "Input word: <b>$input</b>\n";
        if ($shortest == 0) {
            $sx .= "<br>Exact match found: <b>$closest</b>\n";
        } else {
            $sx .= "<br>Did you mean: <b>$closest</b>?\n";
        }
        return $sx;
    }
}
