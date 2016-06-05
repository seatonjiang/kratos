<?php get_header(); ?>
<div class="kratos-start">
	<div class="kratos-overlay"></div>
	<div class="kratos-cover kratos-topimg text-center" style="background-image: url('<?php echo get_template_directory_uri(); ?>/images/404.jpg')">
		<div class="desc desc3 animate-box">
			<h2>这里已经是废墟，什么东西都没有</h2>
			<span>That page can’t be found</span>
			<span><a href="javascript:history.go(-1)"><div class="btn btn-star">返回上级</div></a></span>
		</div>
	</div>
</div>
<?php get_footer('subscribe'); ?>
<?php get_footer(); ?>