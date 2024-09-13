<?php

function form_simple($extra='')
    {
        $text = get("text");
        $test = !empty(get("test"));
        $sx = '';
        $sx .= form_open();
        $sx .= form_textarea(array('name'=>'text','class'=>'form-control-sm','value'=>$text,'style'=>'width: 100%;'));
        $sx .= form_checkbox(array('name'=>'test','value'=>1,'checked'=> $test)). ' Test';
        $sx .= $extra;
        $sx .= '<br>'.form_submit(array('name'=>'action','value'=>lang('brapci.send')));
        $sx .= form_close();
        return $sx;
    }
function show_array($ar)
{
    $sx = '<table class="table">';
    $sx .= '<tr>';
    $sx .= '<th>' . lang('ai.ID') . '</th>';
    $sx .= '<th>' . lang('ai.HEAD') . '</th>';
    $sx .= '</tr>';
    foreach ($ar as $k => $v) {
        $sx .= '<tr>';
        $sx .= '<td>';
        $sx .= $k;
        $sx .= '</td>';
        $sx .= '<td>';
        $sx .= $v;
        $sx .= '</td>';
        $sx .= '</tr>';
    }
    $sx .= '</table>';
    return $sx;
}
function strzero($id, $z)
{
    $txt = str_pad($id, $z, '0', STR_PAD_LEFT);
    return $txt;
}

function version()
{
    $v = 'v0.' . date("y.m.d");
    return ($v);
}
function tableview($th, $dt = array())
{
    $url = $th->path;

    /********** Campos do formulário */
    $fl = $th->allowedFields;
    if (isset($th->viewFields)) {
        $fld = implode(",", $th->viewFields);
        $th->select($fld);
        $fl = $th->viewFields;
    }

    if (isset($_POST['action'])) {
        $search = $_POST["search"];
        $search_field = $_POST["search_field"];
        $th->like($fl[1], $search);
        $_SESSION['srch_'] = $search;
        $_SESSION['srch_tp'] = $search_field;
    } else {
        //
        $search = '';
        $search_field = 0;
        if (isset($_SESSION['srch_'])) {
            $search = $_SESSION['srch_'];
            $search_field = $_SESSION['srch_tp'];
        }
        if (strlen($search) > 0) {
            $th->like($fl[$search_field], $search);
        }
    }
    if ($fl[$search_field] == 0) {
        $search_field = 1;
    }
    $th->orderBy($fl[$search_field]);


    $v = $th->paginate(15);
    $p = $th->pager;

    /**************************************************************** TABLE NAME */
    $sx = bsc('<h1>' . lang($th->table) . '</h1>', 12);

    $st = '<table width="100%" class="table">';
    $st .= '<tr><td>';
    $st .= form_open();
    $st .= '</td><td>';
    $st .= '<select name="search_field" class="form-control-sm">' . cr();
    for ($r = 1; $r < count($fl); $r++) {
        $sel = '';
        if ($r == $search_field) {
            $sel = 'selected';
        }
        $st .= '<option value="' . $r . '" ' . $sel . '>' . msg($fl[$r]) . '</option>' . cr();
    }
    $st .= '</select>' . cr();
    $st .= '</td><td>';
    $st .= '<input type="text" class="form-control-sm" name="search" value="' . $search . '">';
    $st .= '</td><td>';
    $st .= '<input type="submit" class="btn btn-primary" name="action" value="' . lang('sisdoc.filter') . '">';
    $st .= form_close();
    $st .= '</td><td align="right">';
    $st .=  $th->pager->links();
    $st .= '</td><td align="right">';
    $st .= $th->pager->GetTotal();
    $st .= '/' . $th->countAllResults();
    $st .= '/' . $th->pager->getPageCount();
    $st .= '</td>';

    /*********** NEW */
    $st .= '<td align="right">';
    $st .= anchor($url . '/edit/', lang('sisdoc.new'), 'class="btn btn-primary"');
    $st .= '</td></tr>';
    $st .= '</table>';

    $sx .= bs($st, 12);

    $sx .= '<table class="table sisdoc_table">';

    /* Header */
    $heads = $th->allowedFields;
    $sx .= '<tr>';
    $sx .= '<th class="sisdoc_th">#</th>';
    for ($h = 1; $h < count($heads); $h++) {
        if (strpos($fl[0], '#')) {
            $sx .= '<th class="sisdoc_th">' . lang($heads[$h]) . '</th>';
        }
    }
    $sx .= '</tr>' . cr();

    /* Data */
    for ($r = 0; $r < count($v); $r++) {
        $line = $v[$r];
        $sx .= '<tr class="sisdoc_tr">';
        foreach ($fl as $field) {
            $vlr = $line[$field];
            if (strlen($vlr) == 0) {
                $vlr = ' ';
            }
            /********************************************** VIEWID */
            $sx .= '<td class="sisdoc_td">';
            $sx .= anchor(($url . '/viewid/' . $line[$fl[0]]), $vlr);
            $sx .= '</td>';
        }
        /* Botoes */
        $sx .= '<td><nobr>';
        $sx .= btn_edit($url . '/edit/' . $line[$fl[0]]);
        $sx .= '&nbsp;';
        $sx .= btn_trash($url . '/delete/' . $line[$fl[0]]);
        $sx .= '</nobr>';
        $sx .= '</td>';

        $sx .= '</tr>' . cr();
    }
    $sx .= '</table>';
    $sx .=  $th->pager->links();
    $sx .= bsdivclose();
    $sx .= bsdivclose();
    $sx .= bsdivclose();
    return ($sx);
}
function user_id()
{
    if (isset($_SESSION['id'])) {
        $user = $_SESSION["id"];
        if (strlen($user) > 0) {
            return ($user);
        }
    }
    return (0);
}

function tableview2($rows)
    {
        $sx = '
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    table, th, td {
                        border: 1px solid black;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                    }
                </style>';

        $sx .= '
            <h1>Table with Filters</h1>

            <!-- Filtros -->
            <label for="filterName">Nome:</label>
            <input type="text" id="filterName" placeholder="Filtrar por nome">

            <!-- Tabela -->
            <table id="myTable">
                <tbody>
            ';

            $data = $rows[0];
            $fields = [];
            $sx .= ' <thead><tr>';
            foreach ($data as $key => $value) {
                array_push($fields,$key);
                $sx .= '<th>'.$key.'</th>';
            }
            $sx .= '</tr></thead>';

            foreach($rows as $id=>$row)
                {
                    $sx .= '<tr>';
                    foreach ($data as $key => $value) {
                        $sx .= '<td>'.htmlspecialchars($row[$key]).'</td>'.cr();
                    }
                    $sx .= '</tr>';
                }
                $sx .= '</tbody></table>';
        $sx .= '
            <script>
            $(document).ready(function(){
                // Função para filtrar a tabela com base nos valores dos inputs
                function filterTable() {
                    let nameFilter = $(\'#filterName\').val().toLowerCase();

                    $(\'#myTable tbody tr\').each(function() {
                        let name = $(this).find(\'td:nth-child(2)\').text().toLowerCase();

                        // Exibe ou esconde a linha com base nos filtros
                        if (name.includes(nameFilter)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }

                // Chama a função de filtragem quando os inputs mudarem
                $(\'#filterName, #filterCity\').on(\'keyup\', function() {
                    filterTable();
                });
            });
            </script>';

            return $sx;
    }