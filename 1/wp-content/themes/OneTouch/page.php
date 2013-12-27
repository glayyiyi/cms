    <?php

    get_template_part('templates/page', 'header');

    ?>

<div class="row">

    <?php
    if(!is_front_page()) {
        set_layout('pages');
    } else {
        echo '<div id = "content" class = "fifteen columns" ><div class = "row" >';
    }

    get_template_part('templates/content', 'page');

    echo '</div>';

    if(!is_front_page()){
        set_layout('pages', false);
    } else {
        echo '</div></div>';
    }

    ?>

</div>