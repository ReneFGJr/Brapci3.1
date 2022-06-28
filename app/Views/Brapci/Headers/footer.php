</body>
<?php
if (!isset($bg)) { $bg = 'bg-primary'; }
?>
<footer class="<?=$bg;?> text-white text-center text-lg-start" style="margin-top: 120px;">
  <!-- Grid container -->
  <div class="container p-4">
    <!--Grid row-->
    <div class="row">
      <!--Grid column-->
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Coleções Brapci</h5>
        <p>
          A Brapci é um agregador que reune fontes de informação da área de Ciência da Informação, Biblioteconomia, Arquivologia e Museologia. É um base de dados mantida com a colaboração de difersas instituições de ensino e pesquisa. Tem como mantenedora a Universidade Federal do Rio Grando do Sul e ao Programa de Pós-Graduação em Ciência da Informação (PPGCIN).
        </p>
      </div>
      <!--Grid column-->

      <!--Grid column-->
      <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
        <h5 class="text-uppercase">Bases de Dados</h5>

        <ul class="list-unstyled mb-0">
          <li>
            <a href="/" class="text-white">Brapci Revistas</a>
          </li>
          <li>
            <a href="/benancib" class="text-white">Benancib</a>
          </li>
          <li>
            <a href="/proceedings" class="text-white">Brapci Eventos</a>
          </li>          
          <li>
            <a href="/pq" class="text-white">Pesquisadores PQ</a>
          </li>
          <li>
            <a href="/Livros" class="text-white">Brapci Livros</a>
          </li>
        </ul>
      </div>
      <!--Grid column-->

      <!--Grid column-->
      <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
        <h5 class="text-uppercase mb-0">Links</h5>

        <ul class="list-unstyled">
          <li>
            <a href="/about" class="text-white">Sobre a Brapci</a>
          </li>
          <li>
            <a href="/contact" class="text-white">Contato</a>
          </li>
          <li>
            <a href="/cited" class="text-white">Como citar</a>
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