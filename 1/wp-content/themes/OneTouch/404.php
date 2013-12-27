<?php get_template_part('templates/page', 'header'); ?>
<?php global $NHP_Options; ?>
<div class="row">

    <?php  set_layout('404') ?>

<div class="alert alert-block fade in">
  <div class="messagebox_text">
    <h2><?php _e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'roots'); ?></h2>
  </div>
</div>

<p><?php _e('Please try the following:', 'roots'); ?></p>
<ul>
  <li><?php _e('Check your spelling', 'roots'); ?></li>
  <li><?php printf(__('Return to the <a href="%s">home page</a>', 'roots'), home_url()); ?></li>
  <li><?php _e('Click the <a href="javascript:history.back()">Back</a> button', 'roots'); ?></li>
</ul>

<?php get_search_form(); ?>

  </div>
    <?php  set_layout('404', false) ?>
</div>