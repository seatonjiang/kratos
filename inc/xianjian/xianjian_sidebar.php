<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_utility.php');

$xianjian_title_dom_before = '<h4 class="widget-title">';
$xianjian_title_dom_after = '</h4>';
xianjian_render_sidebar();

function xianjian_render_sidebar() {
	xianjian_check_render_config();
	global $xianjian_side_js_code_map,$xianjian_home_side_js_code_map,$xianjian_title_dom_before,$xianjian_title_dom_after;
	if (is_single()) {
		if (is_array($xianjian_side_js_code_map) && !empty($xianjian_side_js_code_map)) {
			foreach ($xianjian_side_js_code_map as $key => $code_dic) {
				$title = $code_dic['title'];
				$code = $code_dic['code'];
				echo '<div class="widget widget_xianjian_content_side"><div class="widget_box">'.$xianjian_title_dom_before.$title.$xianjian_title_dom_after.$code.'</div></div>';
			}
		}
	} elseif (is_front_page()) {
		if (is_array($xianjian_home_side_js_code_map) && !empty($xianjian_home_side_js_code_map)) {
			foreach ($xianjian_home_side_js_code_map as $key => $code_dic) {
				$title = $code_dic['title'];
				$code = $code_dic['code'];
				echo '<div class="widget widget_xianjian_content_side"><div class="widget_box">'.$xianjian_title_dom_before.$title.$xianjian_title_dom_after.$code.'</div></div>';
			}
		}
	}
}

?>