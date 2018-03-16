<?php
/**
 * settings option
 *
 *
 */



/**
* option field
*/
function KTT_checkbox_field($option, $current_value) {

  if ($option->option_type != 'checkbox') return;

  ?>


                  <label>
                    <input
                    type="checkbox"
                    style="margin-left:0px"
                    id="<?php echo esc_html($option->option_id);?>"
                    name="<?php echo esc_html($option->option_id) ;?>"
                    <?php echo esc_html($option->link($option->option_id));?>
                    <?php  checked( $current_value ); ?>
                    value="1">

                    <?php echo esc_html($option->option_label);?>

                    <?php if ($option->option_description) {?> <p class="description"><?php echo esc_html($option->option_description);?></p> <?php } ?>
                  </label>
                    <?php



}
add_action('KTT_settings_option_field', 'KTT_checkbox_field', 2, 2);
