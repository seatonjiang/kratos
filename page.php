<?php
/*
Template Name: 默认模版
*/
get_header();
get_header('banner'); ?>
<div id="kratos-blog-post" class="kratos-page-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
		<?php while ( have_posts() ) : the_post(); ?>
			<article class="animate-box">
				<div class="kratos-hentry kratos-post-inner clearfix">
					<header class="kratos-entry-header">
						<h1 class="kratos-entry-title text-center"><?php the_title(); ?></h1>
					</header>
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