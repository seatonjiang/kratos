<?php 

if (!defined('ABSPATH')) exit;

$Kratos_xianjian_channel = "theme_jc001";$Kratos_xianjian_version = '1.1.0';
$Kratos_xianjian_config_key = "paradigm_render_config";
$Kratos_xianjian_is_theme = true;$Kratos_xianjian_content_side = false;
$Kratos_xianjian_home_side = false;
$Kratos_xianjian_home_bottom = false;
$Kratos_xianjian_config_loaded = false;
$Kratos_xianjian_host = "https://nbrecsys.4paradigm.com";
$Kratos_xianjian_sdk_obj = "ParadigmSDKv3";
if (strcmp($Kratos_xianjian_host, "https://recsys-free.4paradigm.com") == 0) {
	$Kratos_xianjian_sdk_obj = "ParadigmSDKv3Test";
}

$Kratos_xianjian_render_content_side_id = 'paradigm_render_content_side_id';
$Kratos_xianjian_render_content_append_id = 'paradigm_render_content_append_id';
$Kratos_xianjian_render_content_comment_id = 'paradigm_render_content_comment_id';
$Kratos_xianjian_render_content_comment_bottom_id = 'paradigm_render_content_comment_bottom_id';
$Kratos_xianjian_render_home_side_id = 'paradigm_render_home_side_id';
$Kratos_xianjian_render_home_bottom_id = 'paradigm_render_home_bottom_id';

$Kratos_xianjian_content_side_id_key = 'paradigm_content_side_id';

$Kratos_xianjian_last_check_update_timestamp_key = 'paradigm_last_check_update_timestamp';
$Kratos_xianjian_last_upload_timestamp_key = 'paradigm_last_upload_timestamp';
$Kratos_xianjian_last_fetch_server_config_key = 'paradigm_last_fetch_server_config';
$Kratos_xianjian_server_config_key = 'paradigm_server_config';

$Kratos_xianjian_access_token = "";

$Kratos_xianjian_js_code_map = array();
$Kratos_xianjian_side_js_code_map = array();
$Kratos_xianjian_home_side_js_code_map = array();

?>