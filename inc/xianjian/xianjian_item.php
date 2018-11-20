<?php 

if (!defined('ABSPATH')) exit;

include_once('xianjian_consts.php');

add_action( 'trash_post', 'xianjian_trash_post');
add_action( 'untrash_post', 'xianjian_untrash_post');
add_action( 'save_post', 'xianjian_save_post');
add_action( 'get_footer', 'xianjian_insert_item_id');

function xianjian_trash_post($post_id) {
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
	global $xianjian_access_token,$xianjian_host;
	$response = wp_remote_post($xianjian_host.'/business/items/remove?accessToken='.$xianjian_access_token.'&type=1',$args);
	$response_body = wp_remote_retrieve_body($response);
}

function xianjian_untrash_post($post_id) {
	global $wpdb,$xianjian_access_token;
	$update_posts = $wpdb->get_results("SELECT ID,post_author,post_date,post_content,post_title FROM `".$wpdb->prefix."posts` WHERE ID=".$post_id,ARRAY_A);
	foreach ($update_posts as $update_post) {
		xianjian_upload_material($update_post,-2,$xianjian_access_token);
	}
}

function xianjian_save_post($post_id) {
	$true_post_id = wp_is_post_revision($post_id);
	if ($true_post_id) {

	} else {
		$true_post_id = $post_id;
	}
	global $wpdb,$xianjian_access_token;
	$update_posts = $wpdb->get_results("SELECT ID,post_author,post_date,post_content,post_title,post_status FROM `".$wpdb->prefix."posts` WHERE ID=".$true_post_id,ARRAY_A);
	foreach ($update_posts as $update_post) {
		if (strcmp($update_post['post_status'],'publish') == 0) {
			xianjian_upload_material($update_post,-2,$xianjian_access_token);
		} else {
			xianjian_trash_post($update_post['ID']);
		}
	}
}

function xianjian_insert_item_id($content) {
	if (is_single()) {
		$post_id = get_queried_object_id();
		echo '<div id="paradigm_detail_page_item_id" data-paradigm-item-id="'.$post_id.'"></div>';
	}
	return $content;
}

?>