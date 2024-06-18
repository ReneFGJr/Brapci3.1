<?php
$path = $_SERVER['PATH_INFO'];
$pt = explode('/', $path);
echo "<h1>PATH</h1>";
if (isset($pt[1])) {
    switch ($pt[1]) {
        case 'res':
            res($pt);
            break;
        default:
          print_r($pt);
    }
}

function res($pt)
{
    if (isset($pt[2])) {
        switch ($pt[2]) {
            /************************************ DOWNLOAD */
            case 'download':
                if (!isset($pt[3])) {
                  $pt[3] = '0';
                }
                header("Location: https://cip.brapci.inf.br/download/" . $pt[3]);
                die();
                break;
            break;

            /********************************************* */
            case 'v':
                if (isset($pt[3])) {
                    v($pt[3]);
                } else {
                    e404($pt);
                }
                break;
            case 'indice':
              if (!isset($pt[3])) { $pt[3] = 'subject'; }
              if (!isset($pt[4])) { $pt[4] = 'A'; }
              header("Location: https://brapci.inf.br/#/indexs/".$pt[3]."/".$pt[4]);
              die();
              break;
            default:
                e404($pt);
        }
    }
}

function v($id)
{
    header("Location: https://brapci.inf.br/#/v/" . $id);
    die();
}

function e404($pt)
{
    echo "Página não localizada 404<br>";
    print_r($pt);
}
