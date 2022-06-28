<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class AuthotityRDF extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'rdf_concept';
	protected $primaryKey           = 'id_cc';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_cc','cc_use'
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




	function check_method_3($class = "Person") {
        $sql = "SELECT * FROM rdf_concept as R1
        			INNER JOIN rdf_name ON cc_pref_term = id_n
        			INNER JOIN rdf_class ON cc_class = id_c
        			INNER JOIN rdf_data ON R1.id_cc = d_r2
        			where R1.cc_use > 0 and c_class = '$class' 
                    limit 100";

	    $rlt = $this -> db -> query($sql)->getResultArray();
        $sx = '';
        $m = 0;
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $ida = $line['id_cc'];
            $idt = $line['cc_use'];
            $sx .= '<li>' . $line['n_name'].' set '.
                    $line['id_d'].'=>'.
                    $idt.'</li>';
            
            $sql = "update rdf_data set 
            		d_o = " . $line['d_r2'] . ",
            		d_r2 = $idt,
            		d_update = 1 ";
            $sql .= " where d_r2 = ".$line['d_r2'];

            	/* where id_d = " . $line['id_d']; */
            $this -> db -> query($sql);
            $m++;
        }
        if ($m == 0) {
            $sx = msg('rdf.no_changes');
        } else {
            $sx .= metarefresh('#',3);
        }
        return ($sx);
    }	

    function check_method_1($class="Person") {
		$RDF = new \App\Models\Rdf\RDF();
		$f = $RDF->getClass($class);

        $sql = "
		select N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
        N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use         
        FROM rdf_concept as C1
        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
        where C1.cc_class = " . $f . " and C1.cc_use = 0   
        and length(N1.n_name) > 0
        ORDER BY N1.n_name
        limit 200
        ";
        $rlt = $this -> db -> query($sql)->getResultArray();
        
        $n2 = '';
        $n0 = '';
        $i2 = 0;
        $sx = '';
        $m = 0;
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $n0 = trim($line['n_name']);
            $n1 = trim($line['n_name']);
            $n1 = troca($n1, ' de ', ' ');
            $n1 = troca($n1, ' da ', ' ');
            $n1 = troca($n1, ' do ', ' ');
            $n1 = troca($n1, ' dos ', ' ');
            $nf = substr($n1, strlen($n1) - 3, 3);
            if (($nf == ' de') or ($nf == ' da') or ($nf == ' do') or ($nf == ' dos')) {
                $n1 = trim(substr($n1, 0, strlen($n1) - 3));
            }
            $n1 = trim($n1);
            $i1 = $line['id_cc'];
            
            if ($n1 == $n2) {
                $m++;
                $sx .= '<li>' . $n1 . '(' . $i1 . ')';
                $sx .= '--' . $n2 . '(' . $i2 . ')';
                $sx .= ' --> <b><font color="green">Igual</font></b>';
				$sx .= '</li>';
                $sql = "update rdf_concept set cc_use = $i2 where id_cc = $i1";
                $rrr = $this -> db -> query($sql);
            }
            $n2 = $n1;
            $i2 = $i1;
        }
        
        if ($m == 0) {
            $sx = msg('rdf.no_changes',3);
		} else {
            $sx .= metarefresh('#',3);
        }
        return ($sx);
    }
}
