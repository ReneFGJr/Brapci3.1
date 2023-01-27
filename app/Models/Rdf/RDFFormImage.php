<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfFormImage extends Model
{
	protected $DBGroup              = 'rdf';
	protected $table                = '*';
	protected $primaryKey           = 'id';
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

	function update_proprieties($id)
		{

		}

	function imageTumbnail($filename)
		{
		$filenameD = troca($filename,'image.','tumb.');
		if ($filenameD != $filename)
			{

			$FileInfo = pathinfo($filename);
			$ext = $FileInfo['extension'];

			// Get new sizes
			list($width, $height) = getimagesize($filename);

			$percent = 200/$height;

			$newwidth = round($width * $percent);
			$newheight = round($height * $percent);

			$thumb = imagecreatetruecolor($newwidth, $newheight);

			switch($ext)
				{
					case 'png':
					$source = imagecreatefrompng($filename);
					break;

					case 'jpg':
					$source = imagecreatefromjpeg($filename);
					break;

					default:
					echo "OPS - FILES NOT CONVERTED - ".$ext;
					exit;
				}


			// Resize
			imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

			// Output
			imagejpeg($thumb, $filenameD);
			} else {
				echo "Formato inválido para TumbNail";
				exit;
			}


		}

	function edit($id,$prop='',$idf=0,$idc=0)
		{
			$sx = '';
			$RDFLiteral = new \App\Models\Rdf\RDFLiteral();

			/************************* SALVA REGISTRO */
			$action = get("action");
			if ($action != '')
				{
					$texto = get("descript");
					$data = array('n_lang'=>get("lang"),'n_name'=>$texto);
					if ($id > 0)
						{
							/******************************* ATUALIZA */
							//$RDFLiteral->atualiza($data,$id);

						} else {
							/******************************* NOVA ENTRADA */
							if (isset($_FILES['image']))
								{
									$tmp = $_FILES['image']['tmp_name'];
									$file = $_FILES['image']['name'];
									$file_name = $_FILES['image']['name'];
									$name = md5(file_get_contents($tmp));
									$RDF = new \App\Models\Rdf\RDF();

									$ext = explode('.',$file);
									$ext = strtolower(trim($ext[count($ext)-1]));

									$content_type = trim($_FILES['image']['type']);

									switch ($content_type)
										{
											case 'image/png':
											break;

											case 'image/jpeg':
											$ext = 'jpg';
											break;

											case 'image/webp':
											$ext = 'jpg';
											break;

											default:
											echo lang('rdf.image_format_invalid - '.$content_type);
											exit;
										}

									/************************ Move Files */
									if (file_exists($tmp) and (filesize($tmp) > 0))
									{
										$id_img = $RDF->concept($name,'Image');
										$dir = $RDF->directory($id_img);
										$dir = troca($dir,'.c/','img/c/');

										$dirc = explode('/',$dir);
										$dir = '';
										for ($r=0;$r < count($dirc);$r++)
											{
												if ($dir != '') { $dir .= '/'; }
												$dir .= $dirc[$r];
												dircheck($dir);
											}
										$file = $dir . 'image.'.$ext;
										$file_name_tumb = $dir . 'tumb.' . $ext;
										move_uploaded_file($tmp,$file);

										/******************** TumbNail */
										$this->imageTumbnail($file);

										$xprop = 'hasFilename';
										$resource = '';
										$literal = $RDFLiteral->name($file_name, 'en');
										$RDF->RDP_property($id_img, $xprop, $resource, $literal);

										$xprop = 'hasTumbNail';
										$resource = '';
										$literal = $RDFLiteral->name($file_name_tumb, 'en');
										$RDF->RDP_property($id_img, $xprop, $resource, $literal);

										$xprop = 'hasFileDirectory';
										$resource = '';
										$literal = $RDFLiteral->name($dir, 'en');
										$RDF->RDP_property($id_img, $xprop, $resource, $literal);

										//$img_prop = getimagesize($file);

										$xprop = 'hasContentType';
										$resource = $RDF->concept($content_type, 'ContentType');
										$literal = 0;
										$RDF->RDP_property($id_img, $xprop, $resource, $literal);

										/******************************** Vincula a ID Resource */
										$RDF->RDP_property($idc, $prop, $id_img, 0);

									}
								} else {
									echo "Erro na carga do Arquivo";
									exit;
								}
						}
					return wclose();
				} else {

					/************************** Form */
					if ($id > 0)
					{
						$dt = $RDFLiteral->le($id);
						$texto = $dt['n_name'];
						$path = PATH.'/rdf/text/'.$id;
					} else {
						$texto = get("descript");
						$path = PATH.MODULE.'/rdf/form/edit/'.$prop.'/'.$idf.'/'.$idc;
					}
				}
			$sx .= h('rdf.uploadImage');
			$sx .= form_open_multipart();
			$sx .= form_upload(array('name'=>'image'));
			$sx .= form_submit(array('name' => 'action', 'value' => lang('rdf.upload')));
			$sx .= form_close();
			$sx = bs(bsc($sx,12));
			return $sx;
		}
	function form_edit($path,$texto,$lang='pt-BR')
		{
			$txt = '';
			$txt = form_open($path);
			$txt .= '<span class="supersmall">'.lang('rdf.textarea').'</span>';
			$txt .= '<textarea id="descript" name="descript" style="width: 100%; height: 100px;" class="form-control">'.$texto.'</textarea>';
			$txt .= form_submit('action', lang('rdf.save'), 'class="btn btn-primary supersmall m-3"');
			$txt .= form_close();
			return $txt;
		}
}
