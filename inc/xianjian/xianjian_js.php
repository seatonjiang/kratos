<?php 
if (!defined('ABSPATH')) exit;

include_once('xianjian_consts.php');

function Kratos_xianjian_set_render_js_code($render_div_id, $config) {
	global $Kratos_xianjian_js_code_map,$Kratos_xianjian_host,$Kratos_xianjian_sdk_obj;
	$Kratos_xianjian_js_code = "<div id='".$render_div_id."_".$config['sceneId']."'></div><script charset='utf-8' id='ParadigmSDKv3' src='".$Kratos_xianjian_host."/sdk/js/ParadigmSDK_v3.js'></script><script>try{var paradigmSession = window.paradigmSession;if(!paradigmSession || typeof(paradigmSession)==undefined){window.paradigmSession={};paradigmSession=window.paradigmSession}    paradigmValue=paradigmSession['".$render_div_id."_".$config['sceneId']."'];if(!paradigmValue || typeof(paradigmValue)==undefined){".$Kratos_xianjian_sdk_obj.".init('".$config['clientToken']."',{ isDisableArticleFetch: true });".$Kratos_xianjian_sdk_obj.".renderArticle('".$render_div_id."_".$config['sceneId']."',".$config['itemSetId'].",".$config['sceneId'].");paradigmSession['".$render_div_id."_".$config['sceneId']."']='true';}}catch(e){}</script>";
	$pre_js_code = $Kratos_xianjian_js_code_map[$render_div_id];
	$total_js_code = $pre_js_code.$Kratos_xianjian_js_code;
	$Kratos_xianjian_js_code_map[$render_div_id] = $total_js_code;
}

function Kratos_xianjian_set_side_render_js_code($render_div_id, $config) {
	global $Kratos_xianjian_side_js_code_map,$Kratos_xianjian_host,$Kratos_xianjian_sdk_obj;
	$Kratos_xianjian_js_code = "<div id='".$render_div_id."_".$config['sceneId']."'></div><script charset='utf-8' id='ParadigmSDKv3' src='".$Kratos_xianjian_host."/sdk/js/ParadigmSDK_v3.js'></script><script>try{var paradigmSession = window.paradigmSession;if(!paradigmSession || typeof(paradigmSession)==undefined){window.paradigmSession={};paradigmSession=window.paradigmSession}    paradigmValue=paradigmSession['".$render_div_id."_".$config['sceneId']."'];if(!paradigmValue || typeof(paradigmValue)==undefined){".$Kratos_xianjian_sdk_obj.".init('".$config['clientToken']."',{ isDisableArticleFetch: true });".$Kratos_xianjian_sdk_obj.".renderArticle('".$render_div_id."_".$config['sceneId']."',".$config['itemSetId'].",".$config['sceneId'].");paradigmSession['".$render_div_id."_".$config['sceneId']."']='true';}}catch(e){}</script>";
	$code_dic = array();
	$code_dic['used'] = false;
	$code_dic['code'] = $Kratos_xianjian_js_code;
	$code_dic['title'] = $config['recomTitle'];
	$Kratos_xianjian_side_js_code_map[$config['sceneId']] = $code_dic;
}

function Kratos_xianjian_set_home_side_render_js_code($render_div_id, $config) {
	global $Kratos_xianjian_home_side_js_code_map,$Kratos_xianjian_host,$Kratos_xianjian_sdk_obj;
	$Kratos_xianjian_js_code = "<div id='".$render_div_id."_".$config['sceneId']."'></div><script charset='utf-8' id='ParadigmSDKv3' src='".$Kratos_xianjian_host."/sdk/js/ParadigmSDK_v3.js'></script><script>try{var paradigmSession = window.paradigmSession;if(!paradigmSession || typeof(paradigmSession)==undefined){window.paradigmSession={};paradigmSession=window.paradigmSession}    paradigmValue=paradigmSession['".$render_div_id."_".$config['sceneId']."'];if(!paradigmValue || typeof(paradigmValue)==undefined){".$Kratos_xianjian_sdk_obj.".init('".$config['clientToken']."',{ isDisableArticleFetch: true });".$Kratos_xianjian_sdk_obj.".renderArticle('".$render_div_id."_".$config['sceneId']."',".$config['itemSetId'].",".$config['sceneId'].");paradigmSession['".$render_div_id."_".$config['sceneId']."']='true';}}catch(e){}</script>";
	$code_dic = array();
	$code_dic['used'] = false;
	$code_dic['code'] = $Kratos_xianjian_js_code;
	$code_dic['title'] = $config['recomTitle'];
	$Kratos_xianjian_home_side_js_code_map[$config['sceneId']] = $code_dic;
}

function insert_Kratos_xianjian_js($args, $render_div_id) {
	global $Kratos_xianjian_js_code_map;
	extract($args);
	echo $before_widget;
	echo $before_title . __('先荐', 'Kratos_xianjian') . $after_title;
	echo $Kratos_xianjian_js_code_map[$render_div_id];
	echo $after_widget;
}

function insert_Kratos_xianjian_side_js($args, $render_div_id) {
	global $Kratos_xianjian_side_js_code_map;
	$current_key = null;
	$current_arr = array();
	if (is_array($Kratos_xianjian_side_js_code_map) && !empty($Kratos_xianjian_side_js_code_map)) {
		foreach ($Kratos_xianjian_side_js_code_map as $key => $code_dic) {
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
	$Kratos_xianjian_side_js_code_map[$current_key] = $current_arr;
	extract($args);
	echo $before_widget;
	echo $before_title . __($title, 'Kratos_xianjian') . $after_title;
	echo $code;
	echo $after_widget;
}

function insert_Kratos_xianjian_home_side_js($args, $render_div_id) {
	global $Kratos_xianjian_home_side_js_code_map;
	$current_key = null;
	$current_arr = array();
	if (is_array($Kratos_xianjian_home_side_js_code_map) && !empty($Kratos_xianjian_home_side_js_code_map)) {
		foreach ($Kratos_xianjian_home_side_js_code_map as $key => $code_dic) {
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
	$Kratos_xianjian_home_side_js_code_map[$current_key] = $current_arr;
	extract($args);
	echo $before_widget;
	echo $before_title . __($title, 'Kratos_xianjian') . $after_title;
	echo $code;
	echo $after_widget;
}

?>