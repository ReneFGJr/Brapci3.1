<?php

namespace App\Models\Api\Lattes;

use CodeIgniter\Model;

class KtoN extends Model
{
    protected $DBGroup          = 'lattes';
    protected $table            = 'k_to_n';
    protected $primaryKey       = 'id_kn';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kn ', 'kn_idk', 'kn_idn', 'kn_status', 'updated_at'
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

    // https://buscatextual.cnpq.br/buscatextual/visualizacv.do?id=K4518324E6
    // https://buscatextual.cnpq.br/buscatextual/visualizacv.do?id=K9336847Z6

    function dir_update($id)
        {
            $dt = $this->find($id);
            $n = trim($dt['kn_idk']);
            $id16 = trim($dt['kn_idn']);

            $dir = substr($n, 0, 2) . '/' . substr($n, 2, 3) . '/' . substr($n, 5, 3) . '/';
            $dir = getenv("app.lattes.apoio").$dir;

            $lattes = new \App\Models\Api\Lattes\Index();
            $check = $lattes->checkID($id16);

            if ($lattes->checkID($id16))
                {
                    $txt = $dt['kn_idn'] . ',' . $dt['kn_idk'];
                    dircheck($dir);
                    $filename = $dt['kn_idk'].'.txt';
                    file_put_contents($dir.$filename,$txt);
                } else {
                    echo "Erro: Update DIR Lattes IDK $id16";
                    exit;
                }
        }

    function list($status)
        {
            $sb = '';
            $linka = '</a>';
            $id=get("id");
            if ($id != '')
                {
                    $dta = $this->find($id);
                    $check = 0;
                    $erro = bsmessage('ID Incorreto ' . $dta['kn_idk'],3);
                    $idk = get("kn_idn");
                    if ($idk != '')
                        {
                            $lattes = new \App\Models\Api\Lattes\Index();
                            $check = $lattes->checkID($idk);
                            $erro = '';
                        }
                    /********************************** CHECK */
                    if ($check == 1)
                        {
                            $this->set($_POST)->where('id_kn',$id)->update();
                            $this->dir_update($id);
                            $sb = bsmessage('Atualizado',1);
                            $sb .= metarefresh(PATH. 'admin/lattes/kton/1',1);
                        } else {
                            $link = '<a target="_blank" href="https://buscatextual.cnpq.br/buscatextual/visualizacv.do?id=' . $dta['kn_idk'] . '">';
                            $link .= $dta['kn_idk'];
                            $link .= $linka;
                            $sb .= 'Visualizar o Lattes: '.$link;

                            $sb .= form_open();
                            $sb .= form_label('Informe o ID do Lattes');
                            $sb .= '<br>';
                            $sb .= form_input('kn_idn','');
                            $sb .= form_hidden('kn_status', '2');
                            $sb .= form_hidden('update_at', date("Y-m-d H:i:s"));
                            $sb .= form_submit('action', lang('brapci.save'));
                            $sb .= form_close();
                        }
                }


            $dt = $this
                ->where('kn_status',$status)
                ->findAll();
            $sx = '';
            $sa = '<table class="table2" style="font-size: 0.8em;">';
            $sa .= '<tr><th>IDK</th></tr>';
            foreach($dt as $id=>$line)
            {
                $link = '<a href="'.PATH. 'admin/lattes/kton/1/?id='.$line['id_kn'].'">';
                $sa .= '<tr>';
                $sa .= '<td>'.$link.$line['kn_idk'].$linka.'</td>';
                $sa .= '</tr>';
            }
            $sa .= '</table>';

            $sx = bs(bsc($sa,6).bsc($sb,6));
            return $sx;
        }

    function resume()
        {
            $sx = '';
            $dt = $this
                ->select('count(*) as total, kn_status')
                ->where('kn_status',1)
                ->groupBy('kn_status')
                ->orderBy('total desc')
                ->findAll();
            $sx = '<table width="100%" class="table" style="font-size: 0.7em;>';
            $sx .= '<tr><th colspan=2"><th>'.lang('brapci.lattes_kton').'</th></tr>';
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH.'admin/lattes/kton/'.$line['kn_status'].'">';
                    $linka = '</a>';
                    $sx .= '<tr>';
                    $sx .= '<td width="80%">';
                    $sx .= $link.lang('brapci.lattes_kton_'.$line['kn_status']).$linka;
                    $sx .= '</td>';

                    $sx .= '<td width="20%" class="text-end">';
                    $sx .= $line['total'];
                    $sx .= '</td>';

                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            return $sx;
        }

    function convert_KtoN($n)
    {
        $n = trim($n);
        $rsp = array();
        if (substr($n, 0, 1) != 'K') {
            $rsp['erro'] = '400';
            $rsp['message'] = 'Código ' . $n . ' é inválido';
            echo json_encode($rsp);
            exit;
        }

        $dir = substr($n, 0, 2) . '/' . substr($n, 2, 3) . '/' . substr($n, 5, 3) . '/';
        $path = getenv('app.lattes.apoio');
        $filename = $path . $dir . $n . '.txt';

        if (!file_exists($filename)) {
            $rsp['status'] = '403';
            $rsp['message'] = 'Código ' . $n . ' é não localizado';
            $this->register($n);
            echo json_encode($rsp);
            exit;
        } else {
            $txt = file_get_contents($filename);
            $t = explode(',', $txt);
            $rsp['status'] = '200';
            $rsp['lattes_id'] = $t[0];
            echo json_encode($rsp);
            exit;
        }
        exit;
    }

    function register($k)
        {
            $dt = $this->where('kn_idk',$k)->findAll();
            if (count($dt) == 0)
                {
                    $data['kn_idk'] = $k;
                    $data['kn_idn'] = '';
                    $data['kn_status'] = 1;
                    $this->set($data)->insert();
                }
        }
}
