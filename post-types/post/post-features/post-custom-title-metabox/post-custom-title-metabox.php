<?php
/**
* Create a metabox to enter a title with the format of the entry
*/

/**
* Creation of the metabox_id with the hyper-amazing KTT Framework
*/
$args = array();
$args['metabox_id'] 					= 	'post_title_formated';
$args['metabox_name']					= 	esc_html__("Title", 'narratium');
$args['metabox_post_type'] 		= 	'post';
$args['metabox_vars'] 				= 	array(
                                      KTT_var_name('post_title_formated')
                                  );
$args['metabox_callback']			= 	'KTT_post_title_meta_box';
$args['metabox_context']			= 	'advanced';
$args['metabox_priority']			= 	'high';
$metabox = new KTT_new_metabox($args);



/**
* Metabox render
*/
function KTT_post_title_meta_box($post) {

    /**
    * If currently the entry does not have a formatted title we put
    * by default the normal title of the entry.
    */
    if (!isset($post->post_title_formated)) $post->post_title_formated = $post->post_title;

    /**
    * We declare the configuration of our editor
    */
    $editor_settings = array(
                                      'wpautop' => true,
                                      'media_buttons' => false,
                                      'textarea_name' => KTT_var_name('post_title_formated'),
                                      'textarea_rows' => 0,
                                      'quicktags' => false,
                                      'tinymce' => array(
                                                'toolbar1'=> 'bold,italic,underline,link,unlink,forecolor'
                                        )
    );

    ?>
    <div id="titlediv">
      <p>
        <?php esc_html_e('Insert a title for your post. You can use format tags.', 'narratium');?>
      </p>
    <?php

    /**
    * We create the editor
    */
    wp_editor( $post->post_title_formated, KTT_var_name('post_title_formated'), $editor_settings );




      /**
      * Post name / slug / permalink magic
      */
      global $post_type, $post_type_object;
      $sample_permalink_html = $post_type_object->public ? get_sample_permalink_html($post->ID) : '';

    	if ( $post_type_object->public && ! ( 'pending' == get_post_status( $post ) && !current_user_can( $post_type_object->cap->publish_posts ) ) ) {
    	        $has_sample_permalink = $sample_permalink_html && 'auto-draft' != $post->post_status;
    	?>
    	        <div id="edit-slug-box" class="hide-if-no-js">
    	        <?php
    	                if ( $has_sample_permalink ) echo get_sample_permalink_html($post->ID);
    	        ?>
    	        </div>
    	<?php
    	}
      wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );


    ?>
    </div>
    <?php
}







/***
* We make sure to capture the hook that is executed every time a postmeta is saved for
  this case update the title of the post based on the post_title_formated that we have saved
*/
function KTT_update_post_title_from_formated($meta_id, $post_id, $meta_key, $meta_value) {

      /**
      * If it is not the goal we are looking for or a value has not been indicated we will leave here
      */
      if ( $meta_key != KTT_var_name('post_title_formated'))  return;
      if (!$meta_value) return;

      /**
      * the meta_value is the formatted text, we must sanitize it to eliminate html tags
      */
      $meta_value = wp_strip_all_tags($meta_value, true);

      /**
      * We update the post to which this postmeta belongs to change the title
      */
      KTT_change_post_field($post_id, 'post_title', $meta_value);

      /**
      * With this we make sure to put a correct permalink in the case in which the Post
      * is being published for the first time (instead of an update)
      */
      $post = KTT_get_post($post_id);
      if ($post->post_name == 'auto-draft')  KTT_change_post_field($post_id, 'post_name', sanitize_title($meta_value));

}
add_action( 'added_post_meta', 'KTT_update_post_title_from_formated', 5, 4 );
add_action( 'updated_post_meta', 'KTT_update_post_title_from_formated', 5, 4 );
