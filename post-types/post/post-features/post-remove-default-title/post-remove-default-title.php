<?php
/**
* This script is responsible for eliminating the input and title of the entries
*/

function KTT_remove_post_title_input() {remove_post_type_support('post', 'title');};
add_action('admin_init', 'KTT_remove_post_title_input');

 ?>
