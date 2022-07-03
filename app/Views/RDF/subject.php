<div class="container">
    <div class="row">
        <div class="col-1">
            <?=lang('brapci.Subject');?>
        </div>
        <div class="col-6">
            <h2><?=$concept['n_name'];?> <sup>(<?=$concept['n_lang'];?>)</sup></h2>
        </div>
    </div>
</div>

<!-- Works-->
<div class="container">
    <div class="row">        
        <?php
        $RDF = new \App\Models\Rdf\RDF();
        for ($r=0;$r < count($data);$r++)
            {
                $line = $data[$r];
                $class = trim($line['c_class']);
                if ($class == 'hasSubject')
                    {
                        echo '<div class="col-12">';
                        echo $RDF->c($line['d_r1']);
                        echo '</div>';
                    }
            }
        ?>
    </div>
</div>