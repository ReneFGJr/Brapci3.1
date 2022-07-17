</body>
<?php
if (!isset($bg)) {
    $bg = 'bg-primary';
}
?>
<footer class="<?= $bg; ?> text-white text-center text-lg-start" style="margin-top: 120px;">
    <!-- Grid container -->
    <div class="container p-4">
        <!--Grid row-->
        <div class="row">
            <!--Grid column-->
            <div class="col-lg-5 col-md-12 mb-5 mb-md-0">
                <h5 class="text-uppercase">Coleções Brapci</h5>
                <p>
                    A Brapci é um agregador que reune fontes de informação da área de Ciência da Informação,
                    Biblioteconomia, Arquivologia e Museologia. É um base de dados mantida com a colaboração de difersas
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
                        <a href="<?= PATH; ?>/" class="text-white">Brapci Revistas</a>
                    </li>
                    <li>
                        <a href="<?= PATH; ?>/benancib" class="text-white">Benancib</a>
                    </li>
                    <li>
                        <a href="<?= PATH; ?>/proceedings" class="text-white">Brapci Eventos</a>
                    </li>
                    <li>
                        <a href="<?= PATH; ?>/pq" class="text-white">Pesquisadores PQ</a>
                    </li>
                    <li>
                        <a href="<?= PATH; ?>/books" class="text-white">Brapci Livros</a>
                    </li>
                </ul>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-0">Links</h5>

                <ul class="list-unstyled">
                    <li>
                        <a href="<?= PATH; ?>/about" class="text-white">Sobre a Brapci</a>
                    </li>
                    <li>
                        <a href="<?= PATH; ?>/contact" class="text-white">Contato</a>
                    </li>
                    <li>
                        <a href="<?= PATH; ?>/cited" class="text-white">Como citar</a>
                    </li>
                </ul>
            </div>

            <!--Grid column-->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <ul class="list-unstyled">
                    <li>
                        <a href="<?= PATH; ?>/about" class="text-white">Logos</a>
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
    <!-- Copyright -->
</footer>