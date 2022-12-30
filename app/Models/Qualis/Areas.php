<?php

namespace App\Models\Qualis;

use CodeIgniter\Model;

class Areas extends Model
{
    protected $DBGroup          = 'capes';
    protected $table            = 'qualis_area';
    protected $primaryKey       = 'id_qa';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_qa','qa_area', 'updated_at'
    ];

    protected $typeFields    = [
        'hidden', 'string', 'up'
    ];

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

    function index($d1,$d2)
        {
            $sx = 'AREA';
            $this->path = PATH.COLLECTION. '/qualis/area';
            $this->path_back = $this->path;
            switch($d1)
                {
                    case 'import':
                        $this->id = $d2;
                        $sx .= $this->import($d2);
                        break;

                    case 'viewid':
                        $this->id = $d2;
                        $sx .= $this->viewid($d2);
                        break;

                    case 'edit':
                        $this->id = $d2;
                        $sx .= form($this);
                        break;
                    default:
                        $sx = tableview($this);
                }
            return $sx;
        }

        function import($id)
        {
            $Socials = new \App\Models\Socials();
            $Journal = new \App\Models\Qualis\Journal();
            $Qualis = new \App\Models\Qualis\Qualis();
            $adm = $Socials->getAccess("#ADM");
            $dt = $this->find($id);

            $sx = $this->header($dt);

            if ($adm == 1) {
                if ((isset($_FILES['file'])) and (isset($_FILES['file']['tmp_name']))
                    and (get("q_event") != '') and (get("q_area") != ''))
                    {
                        $fileName = $_FILES['file']['tmp_name'];
                        if ($file = fopen($fileName, "r")) {
                            $ln = 0;
                            while (!feof($file)) {
                                $line = fgets($file);
                                if ($ln > 0)
                                {
                                    if (substr($line,4,1) == '-')
                                        {
                                            $line = utf8_encode($line);
                                            $line = trim($line);
                                            $data['j_issn'] = substr($line,0,9);
                                            $data['j_issn_l'] = $data['j_issn'];
                                            $data['q_issn'] = $data['j_issn'];
                                            $data['q_event'] = get("q_event");
                                            $data['q_area'] = get("q_area");

                                            $jnl = ascii(substr($line, 10, strlen($line)));
                                            $jnl = troca($jnl,'""','¢');
                                            $jnl = troca($jnl,'"','');
                                            $jnl = troca($jnl, '¢','"');

                                            if ($pos = strpos($jnl,';'))
                                                {
                                                    $jnl = substr($jnl,0,$pos);
                                                }
                                            $data['j_name'] = $jnl;

                                            /*********************** Estrato */
                                            $qualis = substr($line, strlen($line) - 2, 2);
                                            $qualis = troca($qualis,';','');
                                            $data['q_estrato'] = $qualis;

                                            $Journal->register($data);
                                            $Qualis->register($data);

                                        } else {
                                            if ($ln > 0) { break; }
                                        }

                                }
                                $ln++;
                            }
                            fclose($file);
                        }
                    }
                $sx = form_open_multipart();
                $sx .= form_upload('file');

                $Event = new \App\Models\Qualis\Evento();

                $options = $Event->options();

                $sx .= '<br>'.form_dropdown('q_event', $options, '');
                $sx .= '<br>';

                $sx .= form_hidden('q_area',$id);
                $sx .= form_submit('action',lang('brapci.save'));
                $sx .= form_close();
            }
            return $sx;
        }

        function viewid($id)
            {
                $Socials = new \App\Models\Socials();
                $Qualis = new \App\Models\Qualis\Qualis();
                $adm = $Socials->getAccess("#ADM");
                $dt = $this->find($id);

                $sx = $this->header($dt);

                if ($adm == 1)
                    {
                        $sx .= $this->btn_import($id);
                    }


                $sx .= $Qualis->statistic(1,1);
                $sx .= $Qualis->list(1, 1);
                return $sx;
            }

        function header($dt)
            {
                $sx = h($dt['qa_area'], 1);
                return $sx;
            }
        function btn_import($id)
            {
                $sx = '';
                $sx .= '<a href="'.PATH.COLLECTION. '/qualis/area/import/'.$id.'">';
                $sx .= bsicone('import');
                $sx .= '</a>';
                return $sx;
            }
}
