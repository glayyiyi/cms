<?php

get_template_part('templates/page', 'header');

?>


<div class="row">

    <?php

    set_layout('archive');

    get_template_part('templates/content', '');

    echo '</div>';

    set_layout('archive', false);  ?>

</div>