</body>
<?php
if (!isset($bg)) { $bg = 'bg-primary'; }
?>
<footer class="<?=$bg;?> text-white text-center text-lg-start">
  <!-- Grid container -->
  <div class="container p-4">
    <!--Grid row-->
    <div class="row">
      <!--Grid column-->
      <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
        <h5 class="text-uppercase">Footer Content</h5>

        <p>
          Lorem ipsum dolor sit amet consectetur, adipisicing elit. Iste atque ea quis
          molestias. Fugiat pariatur maxime quis culpa corporis vitae repudiandae aliquam
          voluptatem veniam, est atque cumque eum delectus sint!
        </p>
      </div>
      <!--Grid column-->

      <!--Grid column-->
      <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
        <h5 class="text-uppercase">Links</h5>

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