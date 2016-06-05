<?php get_header(); ?>
<?php get_header('banner'); ?>
<div id="kratos-blog-post" class="kratos-page-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<?php if (have_posts()) : the_post(); update_post_caches($posts);
				?>
				<?php get_template_part('content','archive'); ?>
				<?php endif; ?>
			</div>
			<div id="kratos-widget-area" class="col-md-4 hidden-xs hidden-sm">
				<?php dynamic_sidebar('sidebar_blog'); ?>
			</div>
		</div>
	</div>
</div>
<?php get_footer('subscribe'); ?>
<?php get_footer(); ?>