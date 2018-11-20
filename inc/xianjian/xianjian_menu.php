<?php
if (!defined('ABSPATH')) exit;

add_action( 'admin_menu', 'xianjian_rec_menu');

function xianjian_rec_menu() {
    add_theme_page('推荐设置','推荐设置', 'manage_options','xianjian_rec_options', 'xianjian_rec_options');
}

function xianjian_rec_options() {
	include 'xianjian_setting.php';
}

?>