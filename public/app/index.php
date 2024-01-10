<?php
$path = $_SERVER['PATH_INFO'];
$pt = explode('/', $path);
echo "<h1>PATH</h1>";
if (isset($pt[1])) {
    switch ($pt[1]) {
        case 'res':
            res($pt);
            break;
    }
}

function res($pt)
{
    if (isset($pt[2])) {
        switch ($pt[2]) {
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
}
