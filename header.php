<?php
/**
 * The template for displaying the header
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
?><!DOCTYPE HTML>
<html class="no-js">
	<head>
		<title><?php wp_title( '-', true, 'right' ); ?></title>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="robots" content="index,follow">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<meta name="format-detection" content="telphone=no, email=no">
		<meta name="description" content="<?php kratos_description(); ?>" />
		<meta name="keywords" content="<?php kratos_keywords();?>" />
		<meta itemprop="image" content="<?php echo kratos_thumbnail_url(); ?>"/>
		<meta name="description" itemprop="description" content="<?php kratos_description(); ?>" />
		<?php wp_head(); ?>
		<?php wp_print_scripts('jquery'); ?>
		<?php if ( kratos_option('site_bw')==1 ) : ?>
			<style type="text/css">html{filter: grayscale(100%);-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);filter: gray;-webkit-filter: grayscale(1); }
			</style>
		<?php endif; ?>
		<script>
		var _hmt = _hmt || [];
		(function() {
		  var hm = document.createElement("script");
		  hm.src = "https://hm.baidu.com/hm.js?b57a2a15997077a03a05258ac5d99c84";
		  var s = document.getElementsByTagName("script")[0]; 
		  s.parentNode.insertBefore(hm, s);
		})();
		</script>
	</head>
	<?php flush(); ?>
	<body data-spy="scroll" data-target=".scrollspy">
		<div id="kratos-wrapper">
			<div id="kratos-page">
				<div id="kratos-header">
					<header id="kratos-header-section">
						<div class="container">
							<div class="nav-header">
								<?php if ( has_nav_menu('header_menu') ) :?>
									<a href="#" class="js-kratos-nav-toggle kratos-nav-toggle"><i></i></a>
								<?php endif; ?>
								<?php $site_logo = kratos_option('site_logo');?>
								<?php if ( !empty( $site_logo ) ) {?>
									<a href="<?php echo get_option('home'); ?>">
									<h1 id="kratos-logo-img"><img src="<?php echo $site_logo; ?>"></h1>
									</a>
								<?php }else{?>
									<h1 id="kratos-logo"><a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a></h1>
								<?php }?>
								<?php $defaults = array('theme_location' => 'header_menu', 'container' => 'nav', 'container_id' => 'kratos-menu-wrap', 'menu_class' => 'sf-menu', 'menu_id' => 'kratos-primary-menu', ); ?>
							 <?php wp_nav_menu($defaults); ?>
							</div>
						</div>
					</header>
				</div>
