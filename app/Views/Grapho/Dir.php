<?php
$dir = scandir($path);
$sa = '';
$sb = '';
foreach($dir as $id=>$file)
    {
        if (($file != '.') and ($file != '..') and ($file != 'index.php'))
            {
                $link = '<span class="pointer" onclick="$(\'#'.$file.'\').toggle();">';
                $link .= $file;
                $link .= '</span>' . cr();
                $sa .= '<tt>'.$link.'</tt>';
                $sa .= '<br>' . cr();
                $file_name = $path.'/'.$file;

            }
    }
echo '<table class="full">';

echo $sa;
echo $sb;
echo '</table>';
?>