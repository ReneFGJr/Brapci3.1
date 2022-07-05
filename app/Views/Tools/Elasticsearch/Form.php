<?php
$value = get("search");
$sx = '';
$sx .= form_open();
$sx .= 'Termo de busca';
$sx .= '<div class="input-group mb-3">';
$sx .= form_input(array('name' => 'search', 'class' => "form-control", 'value' => $value));
$sx .= form_submit(array('name' => 'action', 'value' => 'busca'));
$sx .= '</div>';

$sx .= '<div class="input-group mb-3">';
$sx .= '<span>Abrangência de:&nbsp;</span>';
$sx .= '<select name="yeari">';
for($r=1965;$r <= date("Y")+1;$r++)
    {
        $sx .= '<option value="'.$r.'">'.$r.'</option>';
    }
$sx .= '</select>';

$sx .= '<span>&nbsp;Até:&nbsp;</span>';
$sx .= '<select name="yeari">';
for($r=(date("Y")-1);$r >= 1965;$r--)
    {
        $sx .= '<option value="'.$r.'">'.$r.'</option>';
    }
$sx .= '</select>';

/******************************************************* */
$sql = "select * from brapci.source_souce ";
$sx .= '<span>&nbsp;Publicação:&nbsp;</span>';
$sx .= '<select name="yeari">';
for($r=(date("Y")-1);$r >= 1965;$r--)
    {
        $sx .= '<option value="'.$r.'">'.$r.'</option>';
    }
$sx .= '</select>';

/*************************************************/ 
$sx .= '<span>&nbsp;Até:&nbsp;</span>';
$sx .= '<select name="offset">';
for($r=10;$r <= 50;$r=$r+10)
    {
        $sx .= '<option value="'.$r.'">'.$r.'</option>';
    }
$sx .= '</select>';

$sx .= '</div>';

$sx .= form_close();
$sx = bs(bsc($sx, 12));
echo $sx;
