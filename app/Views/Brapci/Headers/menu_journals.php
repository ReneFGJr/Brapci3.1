<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">

            <?php
            $menu = [];
            $menu[PATH . '/journals'] = lang('brapci.journal_list');
            foreach ($menu as $link => $label) {
                echo '<li class="nav-item active">'.cr();
                echo '<a class="nav-link" href="'.$link.'">'.$label.' </a>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</nav>