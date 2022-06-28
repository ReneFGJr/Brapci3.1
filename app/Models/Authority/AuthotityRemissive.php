<?php

namespace App\Models\Authority;

use CodeIgniter\Model;

class AuthotityRemissive extends Model
{
	protected $DBGroup              = 'rdf';
	protected $table                = 'rdf_concept';
	protected $primaryKey           = 'id_cc';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

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

	function remissive_author($id)
	{
		$this->join('rdf_name', 'rdf_name.id_n = rdf_concept.cc_pref_term', 'left');
		$this->where('cc_use', $id);
		$dt = $this->findAll();
		return $dt;
	}

	function remissive($id)
	{
		$sx = '';
		$this->Socials = new \App\Models\Socials();
		$AuthotityRemissive = new \App\Models\Authority\AuthotityRemissive();
		$dt = $AuthotityRemissive->remissive_author($id);
		if (count($dt) > 0) 
		{
			$sx .= '<br>'.lang('rdf.there_are') . ' ' . count($dt) . ' ' . lang('rdf.remissive');
			$sx .= ' '.'<span class="text-primary" onclick="toogle(\'remissive\');" title="'.lang('rdf.see_list').'">'.bsicone('list').'</span>';

			$sx .= '<div id="remissive" style="display: none;" style="width: 90%;">';
			$si = '';
			for ($r = 0; $r < count($dt); $r++) 
			{
				$line = $dt[$r];
				$si .= '<li>' . $line['n_name'];
				if ($this->Socials->getAccess("#ADM")) {
					$link = '<a href="' . URL . MODULE . 'v/' . $line['id_cc'] . '">';
					$link .= $line['id_cc'] . '=>' . $line['cc_use'];
					$link .= '</a>';

					$si .= onclick(PATH . MODULE . 'rdf/set_pref_term/' . $line['cc_use'] . '/' . $line['id_cc'], 400, 100);
					$si .= ' ';
					$si .= '<sup>[set_prefTerm]</sup></span>';
					$si .= ' ';
					$si .= '<sup>' . $link . '</sup>';
				}
				$si .= '</li>';
			}
			if ($si != '') {
				$sx .= '<br><ul class="small">' . $si . '</ul>';
			}
			$sx .= '</div>';
		}
			$sx .= '<script>
				function toogle(id) 
				{ 
					var x = document.getElementById(id);
					if (x.style.display === "none") {
						x.style.display = "block";
					  } else {
						x.style.display = "none";
					  }
				} 
				</script>'.cr();		
		return ($sx);
	}
}
