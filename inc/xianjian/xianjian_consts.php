<?php 

if (!defined('ABSPATH')) exit;

$xianjian_channel = "theme_jc001";
$xianjian_version = 2;
$xianjian_config_key = "paradigm_render_config";
$xianjian_is_theme = true;
$xianjian_content_side = false;
$xianjian_home_side = false;
$xianjian_home_bottom = false;
$xianjian_config_loaded = false;
$xianjian_host = "https://nbrecsys.4paradigm.com";
$xianjian_sdk_obj = "ParadigmSDKv3";
if (strcmp($xianjian_host, "https://recsys-free.4paradigm.com") == 0) {
	$xianjian_sdk_obj = "ParadigmSDKv3Test";
}

$xianjian_render_content_side_id = 'paradigm_render_content_side_id';
$xianjian_render_content_append_id = 'paradigm_render_content_append_id';
$xianjian_render_content_comment_id = 'paradigm_render_content_comment_id';
$xianjian_render_content_comment_bottom_id = 'paradigm_render_content_comment_bottom_id';
$xianjian_render_home_side_id = 'paradigm_render_home_side_id';
$xianjian_render_home_bottom_id = 'paradigm_render_home_bottom_id';

$xianjian_content_side_id_key = 'paradigm_content_side_id';

$xianjian_last_check_update_timestamp_key = 'paradigm_last_check_update_timestamp';
$xianjian_last_upload_timestamp_key = 'paradigm_last_upload_timestamp';
$xianjian_last_fetch_server_config_key = 'paradigm_last_fetch_server_config';
$xianjian_server_config_key = 'paradigm_server_config';

$xianjian_access_token = "";

$xianjian_js_code_map = array();
$xianjian_side_js_code_map = array();
$xianjian_home_side_js_code_map = array();

?>