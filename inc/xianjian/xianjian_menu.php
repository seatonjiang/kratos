<?php
if (!defined('ABSPATH')) exit;

add_action( 'admin_menu', 'Kratos_xianjian_rec_menu');

function Kratos_xianjian_rec_menu() {
    add_menu_page('推荐设置','推荐设置', 'manage_options','Kratos_xianjian_rec_options', 'Kratos_xianjian_rec_options');
}

function Kratos_xianjian_rec_options() {
	include 'xianjian_setting.php';
}

?>