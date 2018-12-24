<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_consts.php');
include_once('xianjian_utility.php');
include_once('xianjian_item.php');

add_action( 'wp_loaded', 'Kratos_xianjian_setup');
add_action( 'admin_enqueue_scripts', 'Kratos_xianjian_custom_scripts');

function Kratos_xianjian_custom_scripts() {
	global $Kratos_xianjian_version;
	wp_enqueue_style('kratos_plugin', get_template_directory_uri() . '/inc/xianjian/xianjian_title_adapter.css', array(), $Kratos_xianjian_version);
}

function Kratos_xianjian_setup() {
	Kratos_xianjian_token_verify();
	Kratos_xianjian_check_render_config();
	Kratos_xianjian_check_item();
}

function Kratos_xianjian_token_verify() {
	$site_id_key="paradigm_site_id";
	$site_token_key = "paradigm_site_token";
	$site_token = get_option($site_token_key);
	if ($site_token == "") {

	} else {
		return;
	}
	$site_id=get_option($site_id_key);
	if($site_id=="") {
		$site_id = Kratos_xianjian_random_str(16);
		update_option($site_id_key,$site_id); 
	}
	global $Kratos_xianjian_channel;
	update_option('paradigm_site_channel',$Kratos_xianjian_channel);
	$body_arr = array( 
		'domain' => home_url(), 
		'plugSiteId' => $site_id,
		'terminalType' => 7,
		'plugChannel' => $Kratos_xianjian_channel,
	);
	$body = json_encode((object)$body_arr);
	$args = array(
		'body' => $body_arr,
		'timeout' => '8'
	);
	global $Kratos_xianjian_host;
	$remote_url = $Kratos_xianjian_host.'/business/plug/register/login';
	$response = wp_remote_post($remote_url,$args);
	$response_body = wp_remote_retrieve_body($response);
	$response_obj = json_decode($response_body,true);
	try {
		$code = $response_obj['code'];
		if ($code == 200) {
			$data = $response_obj['data'];

			$Kratos_xianjian_config_key = "paradigm_render_config";
			$original_config_str = get_option($Kratos_xianjian_config_key);
			$original_config = null;
			if ($original_config_str == '') {
				$original_config = array();
			} else {
				$original_config = json_decode($original_config_str, true);
			}
			$client_token = $data['clientToken'];
			foreach ($data as $key => $value) {
				if (strcmp('token',$key) == 0) {
					
				} elseif (strcmp('clientToken', $key) == 0) {
					
				} else {
					$new_config_str = $value[$key];
					if (is_string($new_config_str)) {
						$new_config_str = stripslashes($new_config_str);
						$new_config = json_decode($new_config_str, true);
						$new_config['sceneId'] = $key;
						$new_config['clientToken'] = $client_token;
						$new_config['recomTitle'] = $new_config["sceneName"];
						$new_config['itemSetId'] = $value['itemSetId'];
						$new_config['accessToken'] = $value['accessToken'];
						$original_config[$key] = $new_config;
					}
				}
			}
			
			$total_config_str = json_encode($original_config);
			if (strlen($total_config_str) > 5) {
				update_option($Kratos_xianjian_config_key,$total_config_str);
			}

			$token = $data['token'];
			update_option($site_token_key,$token);
		}

	} catch (Exception $e) {

	}
}

