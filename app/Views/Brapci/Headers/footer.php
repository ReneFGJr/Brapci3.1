</body>
<?php
$Socials = new \App\Models\Socials();
$ADM = $Socials->getAccess("#ADM");
if (!isset($bg)) {
    $bg = 'bg-primary';
}
?>
<footer class="<?= $bg; ?> text-white text-center text-lg-start d-print-none" style="margin-top: 120px;">
    <!-- Grid container -->
    <div class="container-fluid p-4">
        <!--Grid row-->
        <div class="row">
            <!--Grid column-->
            <div class="col-lg-5 col-md-12 mb-5 mb-md-0">
                <h5 class="text-uppercase">Coleções Brapci</h5>
                <p>
                    A Brapci é um agregador que reune fontes de informação da área de Ciência da Informação,
                    Biblioteconomia, Arquivologia e Museologia. É um base de dados mantida com a colaboração de diversas
                    instituições de ensino e pesquisa. Tem como mantenedora a Universidade Federal do Rio Grando do Sul
                    e o Programa de Pós-Graduação em Ciência da Informação (PPGCIN).
                </p>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Bases de Dados</h5>

                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="https://brapci.inf.br/journals" class="text-white">Brapci Revistas</a>
                    </li>
                    <li>
                        <a href="https://brapci.inf.br/benancib" class="text-white">Benancib</a>
                    </li>
                    <li>
                        <a href="https://brapci.inf.br/proceedings" class="text-white">Brapci Eventos</a>
                    </li>
                    <li>
                        <a href="https://brapci.inf.br/pq">Pesquisadores PQ</a>
                    </li>
                    <li>
                        <a href="https://brapci.inf.br/livros" class="text-white">Brapci Livros</a>
                    </li>
                    <li>
                        <a href="https://brapci.inf.br/authority" class="text-white">Autoridades</a>
                    </li>
                    <?php
                    if ($ADM == true) {
                        echo '<li>';
                        echo '<a href="' . PATH . '/admin" class="text-white">Administração</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-0">Links</h5>

                <ul class="list-unstyled">
                    <li>
                        <a href="https://brapci.inf.br/about/brapci" class="text-white">Sobre a Brapci</a>
                    </li>
                    <li>
                        <a href="https://brapci.inf.br/about/contact" class="text-white">Contato</a>
                    </li>
                    <li>
                        <a href="https://brapci.inf.br/about/brapci#cited" class="text-white">Como citar</a>
                    </li>
                </ul>
            </div>

            <!--Grid column-->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <ul class="list-unstyled">
                    <li>
                        <?php
                        /***************** LOGOS */
                        $logo1 = array();
                        $logo2 = array();
                        $logo1['/img/logo/logo_ppgcin_ufrgs_pb.png'] = 'https://ufrgs.br/ppgcin';
                        $logo2['/img/logo/ufrgs_PB_100.png'] = 'https://ufrgs.br/';

                        if ($bg == 'bg-benancib') {
                            $logo1['/img/logo/logo_ppgci_uff_pb.png'] = 'https://ppgci-uff.com.br/';
                            $logo2['/img/logo/logo_uff_pb.png'] = 'https://uff.br/';
                        }

                        if ($bg == 'bg-livros') {
                        }

                        foreach ($logo1 as $img => $url) {
                            echo '<a href="' . $url . '" target="_new">';
                            echo '<img src="' . $img . '" style="height: 50px;" class="m-2">';
                            echo '</a>';
                        }

                        foreach ($logo2 as $img => $url) {
                            echo '<a href="' . $url . '" target="_new">';
                            echo '<img src="' . $img . '" style="height: 50px;" class="m-2">';
                            echo '</a>';
                        }
                        ?>
                    </li>

                </ul>
            </div>
            <!--Grid column-->
        </div>
        <!--Grid row-->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © <?= date("Y"); ?> Copyright:
        <a class="text-white" href="https://brapci.inf.br/">Brapci</a>
    </div>

    <!-- Elfsight Number Counter | Untitled Number Counter -->
    <script src="https://elfsightcdn.com/platform.js" async></script>
    <div class="elfsight-app-707605f6-dbad-43df-a3ca-c6ba18d4eb2c" data-elfsight-app-lazy></div>
    <!-- Copyright -->
</footer>