<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_utility.php');

Kratos_xianjian_render_article_bottom();

function Kratos_xianjian_render_article_bottom() {
	Kratos_xianjian_check_render_config();
	global $Kratos_xianjian_js_code_map,$Kratos_xianjian_render_content_append_id;
	if (is_single()) {
		if (is_array($Kratos_xianjian_js_code_map) && !empty($Kratos_xianjian_js_code_map)) {
			echo '<div style="margin-bottom:15px">';
			echo $Kratos_xianjian_js_code_map[$Kratos_xianjian_render_content_append_id];
			echo '</div>';
		}
	}
}

?>