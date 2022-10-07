<?php

namespace App\Models\ScientificCommunication;

use CodeIgniter\Model;

class PeerReviewContent extends Model
{
    protected $DBGroup          = 'pgcd';
    protected $table            = 'scientific_opinion_content';
    protected $primaryKey       = 'id_opc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_opc', 'opc_id_op','opc_field', 'opc_content', 'updated_at','opc_pag','opc_comment'
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

    function types()
        {
        $op = array('title', 'introdution', 'search_problem', 'hypothesis', 'goal', 'bibliography', 'method', 'result', 'discussion', 'conclusion', 'reference');
        return $op;
        }

    function edit($d1,$d2)
        {
            echo "edit $d1,$d2";
            $sx = $this->ajax_edit($d2,$d1);
            echo $sx;
            exit;
        }
    function view_register($dt)
        {
            $txt = $dt['opc_content'];
            $txt2 = $dt['opc_comment'];
            $pag = $dt['opc_pag'];
            if (strlen($pag) > 0)
                {
                    $pag .= ' p.';
                }


            $sx = '';
            $sx .= '<hr>';
            $sx .= bsc('<b>'.lang('peer.'.$dt['opc_field']. '</b><br/><i>'. $pag.'</i>'),3);
            $txt = troca($txt,chr(10),'<br>');
            $txt2 = troca($txt2, chr(10), '<br>');
            $sx .= bsc($txt,5,'" style="border-left: 1px solid #000;');
            $sx .= bsc($txt2,5,'" style="border-left: 1px solid #000;');
            $sx = bs($sx);
            return $sx;
        }

    function view($id)
        {
            $sx = '';
            $dt = $this->where('opc_id_op',$id)->findAll();

            for ($r=0;$r < count($dt);$r++)
                {
                    $line = $dt[$r];
                    $sx .= $this->view_register($line);
                }

            $sx .= $this->ajax_new($id);

            return $sx;
        }
    function ajax_new($id)
        {
            global $jsa;
            $sx = '';

            if (!isset($jsa))
                {
                    $jsa = '
                    <script>
                        function field_edit(id,reg)
                            {
                                var url = "'.PATH.COLLECTION. '/opinion/ajax_field/"+id+"/"+reg;
                                $("#field_edit").load(url);
                            }
                    </script>';
                    $sx .= $jsa;
                }

            $sx .= '<a href="#" id="field" onclick="field_edit('.$id.',0)" >';
            $sx .= bsicone('plus');
            $sx .= '</a>';
            $sx .= '<div id="field_edit"></div>';

            return $sx;
        }
    function ajax_save($id,$reg)
        {
            $dt['opc_id_op'] = get("reg");
            $dt['opc_field'] = get("field");
            $dt['opc_content'] = get("text");
            $dt['opc_comment'] = get("text2");
            $dt['opc_pag'] = get("pag");
            $dt['updated_at'] = date("Y-m-f H:i:s");
            if ($id == 0)
                {
                    $this->set($dt)->insert();
                } else {
                    $this->set($dt)->where('id_opc',$id)->update();
                }
            $sx = metarefresh("");
            echo $sx;
            exit;
        }
    function ajax_edit($id,$reg)
        {
            $vlr = '';
            $vlr2 = '';
            $pag = '';
            if ($id > 0)
                {
                    $dt = $this->find($id);
                    $vlr = $dt['opc_content'];
                    $vlr2 = $dt['opc_comment'];
                    $pag = $dt['opc_pag'];
                }
            $sx = '';

            $sx .= '<span class="small">' . lang('peer.field_page') . '</span>';
            $sx .= '<input type="text" id="pag" name="pag" class="form-control" style="width: 100px;">' . $pag . '</input>';
            //$sx .= '<br/>';

            $op = $this->types();
            $sx .= '<span class="small">' . lang('peer.field_name') . '</span>';
            $sx .= '<select id="field_name" name="field_name" class="form-control">';
            $sx .= '<option value="">'. lang('peer.' . 'select_field').'</option>';
            for ($r=0;$r < count($op);$r++)
                {
                    $sx .= '<option value="'.$op[$r].'">'.lang('peer.'.$op[$r]).'</option>';
                }
            $sx .= '</select>';

            $sx .= '<span class="small">'.lang('peer.field_content').'</span>';
            $sx .= '<textarea id="content" name="content" class="form-control" rows=5>'.$vlr.'</textarea>';

            $sx .= '<span class="small">' . lang('peer.field_comment') . '</span>';
            $sx .= '<textarea id="comment" name="comment" class="form-control" rows=5>' . $vlr2 . '</textarea>';

            $sx .= '<a href="#fld'.$id.'" class="btn btn-outline-primary" onclick="field_save('.$id.','.$reg.');">'.lang('peer.save').'</a>';

            $sx .= '<script>
                        function field_save(id,reg)
                            {
                                var url = "' . PATH . COLLECTION . '/opinion/ajax_field_save/"+id+"/"+reg;;
                                var text = $("#content").val();
                                var text2 = $("#comment").val();
                                var pag = $("#pag").val();
                                var field = $("#field_name option:selected").val();

                                data = { text: text, text2:text2, pag: pag, field: field, id: id, reg: reg };
                                $.post(url,data,function(data) {
                                    $("#field_edit").html(data);
                                });
                            }
                    </script>';
            return $sx;
        }
}
