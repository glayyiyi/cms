<?php
/*
Template Name: Page right sidebar
*/
?>

    <?php

    get_template_part('templates/page', 'header');

    $data['pages_layout'] = "1col-fixed"; ?>


<div class="row">

    <?php if (($data['pages_layout'] == "2c-l-fixed") || ($data['pages_layout'] == "3c-fixed")|| ($data['pages_layout'] == "3c-l-fixed")) {
        get_template_part('templates/sidebar', 'left');
    }
    if ($data['pages_layout'] == "3c-l-fixed") {
        get_template_part('templates/sidebar', 'right');
    }

        echo '<div id="content" class="eleven columns">';

    get_template_part('templates/content', 'page');
    echo '</div>';
    get_template_part('templates/sidebar', 'right');
    ?>

</div>