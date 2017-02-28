<?php
/*
Template Name: 首页模版
*/
get_header(); ?>
<div class="kratos-start">
	<div class="kratos-overlay"></div>
	<div class="kratos-cover kratos-topimg text-center" style="background-image: url(<?php echo kratos_option('krsort_hm_img'); ?>)">
		<div class="desc animate-box">
			<h2><?php echo kratos_option('krsort_hm_tx1'); ?></h2>
			<span><?php echo kratos_option('krsort_hm_tx2'); ?></span>
			<span><b class="btn btn-star" id="top-Start">探索更多</b></span>
		</div>
	</div>
</div>
<div id="kratos-blog" class="kratos-page-default">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
				<h3><?php echo kratos_option('krsort_hm_tx3'); ?></h3>
				<p><?php echo kratos_option('krsort_hm_tx4'); ?></p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php query_posts('showposts=3&cat=' . kratos_option('krsort_hm_bk1') . '')?> 
			<?php while (have_posts()) : the_post(); ?>
			<div class="col-md-4 col-sm-6 col-xs-6 col-xxs-12 animate-box">
				<div class="kratos-post ">
					<div class="kratos-post-image">
						<div class="kratos-overlay"></div>
						<div class="kratos-category"><a href="<?php the_permalink() ?>"><i class="fa fa-heart-o"></i> <?php if( get_post_meta($post->ID,'love',true) ){ echo get_post_meta($post->ID,'love',true); } else { echo '0'; }?></a>
						</div>
						<?php kratos_blog_thumbnail_new() ?>
					</div>
					<div class="kratos-post-text">
						<a href="<?php the_permalink() ?>"><h3 class="text-center"><?php the_title(); ?></h3></a>
						<?php $excerptphoto = wp_trim_words(get_the_excerpt(), 60); ?>
						<p><?php echo $excerptphoto ?></p>
					</div>
				</div>
			</div>
			<div class="clearfix visible-sm-block"></div>
			<?php endwhile;?>
		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center animate-box">
				<a href="<?php echo kratos_option('krsort_hm_tx5'); ?>" class="btn btn-primary view-more-1">查看更多</a>
			</div>
		</div>
	</div>
</div>
<div id="kratos-blog" class="kratos-page-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
				<h3><?php echo kratos_option('krsort_hm_tx6'); ?></h3>
				<p><?php echo kratos_option('krsort_hm_tx7'); ?></p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php query_posts('showposts=3&cat=' . kratos_option('krsort_hm_bk2') . '')?> 
			<?php while (have_posts()) : the_post(); ?>
			<div class="col-md-4 col-sm-6 col-xs-6 col-xxs-12 animate-box">
				<div class="kratos-post ">
					<div class="kratos-post-image">
						<div class="kratos-overlay"></div>
						<div class="kratos-category"><a href="<?php the_permalink() ?>"><i class="fa fa-heart-o"></i> <?php if( get_post_meta($post->ID,'love',true) ){ echo get_post_meta($post->ID,'love',true); } else { echo '0'; }?></a>
						</div>
						<?php kratos_blog_thumbnail_new() ?>
					</div>
					<div class="kratos-post-text">
						<a href="<?php the_permalink() ?>"><h3 class="text-center"><?php the_title(); ?></h3></a>
						<?php $excerptphoto = wp_trim_words(get_the_excerpt(), 60); ?>
						<p><?php echo $excerptphoto ?></p>
					</div>
				</div>
			</div>
			<div class="clearfix visible-sm-block"></div>
			<?php endwhile;?>
		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center animate-box">
				<a href="<?php echo kratos_option('krsort_hm_tx8'); ?>" class="btn btn-primary view-more-1">查看更多</a>
			</div>
		</div>
	</div>
</div>
<div id="kratos-blog" class="kratos-page-default">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
				<h3><?php echo kratos_option('krsort_hm_tx9'); ?></h3>
				<p><?php echo kratos_option('krsort_hm_tx10'); ?></p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php query_posts('showposts=3&cat=' . kratos_option('krsort_hm_bk3') . '')?> 
			<?php while (have_posts()) : the_post(); ?>
			<div class="col-md-4 col-sm-6 col-xs-6 col-xxs-12 animate-box">
				<div class="kratos-post ">
					<div class="kratos-post-image">
						<div class="kratos-overlay"></div>
						<div class="kratos-category"><a href="<?php the_permalink() ?>"><i class="fa fa-heart-o"></i> <?php if( get_post_meta($post->ID,'love',true) ){ echo get_post_meta($post->ID,'love',true); } else { echo '0'; }?></a>
						</div>
						<?php kratos_blog_thumbnail_new() ?>
					</div>
					<div class="kratos-post-text">
						<a href="<?php the_permalink() ?>"><h3 class="text-center"><?php the_title(); ?></h3></a>
						<?php $excerptphoto = wp_trim_words(get_the_excerpt(), 60); ?>
						<p><?php echo $excerptphoto ?></p>
					</div>
				</div>
			</div>
			<div class="clearfix visible-sm-block"></div>
			<?php endwhile;?>
		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center animate-box">
				<a href="<?php echo kratos_option('krsort_hm_tx11'); ?>" class="btn btn-primary view-more-1">查看更多</a>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>