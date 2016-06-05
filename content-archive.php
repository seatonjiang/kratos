<article class="animate-box">
	<div class="kratos-hentry kratos-post-inner clearfix">
		<header class="kratos-entry-header">
			<h1 class="kratos-entry-title text-center"><?php the_title(); ?></h1>
			<div class="kratos-post-meta text-center">
				<span>
				<a href="#"><i class="fa fa-calendar"></i> <?php the_time('Y/n/j') ?></a>
				</span>
				<span>
				<?php $category = get_the_category(); echo '<a href="'.get_category_link($category[0]->term_id ).'"><i class="fa fa-folder-open-o"></i> '.$category[0]->cat_name.'</a>'; ?>
				<?php comments_popup_link('<i class="fa fa-commenting-o"></i> 0 Comment', '<i class="fa fa-commenting-o"></i> 1 Comments', '<i class="fa fa-commenting-o"></i> % Comments', '', '<i class="fa fa-commenting-o"></i> 0 Comment'); ?>
				</span>
				<span>
				<a href="<?php the_permalink() ?>"><i class="fa fa-eye"></i> <?php echo kratos_get_post_views();?> Views</a>
				</span>
			</div>
		</header>
		<div class="kratos-post-content"><?php the_content(); ?></div>
		<footer class="kratos-entry-footer">
			<div class="footer-tag clearfix">
				<div class="pull-left">
				<i class="fa fa-tags"></i>
				<?php if ( get_the_tags() ) { the_tags('', ' ', ''); } else{ echo '<a>No Tag</a>';  }?>
				</div>
		</div>					 
		</footer>
	</div>
	<div class="kratos-hentry kratos-copyright text-center clearfix">
		<img alt="知识共享许可协议" src="http://www.itfang.net/wp-content/themes/vtrois/images/licenses.png">
		<h5>本作品采用 <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">知识共享署名-相同方式共享 4.0 国际许可协议</a> 进行许可</h5>
	</div>
	<nav class="navigation post-navigation clearfix" role="navigation">
		<?php
		$prev_post = get_previous_post(TRUE);
		if(!empty($prev_post)):?>
		<div class="nav-previous clearfix">
			<a title="<?php echo $prev_post->post_title;?>" href="<?php echo get_permalink($prev_post->ID);?>">&lt; <?php echo $prev_post->post_title;?></a>
		</div>
		<?php endif;?>
		<?php
		$next_post = get_next_post(TRUE);
		if(!empty($next_post)):?>
		<div class="nav-next">
			<a title="<?php echo $next_post->post_title;?>" href="<?php echo get_permalink($next_post->ID);?>"><?php echo $next_post->post_title;?> &gt;</a>
		</div>
		<?php endif;?>
	</nav>
	<?php comments_template(); ?>
</article>
