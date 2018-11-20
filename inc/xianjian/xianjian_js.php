<?php 
if (!defined('ABSPATH')) exit;

include_once('xianjian_consts.php');


function xianjian_set_render_js_code($render_div_id, $config) {
	global $xianjian_js_code_map,$xianjian_host,$xianjian_sdk_obj;
	$xianjian_js_code = "<div id='".$render_div_id."_".$config['sceneId']."'><script charset='utf-8' id='ParadigmSDKv3' src='".$xianjian_host."/sdk/js/ParadigmSDK_v3.js'></script><script>".$xianjian_sdk_obj.".init('".$config['clientToken']."',{ isDisableArticleFetch: true });".$xianjian_sdk_obj.".renderArticle('".$render_div_id."_".$config['sceneId']."',".$config['itemSetId'].",".$config['sceneId'].");</script></div>";
	$pre_js_code = $xianjian_js_code_map[$render_div_id];
	$total_js_code = $pre_js_code.$xianjian_js_code;
	$xianjian_js_code_map[$render_div_id] = $total_js_code;
}

function xianjian_set_side_render_js_code($render_div_id, $config) {
	global $xianjian_side_js_code_map,$xianjian_host,$xianjian_sdk_obj;
	$xianjian_js_code = "<div id='".$render_div_id."_".$config['sceneId']."'><script charset='utf-8' id='ParadigmSDKv3' src='".$xianjian_host."/sdk/js/ParadigmSDK_v3.js'></script><script>".$xianjian_sdk_obj.".init('".$config['clientToken']."',{ isDisableArticleFetch: true });".$xianjian_sdk_obj.".renderArticle('".$render_div_id."_".$config['sceneId']."',".$config['itemSetId'].",".$config['sceneId'].");</script></div>";
	$code_dic = array();
	$code_dic['used'] = false;
	$code_dic['code'] = $xianjian_js_code;
	$code_dic['title'] = $config['recomTitle'];
	$xianjian_side_js_code_map[$config['sceneId']] = $code_dic;
}

function xianjian_set_home_side_render_js_code($render_div_id, $config) {
	global $xianjian_home_side_js_code_map,$xianjian_host,$xianjian_sdk_obj;
	$xianjian_js_code = "<div id='".$render_div_id."_".$config['sceneId']."'><script charset='utf-8' id='ParadigmSDKv3' src='".$xianjian_host."/sdk/js/ParadigmSDK_v3.js'></script><script>".$xianjian_sdk_obj.".init('".$config['clientToken']."',{ isDisableArticleFetch: true });".$xianjian_sdk_obj.".renderArticle('".$render_div_id."_".$config['sceneId']."',".$config['itemSetId'].",".$config['sceneId'].");</script></div>";
	$code_dic = array();
	$code_dic['used'] = false;
	$code_dic['code'] = $xianjian_js_code;
	$code_dic['title'] = $config['recomTitle'];
	$xianjian_home_side_js_code_map[$config['sceneId']] = $code_dic;
}

function insert_xianjian_js($args, $render_div_id) {
	global $xianjian_js_code_map;
	extract($args);
	echo $before_widget;
	echo $before_title . __('先荐', 'xianjian') . $after_title;
	echo $xianjian_js_code_map[$render_div_id];
	echo $after_widget;
}

function insert_xianjian_side_js($args, $render_div_id) {
	global $xianjian_side_js_code_map;
	$current_key = null;
	$current_arr = array();
	if (is_array($xianjian_side_js_code_map) && !empty($xianjian_side_js_code_map)) {
		foreach ($xianjian_side_js_code_map as $key => $code_dic) {
			if ($code_dic['used']) {
				
			} else {
				$current_key = $key;
				$current_arr = $code_dic;
				break;
			}
		}
	} else {
		return;
	}
	if (empty($current_key)) {
		return;
	}
	$title = $current_arr['title'];
	$code = $current_arr['code'];
	$current_arr['used'] = true;
	$xianjian_side_js_code_map[$current_key] = $current_arr;
	extract($args);
	echo $before_widget;
	echo $before_title . __($title, 'xianjian') . $after_title;
	echo $code;
	echo $after_widget;
}

function insert_xianjian_home_side_js($args, $render_div_id) {
	global $xianjian_home_side_js_code_map;
	$current_key = null;
	$current_arr = array();
	if (is_array($xianjian_home_side_js_code_map) && !empty($xianjian_home_side_js_code_map)) {
		foreach ($xianjian_home_side_js_code_map as $key => $code_dic) {
			if ($code_dic['used']) {
				
			} else {
				$current_key = $key;
				$current_arr = $code_dic;
				break;
			}
		}
	} else {
		return;
	}
	if (empty($current_key)) {
		return;
	}
	$title = $current_arr['title'];
	$code = $current_arr['code'];
	$current_arr['used'] = true;
	$xianjian_home_side_js_code_map[$current_key] = $current_arr;
	extract($args);
	echo $before_widget;
	echo $before_title . __($title, 'xianjian') . $after_title;
	echo $code;
	echo $after_widget;
}

?>