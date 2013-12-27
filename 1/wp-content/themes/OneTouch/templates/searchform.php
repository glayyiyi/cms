
<form role="search" method="get" id="searchform" class="form-search" action="<?php echo home_url('/'); ?>">
  <label class="hide" for="s"><?php _e('Search for:', 'roots'); ?></label>
  <input type="text" value="" name="s" id="s" class="s-field" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
  <input type="submit" id="searchsubmit" value="" class="s-submit">
</form>