<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_consts.php');

add_action( 'trash_post', 'Kratos_xianjian_trash_post');
add_action( 'untrash_post', 'Kratos_xianjian_untrash_post');
add_action( 'save_post', 'Kratos_xianjian_save_post');
add_action( 'get_footer', 'Kratos_xianjian_insert_item_id');

function Kratos_xianjian_trash_post($post_id) {
	$body = '["'.$post_id.'"]';
	$request_header = array(
		'Content-Type' => 'application/json',
		'charset' => 'utf-8'
	);
	$args = array(
		'headers' => $request_header,
		'body' => $body,
		'timeout' => '8'
	);
	global $Kratos_xianjian_access_token,$Kratos_xianjian_host;
	$response = wp_remote_post($Kratos_xianjian_host.'/business/items/remove?accessToken='.$Kratos_xianjian_access_token.'&type=1',$args);
	$response_body = wp_remote_retrieve_body($response);
}

function Kratos_xianjian_untrash_post($post_id) {
	global $wpdb,$Kratos_xianjian_access_token;
	$update_posts = $wpdb->get_results("SELECT ID,post_author,post_date,post_content,post_title FROM `".$wpdb->prefix."posts` WHERE ID=".$post_id,ARRAY_A);
	foreach ($update_posts as $update_post) {
		Kratos_xianjian_upload_material($update_post,-2,$Kratos_xianjian_access_token);
	}
}

function Kratos_xianjian_save_post($post_id) {
	$true_post_id = wp_is_post_revision($post_id);
	if ($true_post_id) {

	} else {
		$true_post_id = $post_id;
	}
	global $wpdb,$Kratos_xianjian_access_token;
	$update_posts = $wpdb->get_results("SELECT ID,post_author,post_date,post_content,post_title,post_status FROM `".$wpdb->prefix."posts` WHERE ID=".$true_post_id,ARRAY_A);
	foreach ($update_posts as $update_post) {
		if (strcmp($update_post['post_status'],'publish') == 0) {
			Kratos_xianjian_upload_material($update_post,-2,$Kratos_xianjian_access_token);
		} else {
			Kratos_xianjian_trash_post($update_post['ID']);
		}
	}
}

function Kratos_xianjian_insert_item_id($content) {
	if (is_single()) {
		$post_id = get_queried_object_id();
		echo '<div id="paradigm_detail_page_item_id" data-paradigm-item-id="'.$post_id.'"></div>';
	}
	return $content;
}

?>