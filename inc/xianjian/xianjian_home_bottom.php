<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_utility.php');

xianjian_render_home_bottom();

function xianjian_render_home_bottom() {
	xianjian_check_render_config();
	global $xianjian_js_code_map,$xianjian_render_home_bottom_id;
	if (is_front_page()) {
		if (is_array($xianjian_js_code_map) && !empty($xianjian_js_code_map)) {
			echo $xianjian_js_code_map[$xianjian_render_home_bottom_id];
		}
	}
}

?>