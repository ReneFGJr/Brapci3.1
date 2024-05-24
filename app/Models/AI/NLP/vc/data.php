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

        $vc[' maio.'] = ' {date:"maio"} ';
        $vc[' maio '] = ' {date:"maio"} ';
        $vc[' ago.'] = ' {date:"agosto"} ';
        $vc['/ago.'] = ' {date:"agosto"} ';
    }