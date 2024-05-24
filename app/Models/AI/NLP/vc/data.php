<?php
$vc = [];
for ($r=1920;$r < date("Y")+10;$r++)
    {
        $vc[' '.$r.' '] = '{date:"'.$r.'"}';
        $vc['(' . $r . ')'] = '({date:"' . $r . '"})';
        $vc[' ' . $r . ')'] = ' {date:"' . $r . '"})';
        $vc['(' . $r . ','] = ' ({date:"' . $r . '"},';
        $vc[' ' . $r . '.'] = ' {date:"' . $r . '"}.';
        $vc[' ' . $r . chr(13)] = ' {date:"' . $r . '"}'.chr(13);


        $mes = ['jan','fev','mar','abr','maio','jun','jul','ago','set','out','nov','dez'];
        $mesN = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        foreach($mes as $m)
            {
                $vc[' '.$m.'.'] = ' {date:"'. $mesN['m'] .'"} ';
                $vc[' '.$m.' '] = ' {date:"'. $mesN['m'] .'"} ';
                $vc['/'.$m.'.'] = ' {date:"'. $mesN['m'] .'"} ';
            }
    }