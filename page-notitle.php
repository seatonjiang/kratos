<?php
/*
Template Name: 无标题模版
*/
get_header();
get_header('banner'); ?>
<div id="kratos-blog-post" style="background:<?php echo kratos_option('background_index_color'); ?>">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
		<?php while ( have_posts() ) : the_post(); ?>
			<article>
				<div class="kratos-hentry kratos-post-inner clearfix">
					<div class="kratos-post-content"><?php the_content(); ?></div>
				</div>
				<?php comments_template(); ?>
			</article>
		<?php endwhile;?>
			</div>
			<div id="kratos-widget-area" class="col-md-4 hidden-xs hidden-sm">
				<?php dynamic_sidebar('sidebar_blog'); ?>
			</div>
		</div>
	</div>
</div>
<?php get_footer('subscribe'); ?>
<?php get_footer(); ?>