<?php

namespace App\Models\RDF;

use CodeIgniter\Model;

class RDFOntology extends Model
{
	var $DBGroup              		= 'default';
	protected $table                = PREFIX.'rdf_class';
	protected $primaryKey           = 'id_c';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['c_prefix'];

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

	function index($d1='', $d2='', $d3='', $d4='')
	{
		$RDF = new \App\Models\Rdf\RDF();
		$sx = '';
		if ($d2 != '') {
			$this->join(PREFIX.'rdf_prefix', 'c_prefix = id_prefix', STR_PAD_LEFT);
			$dt = $this->find($d2);
			if ($dt['c_equivalent'] > 0)
				{
					$RDF->RDF_check_equivalent($dt['c_equivalent'],$d2);
					$sx = $this->index($d1,$dt['c_equivalent'],$d3,$d4);
				} else {
					$dt['equivalent'] = $this->where('c_equivalent',$d2)->findAll();
					$sx .= view('Rdf/Setspec/class',$dt);
				}
		} else {
			$this->check();
			$sx .= '';
			$sx .= '
			<hr>
			<h1>Brapci Metadata Terms</h1>
			<hr>
			<table width="100%" border="0" cellpadding="2">
			  <tr>
				  <td width="15%" style="text-align:right"><b>Title:</td>
				  <td>Brapci Metadata Term</td>
			  </tr>
			  <tr>
				  <td width="15%" style="text-align:right"><b>Creator:</td>
				  <td>Rene Faustino Gabriel Junior &lt;renefgj@gmail.com&gt;</td>
			  </tr>
			  <tr>
				  <td width="15%" style="text-align:right"><b>Identifier:</td>
				  <td>brapci</td>
			  </tr>
			  <tr>
				  <td width="15%" style="text-align:right"><b>Date Issued:</td>
				  <td>2021-12-11</td>
			  </tr>
			  <tr>
				  <td width="15%" style="text-align:right"><b>Latest Version:</td>
				  <td>'.URL.'</td>
			  </tr>
			  <tr>
				  <td width="15%" style="text-align:right"><b>Description:</td>
				  <td>under construction</td>
			  </tr>
			  <tr>
				  <td width="15%" style="text-align:right"><b>Title:</td>
				  <td>Brapci Metadata Term</td>
			  </tr>
			</table>
			';
			$sx .= bsc($this->list('C'), 6);
			$sx .= bsc($this->list('P'), 6);
			//$this->publish();
		}
		return $sx;
	}

	function check()
	{
		$this->set('c_prefix', 2)->where('c_prefix', 0)->update();
	}

	function publish()
	{
		//dircheck('setspec');
		$dir = 'setsepc';
		$sx = '';
		$url_rdf = URL . 'setspec/brapci-core-' . date("Ymd") . '.rdf';
		$uri = URL;
		$type = lang('rdf.class_name');
		$sx .= '
		<div class="row">
			<div class="col-md-12" style="background-color: #CCC; border-top: 3px solid #333;">
				<span style="font-size: 150%;">'.'<b>'.$type.': '.$c_class.'</b>'.'</span>
			</div>
			<table width="100%" cellpadding=2>
				<tr>
					<td width="15%" align="right">
						<b>URI:</b>
					</td><td>
						<?php echo $uri;?>
					</td>
				</tr>

				<tr>
					<td width="15%" align="right">
						<b>Prefix:</b>
					</td><td>
						<?php echo lang($prefix_ref);?> (<?php echo anchor($prefix_url);?>)
					</td>
				</tr>

				<tr>
					<td width="15%" align="right">
						<b>Label:</b>
					</td><td>
						<?php echo lang($c_class);?>
					</td>
				</tr>
				';
				if (isset($equivalent))
					{
						$RDF  = new \App\Models\Rdf\RDF();
						echo '<tr>';
						$label = '<b>'.lang('rdf.equivalent').':</b>';
						for ($r=0;$r < count($equivalent);$r++)
							{
								$idc = $equivalent[$r]['id_c'];
								$ln = $RDF->le_class($idc);
								echo '<td width="15%" align="right">'.$label.'</td>';
								if (strlen($label) > 0)
									{
										$label = '';
									}
								echo '<td>'.$RDF->show_class($ln[0]).'</td>';
							}
						echo '</div>';
					}
			$sx .= '</table></div>';
			return $sx;


	}

	function list($type = 'C')
	{
		$RDF = new \App\Models\Rdf\RDF();
		$this->join(PREFIX.'rdf_prefix', 'c_prefix = id_prefix', STR_PAD_LEFT);
		$this->where('c_type', $type)->orderBy('prefix_ref, c_class');
		$dt = $this->findAll();

		$tela1 = '';
		$tela2 = '';

		if ($type == 'C') {
			$tela1 = '<h3>Classes</h3>';
			$pre = 'class';
		} else {
			$tela1 = '<h3>Properties</h3>';
			$pre = 'property';
		}

		$tela1 .= '<ul>';
		$xpre = '';
		for ($r = 0; $r < count($dt); $r++) {
			$ln = $dt[$r];
			$pre = $ln['prefix_ref'];
			if ($pre!= $xpre)
				{
					$tela1 .= '<h4>'.$pre.'</h4>';
					$xpre = $pre;
				}
			$tela1 .= '<li>';
			$tela1 .= '<a href="' . PATH . COLLECTION . ('/ontology/' . $pre . '/' . $ln['id_c']) . '#' . $ln['c_class'] . '">' .
				$RDF->show_class($dt[$r]) .
				'</a>';
			$tela1 .= '</li>';
		}
		$tela1 .= '</ul>';

		$tela = $tela1;

		return $tela;
	}
}
