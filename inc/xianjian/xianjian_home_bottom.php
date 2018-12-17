<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_utility.php');

Kratos_xianjian_render_home_bottom();

function Kratos_xianjian_render_home_bottom() {
	Kratos_xianjian_check_render_config();
	global $Kratos_xianjian_js_code_map,$Kratos_xianjian_render_home_bottom_id;
	if (is_front_page()) {
		if (is_array($Kratos_xianjian_js_code_map) && !empty($Kratos_xianjian_js_code_map)) {
			echo $Kratos_xianjian_js_code_map[$Kratos_xianjian_render_home_bottom_id];
		}
	}
}

?>