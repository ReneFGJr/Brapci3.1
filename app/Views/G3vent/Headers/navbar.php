<?php
$Semestre = new \App\Models\Dci\Semestre();
$semestreID = $Semestre->getSemestre('ID');
$semestre = $Semestre->getSemestre('');
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #9009A8">
    <div class="container-fluid">
        <a class="navbar-brand" href="http://cedap/">G3vent</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link" href="<?= base_url('event/pessoas') ?>" >
                        Pessoas
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link" href="<?= base_url('event/events') ?>" >
                        Eventos
                    </a>
                </li>                

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        brapci.DCI
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="http://cedap/dci/cursos">brapci.Encargos.Cursos</a></li>
                        <li><a class="dropdown-item" href="#">brapci.Encargos.Drashboard</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">brapci.Encargos.Drashboard</a></li>
                    </ul>
                </li>
            </ul>
            <li class="nav-item d-flex align-items-center text-white"><?php echo $semestre ?></li>
            <li class="nav-item d-flex align-items-center">
                <a href="<?php echo base_url('/social/login'); ?>" class="nav-link  text-white font-weight-bold px-0">
                    <i class="fa fa-user me-sm-1"></i>
                    <span class="d-sm-inline d-none"><?php echo lang('social.social_sign_in');?></span></a>
            </li>
        </div>
    </div>
</nav>