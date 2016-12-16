<?php
/**
 * The template for displaying the footer
 *
 * @package Vtrois
 * @version 2.4
 */
?>
				<footer>
					<div id="footer">
					    <a class="cd-top visible-lg text-center cd-is-visible cd-fade-out"><span class="fa fa-chevron-up"></span></a>
						<div class="container">
							<div class="row">
								<div class="col-md-6 col-md-offset-3 text-center">
									<div class="share-group">
										<div class="content top:50%">赠人玫瑰，手有余香</div>  
										<a href="javascript:;" class="share-plain weixin pop style-plain" rel="nofollow">
											<div class="icon-wrap">
												<i class="fa fa-weixin"></i>
											</div>
											<div class="share-int">
												<div class="qrcode" data-url="<?php echo "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>"></div>
												<div>请使用微信扫码分享</div>
											</div>
										</a>
										<a href="javascript:;" class="share-plain qzone style-plain" onclick="share('qzone');" rel="nofollow">
											<div class="icon-wrap">
												<i class="fa fa-qzone"></i>
											</div>
										</a>
										<a href="javascript:;" class="share-plain qqchat style-plain" onclick="share('qqchat');" rel="nofollow">
											<div class="icon-wrap">
												<i class="fa fa-qq"></i>
											</div>
										</a>
										<a href="javascript:;" class="share-plain facebook style-plain" onclick="share('facebook');" rel="nofollow">
											<div class="icon-wrap">
												<i class="fa fa-facebook"></i>
											</div>
										</a>
										<a href="javascript:;" class="share-plain twitter style-plain" onclick="share('twitter');" rel="nofollow">
											<div class="icon-wrap">
												<i class="fa fa-twitter"></i>
											</div>
										</a>
										<a href="javascript:;" class="share-plain googleplus style-plain" onclick="share('googleplus');" rel="nofollow">
											<div class="icon-wrap">
												<i class="fa fa-google-plus"></i>
											</div>
										</a>
										<a href="javascript:;" class="share-plain weibo" onclick="share('weibo');" rel="nofollow">
											<div class="icon-wrap">
												<i class="fa fa-weibo"></i>
											</div>
										</a>
									</div>
									<script type="text/javascript">
									function share(obj){
										var weiboShareURL="http://service.weibo.com/share/share.php?";
										var facebookShareURL="https://www.facebook.com/sharer/sharer.php?";
										var twitterShareURL="https://twitter.com/intent/tweet?";
										var googleplusShareURL="https://plus.google.com/share?";
										var qzoneShareURL="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?";
										var qqchatShareURL="http://connect.qq.com/widget/shareqq/index.html?";
										var host_url="<?php echo "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
										// var title="【<?php wp_title( '-', true, 'right' ); ?>】<?php echo get_the_excerpt(); ?>";
										var title="<?php wp_title( '-', true, 'right' ); ?>";
										var pic="https://www.elfive.cn/wp-content/uploads/2016/12/cropped-IMG_1098-1.jpg";
										var appkey="<?php echo kratos_option('sina_appkey'); ?>";
										var _URL;
										if(obj=="weibo"){
											_URL=weiboShareURL+"url="+host_url+"&appkey="+appkey+"&title="+title+"&pic="+pic;
										}else if(obj=="facebook"){
									 		_URL=facebookShareURL+"u="+host_url;
										}else if(obj=="twitter"){
									 		_URL=twitterShareURL+"text="+title+"&url="+host_url;
										}else if(obj=="googleplus"){
									 		_URL=googleplusShareURL+"url="+host_url;
										}else if(obj=="qzone"){
									 		_URL=qzoneShareURL+"url="+host_url+"&desc=&summary=elfive的个人小站&site=elfive.cn&pics="+pic;
									 	}else if(obj=="qqchat"){
									 		_URL=qqchatShareURL+"url="+host_url+"&desc=&summary=elfive的个人小站&site=elfive.cn&pic="+pic;
									 	}
										window.open(_URL);
									}
									</script>

 									<p class="kratos-social-icons">
									<?php echo (!kratos_option('social_facebook')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_facebook') . '"><i class="fa fa-facebook-official"></i></a>'; ?>
									<?php echo (!kratos_option('social_twitter')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_twitter') . '"><i class="fa fa-twitter"></i></a>'; ?>
									<?php echo (!kratos_option('social_github')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_github') . '"><i class="fa fa-github"></i></a>'; ?>
									<?php echo (!kratos_option('social_tweibo')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_tweibo') . '"><i class="fa fa-tencent-weibo"></i></a>'; ?>
									<?php echo (!kratos_option('social_linkedin')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_linkedin') . '"><i class="fa fa-linkedin-square"></i></a>'; ?>
									<?php echo (!kratos_option('social_weibo')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_weibo') . '"><i class="fa fa-weibo"></i></a>'; ?>
									</p>

									<p>Copyright 2016 <a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a>. All Rights Reserved.<br><a href="http://www.miitbeian.gov.cn/" rel="external nofollow" target="_blank"><?php if(!kratos_option('icp_num')){ echo get_option( 'zh_cn_l10n_zicp_num' ); } else { echo kratos_option( 'icp_num' ); }  ?></a><br><?php if(kratos_option('gov_num')){?><a href="<?php echo kratos_option( 'gov_link' ); ?>" rel="external nofollow" target="_blank"><i class="govimg"></i><?php echo kratos_option( 'gov_num' ); ?></a><?php }?></p><p><?php echo (!kratos_option('site_tongji')) ? '' : '<script>' . kratos_option('site_tongji') . '</script>'; ?></p>
								</div>
							</div>
						</div>
					</div>
				</footer>
			</div>
		</div>
		<?php wp_footer();?>
		<?php echo (!kratos_option('ad_code')) ? '' : '<script>' . kratos_option('ad_code') . '</script>'; ?>
		<?php if ( kratos_option('site_sa')==1 ) : ?>
		<script type="text/javascript">
			if ($("#main").height() > $("#sidebar").height()) {
				var footerHeight = 0;
				if ($('#page-footer').length > 0) {
					footerHeight = $('#page-footer').outerHeight(true);
				}
				$('#sidebar').affix({
					offset: {
						top: $('#sidebar').offset().top - 30,
						bottom: $('#footer').outerHeight(true) + footerHeight + 6
					}
				});
			}
		</script>
		<?php endif; ?>
	</body>
</html>