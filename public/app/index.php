HELLO 2
<pre>
<?php
echo "<h1>POST</h1>";
print_r($_POST);
echo "<h1>GET</h1>";
print_r($_GET);
echo "<h1>SERVER</h1>";
print_r($_SERVER);

$path = $_SERVER['PATH_INFO'];
$pt = explode('/',$path);
echo "<h1>PATH</h1>";
if (isset($pt[1]))
{
    switch($pt[1])
        {
            case 'res':
                res($pt);
                break;
        }
}

function res($pt)
    {
        if (isset($pt[2]))
            {
                switch($pt[2])
                    {
                        case 'v':
                            if (isset($pt[3]))
                                {
                                    v($pt[3]);
                                } else {
                                    e404();
                                }
                        default:
                            e404();
                    }
            }
    }

function v($id)
    {
        echo "===========".$id;
    }

function e404()
    {
        echo "Página não localizada 404";
    }
?>
</pre>