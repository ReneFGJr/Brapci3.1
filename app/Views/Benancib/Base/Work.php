<div class="container">
    <div class="row">
        <div class="col-2">
            <?=view("Benancib/Base/Header");?>
        </div>    
        <div class="col-10">
            <?=view("Benancib/Base/Header_proceeding");?>
            
        </div>    
    </div>
    <div class="row">
        <div class="col-12">
            <?php
            /******************************** TITULO */
            $H = 1;
            foreach ($Title as $idioma => $titulo) {
                echo '<h'.$H.'>' . $titulo . '</h'.$H.'>';
                $H++;
            }
            ?>
        </div>    
        <!------------------------ Authors ---------------------->
        <div class="col-10">
            <div class="text-end" id="authors">
            <?php
            $id = 0;
            foreach ($authors as $id=>$author)
                {
                    if ($id > 0) { echo '; '; }
                    echo $author;
                    $id++;
                }
            echo '.';
            ?>
            </div>

            <?php
            /******************************** ABSTRACT */
            foreach ($Abstract as $idioma => $abstract) {
                echo '<b>'.lang('brapci.abstract_'.$idioma).'</b>';
                echo '<div style="text-align: justify;" id="abstract_'.$idioma.'>'.$abstract.'</div>';

                if (isset($keywords[$idioma]))
                    {     
                        echo '<b>'.lang('brapci.keywords_'.$idioma).'</b>: ';
                        $keys = '';
                        foreach($keywords[$idioma] as $id=>$keyword)
                            {
                                $keys .= trim($keyword).'. ';
                            }
                        echo $keys;
                        echo '<br><br>';
                    }
            }
            ?>
        </div> 

        <div class="col-2">         
            <?php
            $data['pdf'] = $PDF;
            echo view('Benancib/Base/PDF',$data);
            ?>
        </div>
</div>
</div>