<?php
/**
 * Narratium functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Narratium
*/

/**
* We include the configuration files of the theme
*/
require_once(get_parent_theme_file_path('theme-config/theme-config.php'));

/**
* load the kohette framework (handy functions!)
*/
require_once(get_parent_theme_file_path('kohette-framework/kohette-framework.php'));

/**
* Add a feature this post checkbox to the post creation page
*/
function sm_custom_meta() {
  add_meta_box('sm_meta', __('Featured Posts', 'sm-textdomain'), 'sm_meta_callback', 'post');
}
function sm_meta_callback( $post ) {
  $featured = get_post_meta($post->ID);
  ?>
  <p>
    <div class="sm-row-content">
      <label for="meta-checkbox">
        <input type="checkbox" name="meta-checkbox" id="meta-checkbox" value="yes" <?php if ( isset ($featured['meta-checkbox']) ) checked($featured['meta-checkbox'][0], 'yes'); ?> />
        <?php _e('Feature this post', 'sm-textdomain')?>
      </label>
    </div>
  </p>
  <?php
}
add_action('add_meta_boxes', 'sm_custom_meta');

/**
* Saves the featured post input
*/
function sm_meta_save($post_id) {
  // Checks save status
  $is_autosave = wp_is_post_autosave($post_id);
  $is_revision = wp_is_post_revision($post_id);
  $is_valid_nonce = ( isset($_POST['sm_nonce']) && wp_verify_nonce($_POST['sm_nonce'], basename(__FILE__))) ? 'true' : 'false';

  // Exits script depending on save status
  if ($is_autosave || $is_revision || !$is_valid_nonce) {
    return;
  }
  // Checks for input and saves
  if (isset($_POST['meta-checkbox'])) {
    update_post_meta($post_id, 'meta-checkbox', 'yes');
  } else {
    update_post_meta($post_id, 'meta-checkbox', '');
  }
}
add_action('save_post', 'sm_meta_save');

/**
* create a kohette framework object
*/
$theme = new kohette_framework();

/**
* We execute the function that is responsible for activating the theme and load the default options when it is activated for the first time from the themes page of the administration.
*/
$theme->start_kohette_framework();
