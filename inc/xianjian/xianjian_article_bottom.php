<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_utility.php');

xianjian_render_article_bottom();

function xianjian_render_article_bottom() {
	xianjian_check_render_config();
	global $xianjian_js_code_map,$xianjian_render_content_append_id;
	if (is_single()) {
		if (is_array($xianjian_js_code_map) && !empty($xianjian_js_code_map)) {
			echo '<div style="margin-bottom:15px">';
			echo $xianjian_js_code_map[$xianjian_render_content_append_id];
			echo '</div>';
		}
	}
}

?>