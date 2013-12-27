<script src="<?php echo get_template_directory_uri(); ?>/inc/audiojs/audio.min.js"></script>

<?php

$audio_link = '';
if ( preg_match('/<a (.+?)>/', get_the_content(), $match) ) {
    $link = array();
    foreach ( wp_kses_hair($match[1], array('http')) as $attr) { ?>

        <audio src="<?php echo $attr['value']; ?>" preload="auto" />

    <?php }
    $audio_link = $link['href'];
}
?>





<script>
    audiojs.events.ready(function() {
        var as = audiojs.createAll();
    });
</script>

