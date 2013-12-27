<?php

global $NHP_Options;

function parse_typo($data, $color = ""){
    $result = array();

    if ($color != '#000000')
        $result['color'] = $color;

    $slash_pos = strpos($data, '/');
    $result['size'] = substr($data, 0, $slash_pos);

    $data = substr($data, $slash_pos + 1);

    $slash_pos = strpos($data, '/');
    $result['family'] = substr($data, 0, $slash_pos);

    $result['style'] = substr($data, $slash_pos + 1);
    $result['style-beg'] = $result['style'];
    if (($result['style'] == 'bold') || ($result['style'] == 'italic_bold'))
        $result['weight'] = 'bold';
    else
        $result['weight'] = 'normal';

    if (($result['style'] == 'italic') || ($result['style'] == 'italic_bold'))
        $result['style'] = 'italic';
    else
        $result['style'] = 'normal';

    if($result['style-beg']  == '.'){
        $result['style'] = '.';
        $result['weight'] = '.';
    }

    $fonts_to_load = get_option('fonts_to_load');
    $fonts_to_load[$result['family']] = $result['family'];
    update_option('fonts_to_load', $fonts_to_load);
    return $result;
}

function get_blocks_options($page = "homepage_blocks"){
    if($page == '')
        $page = "homepage_blocks";

    global $NHP_Options;

    $enabled_and_disabled = (array)json_decode(str_replace("+","\"", $NHP_Options->get("homepage_blocks") ) );
    $enabled_blocks = (array)$enabled_and_disabled['enabled'];
    $enabled_blocks = array_flip($enabled_blocks);
    $all_blocks = (array)json_decode(str_replace("+","\"", $NHP_Options->get("block_manager")));
    $result = array();
    foreach($enabled_blocks as $name=>$enabled_block){
        if(isset($all_blocks[$name])){
            $result[$name] = (array)$all_blocks[$name];
        }
    }

    return $result;
}

function print_block_page( $homepage_block ){
    $pagename = $homepage_block['page'];
    $id = $homepage_block['id'];
    ?>

<div class="row"><div class="fifteen columns" id="<?php echo $id; ?>" ><div class="text-block">
    <?php
    $subtitle_exists = isset($homepage_block['subtitle']) && ($homepage_block['subtitle'] != '');
    $title_exists = isset($homepage_block['title']) && ($homepage_block['title'] != '');
    $display_title = $title_exists || $subtitle_exists;

    if($display_title){
        echo '<span class="icon recent"></span>';
        if($title_exists){
            echo '<div class="subtitle" style="padding-left:45px;">'.$homepage_block['subtitle'].'</div>';
        }
        if($subtitle_exists){
            echo '<h2 class="block-title" style="padding-left:45px;">'.$homepage_block['title'].'</h2>';
        }
    }
    $page = get_page_by_path($pagename);
    echo do_shortcode($page->post_content);
    ?>
</div></div></div>

<?php
}

function set_layout($page, $open = true){

    global $NHP_Options;
    $page = $NHP_Options->get($page."_layout");

    if($open){
        if (($page == "2c-l-fixed") || ($page == "3c-fixed") || ($page == "3c-l-fixed")) {
            get_template_part('templates/sidebar', 'left');
        }
        if ($page == "3c-l-fixed"){
            get_template_part('templates/sidebar', 'right');
        }

        if (($page == "2c-l-fixed") || ($page == "2c-r-fixed")) {
            echo '<div id="content" class="eleven columns">';
        } elseif (($page == "1col-fixed")) {
            echo '<div id="content" class="fifteen columns">';
        } else {
            echo '<div id="content" class="seven columns">';
        }
    }else {
        if ($page == "3c-r-fixed") {
            get_template_part('templates/sidebar', 'left');
        }
        if (($page == "2c-r-fixed") || ($page == "3c-fixed") || ($page == "3c-r-fixed")) {
            get_template_part('templates/sidebar', 'right');
        }
    }
}
?>