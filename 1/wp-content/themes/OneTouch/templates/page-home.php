<?php
    global $NHP_Options;

    if (($NHP_Options->get('homepage_title_description_text') == '')||($NHP_Options->get('homepage_subtitle_description_text') == '')){
     echo'<div style="height:35px"> </div>';
    } else { ?>

    <div class="row">
      <div class="fifteen columns">
        <div class="promo">
          <span class="icon info"></span>
          <h2><?php echo $NHP_Options->get('homepage_title_description_text'); ?></h2>
          <h5><?php echo $NHP_Options->get('homepage_subtitle_description_text'); ?></h5>
        </div>
      </div>
    </div>
    <?php } ?>

<?php $homepage_blocks = get_blocks_options();
    foreach($homepage_blocks as $homepage_block){
        ?>

        <?php
        switch($homepage_block['page']){
            case 'post_slider':{
                if($NHP_Options->get('boxed_post_slider') == 4) {

                    get_template_part('templates/block', 'post_no-scroll');

                }
                else {
                    require_once locate_template('templates/block-post_slider-theme-options.php');
                }
                break;
            }
            case 'recent_projects':{
                require_once locate_template('templates/block-recent_projects.php');
                break;
            }
            default:
                print_block_page($homepage_block);
            break;
        }
    }
?>

  <?php
  wp_register_script('qsand', ''.get_template_directory_uri().'/assets/js/jquery.quicksand.js', false, array('jquery'), true);
  wp_enqueue_script('qsand');
  wp_register_script('pfoto', ''.get_template_directory_uri().'/assets/js/jquery.prettyPhoto.js', false, array('jquery'), true);
  wp_enqueue_script('pfoto');
  ?>

  <script type="text/javascript">
    /*-----------------------------------------------------------------------------------

       Custom JS - All front-end jQuery

  -----------------------------------------------------------------------------------*/

    jQuery(document).ready(function($) {



      function portfolio_quicksand() {

        // Setting Up Our Variables
        var $filter;
        var $container;
        var $containerClone;
        var $filterLink;
        var $filteredItems

        // Set Our Filter
        $filter = $('.filter li.active a').attr('class');

        // Set Our Filter Link
        $filterLink = $('.filter li a');

        // Set Our Container
        $container = $('ul.filterable-grid');

        // Clone Our Container
        $containerClone = $container.clone();

        // Apply our Quicksand to work on a click function
        // for each for the filter li link elements
        $filterLink.click(function(e)
        {
          // Remove the active class
          $('.filter li').removeClass('active');

          // Split each of the filter elements and override our filter
          $filter = $(this).attr('class').split(' ');

          // Apply the 'active' class to the clicked link
          $(this).parent().addClass('active');

          // If 'all' is selected, display all elements
          // else output all items referenced to the data-type
          if ($filter == 'all') {
            $filteredItems = $containerClone.find('li');
          }
          else {
            $filteredItems = $containerClone.find('li[data-type~=' + $filter + ']');
          }

          // Finally call the Quicksand function
          $container.quicksand($filteredItems,
              {
                // The Duration for animation
                duration: 750,
                // the easing effect when animation
                easing: 'easeInOutCirc'
                // height adjustment becomes dynamic

              });

          //Initalize our PrettyPhoto Script When Filtered
          $container.quicksand($filteredItems,
              function () { lightbox(); }
          );
        });
      }

      if(jQuery().quicksand) {
        portfolio_quicksand();
      }

      function lightbox() {
        // Apply PrettyPhoto to find the relation with our portfolio item
        $("a[rel^='prettyPhoto']").prettyPhoto({
          // Parameters for PrettyPhoto styling
          animationSpeed:'fast',
          slideshow:5000,
          theme:'pp_default',
          show_title:false,
          overlay_gallery: false,
          social_tools: false
        });
      }

      if(jQuery().prettyPhoto) {
        lightbox();
      }


    }); // END OF DOCUMENT
  </script>

