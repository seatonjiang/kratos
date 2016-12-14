<?php
/*
Template Name: 无标题模版
*/
$page_side_bar = kratos_option('page_side_bar');
$page_side_bar = (empty($page_side_bar)) ? 'right_side' : $page_side_bar;
get_header();
get_header('banner'); ?>
<div id="kratos-blog-post" style="background:<?php echo kratos_option('background_index_color'); ?>">
	<div class="container">
		<div class="row">
			<?php if($page_side_bar == 'left_side'){ ?>
				<aside id="kratos-widget-area" class="col-md-4 hidden-xs hidden-sm scrollspy">
	                <div id="sidebar">
	                    <?php dynamic_sidebar('sidebar_tool'); ?>
	                </div>
	            </aside>
			<?php } ?>
            <section id="main" class='<?php echo ($page_side_bar == 'single') ? 'col-md-12' : 'col-md-8'; ?>'>
			<?php while ( have_posts() ) : the_post(); ?>
				<article>
					<div class="kratos-hentry kratos-post-inner clearfix">
						<div class="kratos-post-content"><?php the_content(); ?></div>
						<?php if(kratos_option('page_like_donate')||kratos_option('page_share')) {?>
						<footer class="kratos-entry-footer clearfix">
								<div class="post-like-donate text-center clearfix" id="post-like-donate">
								<?php if ( kratos_option( 'page_like_donate' )==1 ) : ?>
					   			<a href="<?php echo kratos_option('donate_links'); ?>" class="Donate"><i class="fa fa-bitcoin"></i> 打赏</a>
					   			<?php endif; ?>
								<?php if ( kratos_option( 'page_share' )==1 ) : ?>
								<a href="javascript:;"  class="Share" ><i class="fa fa-share-alt"></i> 分享</a>
								<div class="share-wrap" style="display: none;">
								<div class="share-group">
									<a href="javascript:;" class="share-plain twitter" onclick="share('qq');" rel="nofollow">
										<div class="icon-wrap">
											<i class="fa fa-qq"></i>
										</div>
									</a>
									<a href="javascript:;" class="share-plain weibo" onclick="share('weibo');" rel="nofollow">
										<div class="icon-wrap">
											<i class="fa fa-weibo"></i>
										</div>
									</a>
									<a href="javascript:;" class="share-plain facebook style-plain" onclick="share('facebook');" rel="nofollow">
										<div class="icon-wrap">
											<i class="fa fa-facebook"></i>
										</div>
									</a>
									<a href="javascript:;" class="share-plain googleplus style-plain" onclick="share('googleplus');" rel="nofollow">
										<div class="icon-wrap">
											<i class="fa fa-google-plus"></i>
										</div>
									</a>
									<a href="javascript:;" class="share-plain weixin pop style-plain" rel="nofollow">
										<div class="icon-wrap">
											<i class="fa fa-weixin"></i>
										</div>
										<div class="share-int">
											<div class="qrcode" data-url="<?php the_permalink() ?>"></div>
											<p>打开微信“扫一扫”，打开网页后点击屏幕右上角分享按钮</p>
										</div>
									</a>
								</div>
								<script type="text/javascript">
								function share(obj){
									var qqShareURL="http://connect.qq.com/widget/shareqq/index.html?";
									var weiboShareURL="http://service.weibo.com/share/share.php?";
									var facebookShareURL="https://www.facebook.com/sharer/sharer.php?";
									var twitterShareURL="https://twitter.com/intent/tweet?";
									var googleplusShareURL="https://plus.google.com/share?";
									var host_url="<?php the_permalink(); ?>";
									var title="【<?php the_title(); ?>】";
									var qqtitle="<?php the_title(); ?>";
									var excerpt="<?php echo get_the_excerpt(); ?>";
									var pic="<?php echo share_post_image(); ?>";
									var appkey="<?php echo kratos_option('sina_appkey'); ?>";
									var _URL;
									if(obj=="qq"){
										_URL=qqShareURL+"url="+host_url+"&title="+qqtitle+"&pics="+pic+"&desc=&summary="+excerpt+"&site=vtrois";
									}else if(obj=="weibo"){
										_URL=weiboShareURL+"url="+host_url+"&appkey="+appkey+"&title="+title+excerpt+"&pic="+pic;
									}else if(obj=="facebook"){
								 		_URL=facebookShareURL+"u="+host_url;
									}else if(obj=="twitter"){
								 		_URL=twitterShareURL+"text="+title+excerpt+"&url="+host_url;
									}else if(obj=="googleplus"){
								 		_URL=googleplusShareURL+"url="+host_url;
									}
									window.open(_URL);
								}
								</script>
								</div>
								<?php endif; ?>
					    		</div>
						</footer>
						<?php }?>
					</div>
						<?php if ( kratos_option( 'page_cc' )==1 ) : ?>
						<div class="kratos-hentry kratos-copyright text-center clearfix">
							<img alt="知识共享许可协议" src="<?php echo get_template_directory_uri(); ?>/images/licenses.png">
							<h5>本作品采用 <a rel="license nofollow" target="_blank" href="http://creativecommons.org/licenses/by-sa/4.0/">知识共享署名-相同方式共享 4.0 国际许可协议</a> 进行许可</h5>
						</div>
						<?php endif; ?>
						<?php comments_template(); ?>
				</article>
			<?php endwhile;?>
			</section>
			<?php if($page_side_bar == 'right_side'){ ?>
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