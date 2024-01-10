<?php
$path = $_SERVER['PATH_INFO'];
$pt = explode('/', $path);

if (isset($pt[1])) {
    switch ($pt[1]) {
        case 'res':
            res($pt);
            break;
        default:
          echo "<h1>PATH</h1>";
          pre($pt, false);
    }
}

function indice($pt)
{
  if (!isset($pt[2]))
    {
      $pt[2] = 'subject';
    }
  if (!isset($pt[3]))
    {
      $pt[3] = 'A';
    }
  header("Location: https://brapci.inf.br/#/indexs/".$pt[2]."/" . $pt[3]);
  die();
}

function res($pt)
{
    if (isset($pt[2])) {
        switch ($pt[2]) {
            case 'indice':
              indice($pt);
              break;
            case 'v':
                if (isset($pt[3])) {
                    v($pt[3]);
                } else {
                    e404();
                }
                break;
            default:
                e404();
        }
    }
}

function v($id)
{
    header("Location: https://brapci.inf.br/#/v/" . $id);
    die();
}

function e404()
{
    echo "Página não localizada 404";
    pre($_SERVER['PATH_INFO']);
}
