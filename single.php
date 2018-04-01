<?php
/**
 * The template for displaying all single posts and attachments
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
$sidebar = kratos_option('side_bar');
$sidebar = (empty($sidebar)) ? 'right_side' : $sidebar;
get_header();
get_header('banner'); ?>
<div id="kratos-blog-post" style="background:<?php echo kratos_option('background_index_color'); ?>">
	<div class="container">
		<div class="row">
			<?php if($sidebar == 'left_side'){ ?>
			<aside id="kratos-widget-area" class="col-md-4 hidden-xs hidden-sm scrollspy">
                <div id="sidebar">
                    <?php dynamic_sidebar('sidebar_tool'); ?>
                </div>
            </aside>
			<?php } ?>
            <section id="main" class='<?php echo ($sidebar == 'single') ? 'col-md-12' : 'col-md-8'; ?>'>
				<?php if (have_posts()) : the_post(); update_post_caches($posts); ?>
				<article>
					<div class="kratos-hentry kratos-post-inner clearfix">
						<header class="kratos-entry-header">
							<h1 class="kratos-entry-title text-center"><?php the_title(); ?></h1>
							<div class="kratos-post-meta text-center">
								<span>
								<i class="fa fa-calendar"></i> <?php echo get_the_date(); ?>
				                <i class="fa fa-commenting-o"></i> <?php comments_number('0', '1', '%'); ?>条评论
				                <i class="fa fa-eye"></i> <?php echo kratos_get_post_views();?>次阅读
				                <i class="fa fa-thumbs-o-up"></i> <?php if( get_post_meta($post->ID,'love',true) ){ echo get_post_meta($post->ID,'love',true); } else { echo '0'; }?>人点赞
								</span>
							</div>
						</header>
						<div class="kratos-post-content">
						<?php if ( kratos_option('ad_show_1') ): ?>
							<a href="<?php echo kratos_option('ad_link_1'); ?>"><img src="<?php echo kratos_option('ad_img_1')?>"></a>
	                    <?php endif ?>
                        <?php the_content(); ?>
						<?php if ( kratos_option('ad_show_2') ): ?>
							<a href="<?php echo kratos_option('ad_link_2'); ?>"><img src="<?php echo kratos_option('ad_img_2')?>"></a>
	                    <?php endif ?>
						</div>
						<footer class="kratos-entry-footer clearfix">
							<div class="post-like-donate text-center clearfix" id="post-like-donate">
							<?php if ( kratos_option( 'post_like_donate' ) ) : ?>
				   			<a href="<?php echo kratos_option('donate_links'); ?>" class="Donate"><i class="fa fa-bitcoin"></i> 打赏</a>
				   			<?php endif; ?>
				   			<a href="javascript:;" id="btn" data-action="love" data-id="<?php the_ID(); ?>" class="Love <?php if(isset($_COOKIE['love_'.$post->ID])) echo 'done';?>" ><i class="fa fa-thumbs-o-up"></i> 点赞</a>
							<?php if ( kratos_option( 'post_share' ) ) : ?>
							<a href="javascript:;"  class="Share" ><i class="fa fa-share-alt"></i> 分享</a>
								<?php require_once( get_template_directory() . '/inc/share.php'); ?>
							<?php endif; ?>
				    		</div>
							<div class="footer-tag clearfix">
								<div class="pull-left">
								<i class="fa fa-tags"></i>
								<?php if ( get_the_tags() ) { the_tags('', ' ', ''); } else{ echo '<a>No Tag</a>';  }?>
								</div>
							</div>
						</footer>
					</div>
					<?php if ( kratos_option( 'post_cc' )==1 ) : ?>
					<div class="kratos-hentry kratos-copyright text-center clearfix">
						<img alt="知识共享许可协议" src="<?php echo get_template_directory_uri(); ?>/images/licenses.png">
						<h5>本作品采用 <a rel="license nofollow" target="_blank" href="http://creativecommons.org/licenses/by-sa/4.0/">知识共享署名-相同方式共享 4.0 国际许可协议</a> 进行许可</h5>
					</div>
					<?php endif; ?>
					<nav class="navigation post-navigation clearfix" role="navigation">
						<?php
						$prev_post = get_previous_post(TRUE);
						if(!empty($prev_post)):?>
						<div class="nav-previous clearfix">
							<a title="<?php echo $prev_post->post_title;?>" href="<?php echo get_permalink($prev_post->ID);?>">&lt; 上一篇</a>
						</div>
						<?php endif;?>
						<?php
						$next_post = get_next_post(TRUE);
						if(!empty($next_post)):?>
						<div class="nav-next">
							<a title="<?php echo $next_post->post_title;?>" href="<?php echo get_permalink($next_post->ID);?>">下一篇 &gt;</a>
						</div>
						<?php endif;?>
					</nav>
					<?php comments_template(); ?>
				</article>
				<?php endif; ?>
			</section>
			<?php if($sidebar == 'right_side'){ ?>
				<aside id="kratos-widget-area" class="col-md-4 hidden-xs hidden-sm scrollspy">
	                <div id="sidebar">
	                    <?php dynamic_sidebar('sidebar_tool'); ?>
	                </div>
	            </aside>
			<?php } ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>