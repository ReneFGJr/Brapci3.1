<div class="container">
  <div class="row">
    <div class="col-12">
      <ul class="nav " id="pgcdTab" role="tablist">
        <?php
        $FormTab = new \App\Models\PGCD\PlansFormSections();
        $tab = $FormTab->tabs($form_id);

        /* First Sections ***********************************/
        $tabPage = get("pag");
        if ($tabPage == '') { $tabPage = $tab[0]['id_pfs']; }

        /* Show Tabs ****************************************/
        for ($r=0;$r < count($tab);$r++)
        {
          $class = '';
          if ($tab[$r]['id_pfs'] == $tabPage) { $class = 'pgcd_nav-item-active'; }
          $tab_name = lang($tab[$r]['pfs_section_name']);
          echo '<li class="'.$class.'" role="presentation">'.cr();
          echo '<a href="/pgcd/plans/edit/'.$id_p.'?pag='.$tab[$r]['id_pfs'].'" class="nav-link '.$class.'">'.$tab_name.'</a>';
          echo '</li>'.cr();
        }
        ?>
      </ul>
    </div>
  </div>
</div>