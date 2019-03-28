<?php
/**
 * The template for displaying 404 pages (not found)
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
get_header(); ?>
<div class="kratos-start">
	<div class="kratos-overlay"></div>
	<div class="kratos-cover kratos-topimg text-center" style="background-image: url('<?php echo kratos_option('error_image', get_template_directory_uri() . '/images/404.jpg'); ?>')">
		<div class="desc desc3">
			<h2><?php echo kratos_option('error_text1', '这里已经是废墟，什么东西都没有'); ?></h2>
			<span><?php echo kratos_option('error_text2', 'That page can not be found'); ?></span>
			<span><a href="<?php echo home_url(); ?>"><div class="btn btn-star">返回首页</div></a></span>
		</div>
	</div>
</div>
<?php get_footer(); ?>