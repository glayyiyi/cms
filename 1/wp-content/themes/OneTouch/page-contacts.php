<?php
/*
Template Name: Contacts Page
*/
?>

<?php get_template_part('templates/page', 'header'); ?>

<?php  global $NHP_Options; ?>

<div id="content">

  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

  <div class="team row">


      <?php
      $mypages = get_pages( array( 'child_of' => $post->ID, 'sort_column' => 'post_date', 'sort_order' => 'asc' ) );
      foreach( $mypages as $page ) {

          $imgs = get_the_post_thumbnail($page->ID, 'post-thumbnails');
          $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $imgs, $matches);
          $img_url = $matches [1] [0];
          $article_image = aq_resize($img_url, 140, 140, true); ?>


        <div class="team-brick five columns">
          <div class="bg">
            <a href="<?php echo get_page_link( $page->ID ); ?>">
              <img src="<?php echo $article_image ?>" alt="<?php echo $page->post_title; ?>"/>
            </a>
            <div class="desc">
              <h4><a href="<?php echo get_page_link( $page->ID ); ?>"><?php echo $page->post_title; ?></a></h4>
              <p><?php echo $page->post_excerpt; ?></p>
            </div>
          </div>
        </div>

          <?php    }     ?>

  </div>
<div class="row">

<?php if ($NHP_Options->get("text_contact_page")){?>

      <div class="fifteen columns">
          <article><p>
            <?php $NHP_Options->show("text_contact_page"); ?>
        </p></article>
      </div>
<?php } ?>


    <div class="fifteen columns">
      <div id="map"></div>

      <script type="text/javascript">
        jQuery(document).ready(function () {
          jQuery("#map").gmap3(
              { action:'addMarker',
                address:"<?php echo $NHP_Options->get("map_address"); ?>",
                map:{
                  center:true,
                  zoom:14,
                  scrollwheel: false
                }})
        });
      </script>

    </div>

    <div class="five columns">
        <?php while (have_posts()) : the_post(); ?>
        <?php the_content(''); ?>
        <?php endwhile; ?>
    </div>


    <div class="ten columns">
        <div class="row">
            <div class="fifteen columns">
     <?php
        //Sets reCaptcha settings, accorpding to options, seted by user.
        if( $NHP_Options->get("antispam_way") == 'recaptcha' ) {

            require_once locate_template('/lib/recaptchalib.php');
            if( $NHP_Options->get("default_keys_recaptcha") ||  $privatekey == '' ||  $publickey == '' ){
                $privatekey = "6LcjudoSAAAAAGQGIV7HTZfeTdTeYXyUutgnPjpR";
                $publickey = "6LcjudoSAAAAAG9eoKvy0N1agoRWbWay26CjukYJ";
            } else {
                $privatekey = $NHP_Options->get("private_key_recaptcha");
                $publickey = $NHP_Options->get("public_key_recaptcha");
            }
        }
            if( isset($_POST['sender_email']) ) { //is form sent


                if( $NHP_Options->get("antispam_way") == 'recaptcha' ){    //is reCaptcha in use
                    //Checks is capthca valid
                    $resp = recaptcha_check_answer ($privatekey,
                        $_SERVER["REMOTE_ADDR"],
                        $_POST["recaptcha_challenge_field"],
                        $_POST["recaptcha_response_field"]);

                    //Precessing results of captcha validation
                    if (!$resp->is_valid) {
                        echo _e('<h2>Massage was not sent!<br/>Captcha is incorrect</h2>', 'roots');
                    } else {
                        if( wp_mail(get_option("admin_email") , "Subject: ".$_POST['letter_subject']." Author/".$_POST['sender_name']."/".$_POST['sender_email'], $_POST['letter_text']) )
                            echo _e('<h2>Thank you for your message!</h2>', 'roots');
                        else
                            echo _e('<h2>Unknown error, during message sending.</h2>', 'roots');
                    }

                } else if( $NHP_Options->get("antispam_way") == 'question' ) {  //is controll question in use
                    if( $NHP_Options->get("antispam_answer") == $_POST['antispam_answer'] ){
                        wp_mail(get_option("admin_email") , "Subject: ".$_POST['letter_subject']." Author/".$_POST['sender_name']."/".$_POST['sender_email'], $_POST['letter_text']);
                        echo _e('<h2>Thank you for your message!</h2>', 'roots');
                    } else {
                        echo _e('<h2>Message was not sent!<br/>Answer is incorrect</h2>', 'roots');
                    }
                }
            } else { ?>
      <h3><?php _e('Leave a Reply', 'roots'); ?></h3>
        <form action="" method="POST" name="contact_feedback" id="contact_feedback" class="contact-form">
            <div class="fields">
                <label for="sender_name"><?php _e('Name', 'roots'); ?></label>
                <input type="text" class="text" name="sender_name" id="sender_name" />
                <label for="sender_email"><?php _e('E-mail', 'roots'); ?></label>
                <input id="sender_email" name="sender_email" class="text span4" type="text" />
                <label><?php _e('My letter topic is:', 'roots');?></label>
                <input class="text span4"  type="text" name="letter_subject" id="letter_subject" />
            </div>

            <textarea <?php echo ($NHP_Options->get("antispam_way") == 'recaptcha')?'style="min-height:341px;"':''; ?> name="letter_text" id="letter_text" cols="30" rows="10" placeholder="" tabindex="4"></textarea>
            <input type="submit" class="button right" tabindex="5" value="<?php _e('Send message', 'roots'); ?>" />
            <input type="checkbox" id="agree" style="display:none;" />
            <?php
                if( $NHP_Options->get("antispam_way") == 'recaptcha' ) {
                    echo recaptcha_get_html($publickey);
                } else {
                    echo '<label for="antispam_answer" style="float:left; width:300px; margin-top:13px; margin-right:20px;">'.$NHP_Options->get("antispam_question").'</label>';
                    echo '<input class="text" style="width:331px; float:left;"  type="text" name = "antispam_answer" id="antispam_answer" />';
                }
            ?>
        </form>
        <?php
            wp_register_script('validate', ''.get_template_directory_uri().'/assets/js/jquery.validate.min.js', false, array('jquery'), true);
            wp_enqueue_script('validate');
        ?>

        <script type="text/javascript">
            jQuery(document).ready(function($){

                $("#sender_email,#sender_name,#letter_subject,#letter_text").on("keydown", function(){
                    if( $(this).css("color") == "rgb(255, 0, 0)" )
                        $(this).css("color","#000000").val("");
                });

                $("#contact_feedback input[type=submit]").on("click", function(){
                    if( $("#sender_email").css("color") == "rgb(255, 0, 0)") $("#sender_email").val("");
                    if( $("#sender_name").css("color") == "rgb(255, 0, 0)") $("#sender_name").val("");
                    if( $("#letter_subject").css("color") == "rgb(255, 0, 0)") $("#letter_subject").val("");
                    if ($("#letter_text").css("color") == "rgb(255, 0, 0)") $("#letter_text").val("");
                });

                $("#contact_feedback").validate({
                    submitHandler: function(form) {
                        form.submit();
                    },
                    rules: {
                        sender_email: {
                            required: true,
                            email: true
                        },
                        sender_name: {
                            required:true
                        },
                        letter_subject: {
                            required:true
                        },
                        letter_text: {
                            required:true
                        }
                    },
                    messages: {
                        sender_email: {
                            required: "<?php _e('Type your email!', 'roots'); ?>",
                            email: "<?php _e('Email is incorect', 'roots'); ?>"
                        },
                        sender_name: {
                            required:"<?php _e('Type your name!', 'roots'); ?>"
                        },
                        letter_subject: {
                            required:"<?php _e('Type the subject!', 'roots'); ?>"
                        },
                        letter_text: {
                            required:"<?php _e('Type the message!', 'roots'); ?>"
                        }
                    },
                    errorPlacement: function(error, element) {
                        //element.css("color","red").val(error.html());
                    },
                    invalidHandler: function(form, validator) {
                        for(z in validator['errorList'] ){
                            var element = validator['errorList'][z]['element'];
                            var message = validator['errorList'][z]['message'];
                            jQuery(element).css("color","red").val(message);
                        }
                    }
                });
            });
        </script>
        <?php } ?>
            </div>
        </div>
    </div>
</div>
</div>