function Kratos_xianjian_check_item() {
	global $Kratos_xianjian_last_upload_timestamp_key,$Kratos_xianjian_last_fetch_server_config_key,$Kratos_xianjian_server_config_key,$Kratos_xianjian_host,$Kratos_xianjian_access_token;

	$last_fetch_config_timestamp = get_option($Kratos_xianjian_last_fetch_server_config_key);
	$current_time = time();
	$Kratos_xianjian_server_config = get_option($Kratos_xianjian_server_config_key);
	$Kratos_xianjian_fetch_interval = 60 * 60;
	$Kratos_xianjian_day_minute_max = 30;
	$Kratos_xianjian_night_minute_max = 300;
	$Kratos_xianjian_day_limit = 1;
	$Kratos_xianjian_night_limit = 20;
	if ($Kratos_xianjian_server_config == "") {

	} else {
		$server_config = json_decode($Kratos_xianjian_server_config,true);
		$Kratos_xianjian_fetch_interval = $server_config['interval'] ? $server_config['interval'] : 60 *60;
		$Kratos_xianjian_day_minute_max = $server_config['dayMinuteMax'] ? $server_config['dayMinuteMax'] : 30;
		$Kratos_xianjian_night_minute_max = $server_config['nightMinuteMax'] ? $server_config['nightMinuteMax'] : 300;
		$Kratos_xianjian_day_limit = $server_config['dayLimit'] ? $server_config['dayLimit'] : 1;
		$Kratos_xianjian_night_limit = $server_config['nightLimit'] ? $server_config['nightLimit'] : 20;
	}
	if ($current_time - $last_fetch_config_timestamp > $Kratos_xianjian_fetch_interval) {
		$args = array(
			'timeout' => '5'
		);
		$response = wp_remote_get($Kratos_xianjian_host.'/business/cms/plug/post/config?token=ai4paradigm&accessToken='.$Kratos_xianjian_access_token,$args);
		$response_body = wp_remote_retrieve_body($response);
		$response_obj = json_decode($response_body,true);
		$data = $response_obj['data'];
		$config_str = $data['plugSitePostConfig'];
		$config_str = stripslashes($config_str);
		if ($config_str == "" || strcmp($config_str, 'null')==0 || $config_str == null) {
		 	
		} else {
			update_option($Kratos_xianjian_server_config_key,$config_str);
		}
		$current_time = time();
		update_option($Kratos_xianjian_last_fetch_server_config_key,$current_time);
		return;
	}

	$post_limit = 5;
	$update_interval = 5;
	if (Kratos_xianjian_check_night_time()) {
		$post_limit = $Kratos_xianjian_night_limit;
		$update_interval = 60 / ($Kratos_xianjian_night_minute_max / $Kratos_xianjian_night_limit);
	} else {
		$post_limit = $Kratos_xianjian_day_limit;
		$update_interval = 60 / ($Kratos_xianjian_day_minute_max / $Kratos_xianjian_day_limit);
	}

	$last_upload_tiemstamp = get_option($Kratos_xianjian_last_upload_timestamp_key);
	$current_time = time();
	if ($current_time - $last_upload_tiemstamp < $update_interval) {
		return;
	}
	global $wpdb,$Kratos_xianjian_access_token;
	if ($Kratos_xianjian_access_token == "") {
		return;
	}

	$last_upload_id_key = 'last_upload_id_'.$Kratos_xianjian_access_token;
	$last_upload_id = get_option($last_upload_id_key);

	if (!$last_upload_id) {
		$last_upload_id = 0;
	}

	# 获取文章信息
	$posts = $wpdb->get_results("SELECT ID,post_author,post_date,post_content,post_title,post_status,post_parent FROM `".$wpdb->prefix."posts` WHERE ID>".$last_upload_id." AND post_status='publish' ORDER BY ID ASC LIMIT ".$post_limit,ARRAY_A);
	foreach ($posts as $post) {
		if (strcmp('publish', $post['post_status']) == 0) {
			Kratos_xianjian_upload_material($post,-1,$Kratos_xianjian_access_token);
		} elseif (strcmp('inherit', $post['post_status']) == 0) {
			$post_parent = $post['post_parent'];
			if ((int)$post_parent != 0) {
				$inherit_id = $post_parent;
				$update_posts = $wpdb->get_results("SELECT ID,post_author,post_date,post_content,post_title,post_status FROM `".$wpdb->prefix."posts` WHERE ID=".$inherit_id,ARRAY_A);
				foreach ($update_posts as $update_post) {
					if (strcmp('publish', $update_post['post_status']) == 0) {
						Kratos_xianjian_upload_material($update_post,$post['ID'],$Kratos_xianjian_access_token);
					} else {
						update_option($last_upload_id_key,$post['ID']);
					}
				}
			} else {
				update_option($last_upload_id_key,$post['ID']);
			}
		} else {
			update_option($last_upload_id_key,$post['ID']);
		}
	}
	$new_time = time();
	update_option($Kratos_xianjian_last_upload_timestamp_key, $new_time);
}

?>