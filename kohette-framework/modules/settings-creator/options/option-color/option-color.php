<?php

function KTT_color_field($option, $current_value) {

  if (!in_array($option->option_type, array('color'))) return;

  ?>

                    <input
                    type="<?php echo esc_html($option->option_type) ;?>"
                    step="any"
                    style="height:30px;border-radius:2px;<?php echo esc_html($option->option_style);?>"
                    class=""
                    id="<?php echo esc_html($option->option_id);?>"
                    name="<?php echo esc_html($option->option_id) ;?>"
                    value="<?php echo  esc_html($current_value) ;?>">

                    <?php echo esc_html($option->option_label);?>

                    <?php if ($option->option_description) {?> <p class="description"><?php echo esc_html($option->option_description);?></p> <?php } ?>


                    <?php


}
add_action('KTT_settings_option_field', 'KTT_color_field', 2, 2);

?>
