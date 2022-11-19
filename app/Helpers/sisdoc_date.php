<?php
function dif_date($d1,$d2)
    {

    $di = new DateTime($d1);
    $df = new DateTime($d2);

    // Resgata diferença entre as datas
    $dateInterval = $di->diff($df);
    echo $dateInterval->days;
    }

function brtos($dt)
{
    $dt = sonumero($dt);
    $dt = substr($dt, 4, 4) . substr($dt, 2, 2) . substr($dt, 0, 2);
    return $dt;
}

function stodbr($dt)
{
    $dt = sonumero($dt);
    $rst = substr($dt, 6, 2) . '/' . substr($dt, 4, 2) . '/' . substr($dt, 0, 4);
    return $rst;
}

function stodus($dt)
{
    $dt = sonumero($dt);
    $rst = substr($dt, 0, 4) . '-' . substr($dt, 4, 2) . '-' . substr($dt, 6, 2);
    return $rst;
}