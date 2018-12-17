<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_utility.php');

$Kratos_xianjian_title_dom_before = '<h4 class="widget-title">';
$Kratos_xianjian_title_dom_after = '</h4>';
Kratos_xianjian_render_sidebar();

function Kratos_xianjian_render_sidebar() {
	Kratos_xianjian_check_render_config();
	global $Kratos_xianjian_side_js_code_map,$Kratos_xianjian_home_side_js_code_map,$Kratos_xianjian_title_dom_before,$Kratos_xianjian_title_dom_after;
	if (is_single()) {
		if (is_array($Kratos_xianjian_side_js_code_map) && !empty($Kratos_xianjian_side_js_code_map)) {
			foreach ($Kratos_xianjian_side_js_code_map as $key => $code_dic) {
				$title = $code_dic['title'];
				$code = $code_dic['code'];
				echo '<div class="widget widget_Kratos_xianjian_content_side"><div class="widget_box">'.$Kratos_xianjian_title_dom_before.$title.$Kratos_xianjian_title_dom_after.$code.'</div></div>';
			}
		}
	} elseif (is_front_page()) {
		if (is_array($Kratos_xianjian_home_side_js_code_map) && !empty($Kratos_xianjian_home_side_js_code_map)) {
			foreach ($Kratos_xianjian_home_side_js_code_map as $key => $code_dic) {
				$title = $code_dic['title'];
				$code = $code_dic['code'];
				echo '<div class="widget widget_Kratos_xianjian_content_side"><div class="widget_box">'.$Kratos_xianjian_title_dom_before.$title.$Kratos_xianjian_title_dom_after.$code.'</div></div>';
			}
		}
	}
}

?>