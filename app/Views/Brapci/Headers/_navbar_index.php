<?php
$dir = '../.tmp/indexes';
if (is_dir($dir))
{
$indexs = scandir($dir);
$ind = [];
foreach($indexs as $idx=>$pag)
    {
        if (($pag != '.') and ($pag != '..') and ($pag != 'index.php'))
            {
                $ind[$pag] = $dir.'/'.$pag;
            }
    }
?>
        <li class="nav-item dropdown">
            <a class="nav-link-brp dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?=lang('brapci.Indexes');?>
            </a>
            <ul class="dropdown-menu">
                <?php
                    foreach($ind as $index=>$page)
                        {
                            echo '<li><a class="dropdown-item" href="'.PATH.'/indexes/'.$index.'">' . lang('brapci.index_' . $index) . '</a></li>';
                        }

                ?>

            </ul>
        </li>
<?php
}
?>