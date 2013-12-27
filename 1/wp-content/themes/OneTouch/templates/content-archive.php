<div class="row">

    <?php

    get_template_part('templates/page', 'header_lay');

    if (($data['archive_layout'] == "2c-l-fixed") || ($data['archive_layout'] == "3c-fixed")) {
        get_template_part('templates/sidebar', 'left');
    }
    if (($data['archive_layout'] == "2c-l-fixed") || ($data['archive_layout'] == "2c-r-fixed")) {
        echo '<div id="content" class="eleven columns">';
    } elseif (($data['archive_layout'] == "1col-fixed")) {
        echo '<div id="content" class="fifteen columns">';
    } else {
        echo '<div id="content" class="seven columns">';
    }
        get_template_part('templates/content', '');

    echo '</div>';
    if ($data['archive_layout'] == "3c-r-fixed") {
        get_template_part('templates/sidebar', 'left');
    }
    if (($data['archive_layout'] == "2c-r-fixed") || ($data['archive_layout'] == "3c-fixed") || ($data['archive_layout'] == "3c-r-fixed")) {
        get_template_part('templates/sidebar', 'right');
    } ?>

</div>