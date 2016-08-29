<?php
/*
Template Name: 主页模板
*/
get_header();?>
<div class="kratos-start">
	<div class="kratos-overlay"></div>
	<div class="kratos-cover kratos-topimg text-center" id="kratos-topimg">
		<div class="desc animate-box">
			<h2><?php echo kratos_option('index_text1'); ?></h2>
			<span><?php echo kratos_option('index_text2'); ?></span>
			<span><div class="btn btn-star" id="top-Start">探索更多</div></span>
		</div>
	</div>
</div>
<!-- Kratos Start Over :-) -->
<div id="kratos-blog" class="kratos-page-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
				<h3><?php echo kratos_option('index_post1_title'); ?></h3>
				<p><?php echo kratos_option('index_post1_jj'); ?></p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php query_posts('showposts=6&cat='.kratos_option('index_post1_num').'')?> 
			<?php while (have_posts()) : the_post(); ?>
			<div class="col-md-4 col-sm-6 col-xs-6 col-xxs-12 animate-box">
				<div class="kratos-post ">
					<div class="kratos-post-image">
						<div class="kratos-overlay"></div>
						<div class="kratos-category"><a href="<?php the_permalink() ?>"><i class="fa fa-heart-o"></i> <?php if( get_post_meta($post->ID,'kratos_love',true) ){ echo get_post_meta($post->ID,'kratos_love',true); } else { echo '0'; }?></a>
						</div>
						<?php kratos_index_thumbnail() ?>
					</div>
					<div class="kratos-post-text">
						<a href="<?php the_permalink() ?>"><h3><?php the_title(); ?></h3></a>
						<?php $excerptphoto = wp_trim_words(get_the_excerpt(), 60); ?>
						<p><?php echo $excerptphoto ?></p>
					</div>
					<div class="kratos-post-meta text-center">
						<a href="#"><i class="fa fa-calendar"></i> <?php the_time('Y/n/j') ?></a>
						<?php comments_popup_link('<i class="fa fa-commenting-o"></i> 0 Comment', '<i class="fa fa-commenting-o"></i> 1 Comments', '<i class="fa fa-commenting-o"></i> % Comments', '', '<i class="fa fa-commenting-o"></i> 0 Comment'); ?>
						<a href="<?php the_permalink() ?>"><i class="fa fa-eye"></i> <?php echo kratos_get_post_views(); ?> Views</a>
					</div>
				</div>
			</div>
			<div class="clearfix visible-sm-block"></div>
			<?php endwhile;?>
		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center animate-box">
				<a href="#" class="btn btn-primary view-more-1">查看更多</a>
			</div>
		</div>
	</div>
</div>
<!-- Kratos Project1 Over :-) -->
<div id="kratos-photos" class="kratos-page-default">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
				<h3><?php echo kratos_option('index_post2_title'); ?></h3>
				<p><?php echo kratos_option('index_post2_jj'); ?></p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php query_posts('showposts=6&cat='.kratos_option('index_post2_num').'')?> 
			<?php while (have_posts()) : the_post(); ?>
			<div class="col-md-4 col-sm-6 col-xxs-12 animate-box">
				<a href="<?php the_permalink() ?>" class="kratos-photos-item image-popup">
					<?php kratos_index_thumbnail() ?>
					<div class="kratos-text">
						<h2><?php the_title(); ?></h2>
						<?php $excerptphoto = wp_trim_words(get_the_excerpt(), 30); ?>
						<p><?php echo $excerptphoto ?></p>
					</div>
				</a>
			</div>
			<?php endwhile;?>

		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center animate-box">
				<a href="#" class="btn btn-primary view-more-1">查看更多</a>
			</div>
		</div>
	</div>
</div>
<!-- Kratos Project2 Over :-) -->
<?php get_footer('subscribe'); ?>
<?php get_footer(); ?>