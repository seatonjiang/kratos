<?php get_header(); ?>
<?php get_header('banner'); ?>
<div id="kratos-blog-post" class="kratos-page-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
							<?php
				if(is_home()){
					kratos_banner();
				}elseif(is_category()){
			?>
				<div class="kratos-hentry clearfix animate-box">
					<h1 class="kratos-post-header-title">分类目录：<?php echo single_cat_title('', false); ?></h1>
				</div>				
			<?php
				}elseif(is_date()){
			?>	
			<?php
				}elseif(is_tag()){
			?>
				<div class="kratos-hentry clearfix animate-box">
					<h1 class="kratos-post-header-title">标签目录：<?php echo single_cat_title('', false); ?></h1>
				</div>
			<?php
				}elseif(is_search()){
			?>
				<div class="kratos-hentry clearfix animate-box">
					<h1 class="kratos-post-header-title">搜索结果：<?php the_search_query(); ?></h1>
				</div>				
			<?php
				}
			?>
            <?php
				if ( have_posts() ) {
					while ( have_posts() ){
						the_post();
						get_template_part('content', get_post_format());
					}
				}else{
			?>
			<div class="kratos-hentry clearfix animate-box">
					<h1 class="kratos-post-header-title">很抱歉，没有找到任何内容。</h1>
			</div>
			<?php } ?>

        		<?php kratos_pages(3);?>
				<?php wp_reset_query(); ?>
				</div>
			<div id="kratos-widget-area" class="col-md-4 hidden-xs hidden-sm">
				<?php dynamic_sidebar('sidebar_blog'); ?>
			</div>
		</div>
	</div>
</div>
<?php get_footer('subscribe'); ?>
<?php get_footer(); ?>