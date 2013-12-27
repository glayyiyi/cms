

<?php global $NHP_Options;

    if($NHP_Options->get("footer_color_style") == "light") {}
    else {
        echo '<div id="darkf">';
    } ?>


  <section id="footer" role="contentinfo">

    <div class="row">

    <div class="five columns">
        <?php dynamic_sidebar('sidebar-footer-col1');?>
    </div>

    <div class="five columns">
        <?php dynamic_sidebar('sidebar-footer-col2');?>
    </div>

    <div class="five columns">
        <?php dynamic_sidebar('sidebar-footer-col3');?>
    </div>
  </div>

  <div class="row dop-row">
    <?php if($NHP_Options->get("footer_display") != "copyright") { ?>
    <div class="five columns">

      <section class="footer-logo">
        <a href="<?php echo home_url(); ?>/"><img src="<?php $NHP_Options->show("custom_footer_logo_upload"); ?>" alt="<?php bloginfo('name'); ?>"></a>
      </section>

    </div>


    <div class="five columns">

      <section class="widget widget_info">
        <div class="info-widget">
          <div class="subtitle"><?php echo $NHP_Options->get("footer_subtitle_description_text") ?></div>
          <h3><?php echo $NHP_Options->get("footer_title_description_text") ?></h3>

          <p><?php echo $NHP_Options->get("footer_description_text") ?></p>

        </div>
      </section>

    </div>

    <div class="five columns">

      <section class="adress-icon">
        <div data-picture data-alt="adress-icon" class="inline-icon">
          <div data-src="<?php echo get_template_directory_uri(); ?>/assets/icons/map_w.png"></div>
          <div data-src="<?php echo get_template_directory_uri(); ?>/assets/icons/map_w@2x.png" data-media="(min-width: 400px) and (min-device-pixel-ratio: 1.5)"></div>

          <!--[if (lt IE 9) & (!IEMobile)]>
            <div data-src="<?php echo get_template_directory_uri(); ?>/assets/icons/map_w.png"></div>
          <![endif]-->

          <!-- Fallback content for non-JS browsers. Same img src as the initial, unqualified source element. -->
          <noscript>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/icons/map_w.png" alt="adress-icon" class="adress-icon">
          </noscript>
        </div>


        <p><?php echo $NHP_Options->get("footer_contacts_text") ?></p>
      </section>

    </div>
  <?php
  }
    echo '<div class="five columns">'.$NHP_Options->get("copyright_footer").'</div>';

    if ( $NHP_Options->get("menu_in_footer") =='1'){

      echo '<div class="ten columns"><nav>';
        wp_nav_menu(array('menu' => $NHP_Options->get("footer_menu_select"), 'menu_class' => 'footer-menu'));
      echo'</nav></div>';

    }

  ?>
  </div>
  </section>

<?php if($NHP_Options->get("footer_color_style") == "light") {}
else {
    echo '</div>';
} ?>


<a href="#" id="linkTop" class="backtotop">
  <span></span>
</a>

<script type="text/javascript">
<?php
    $NHP_Options->show("google_analytics");
?>
</script>

