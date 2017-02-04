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
						<div class="cd-tool visible-lg text-center">
							<?php if ( kratos_option( 'cd_gb' )==1 && kratos_option( 'cd_weixin' )==1 ) { ?>
						   		<a rel="nofollow" class="cd-gb-a" href="<?php echo kratos_option('guestbook_links'); ?>"><span class="fa fa-book"></span></a>	
						   	<?php } elseif( kratos_option( 'cd_gb' )==1 && kratos_option( 'cd_weixin' )==0 ){ ?>
						   		<a rel="nofollow" class="cd-gb-b" href="<?php echo kratos_option('guestbook_links'); ?>"><span class="fa fa-book"></span></a>	
						   	<?php } ?>
						   	<?php if ( kratos_option( 'cd_weixin' )==1 ) : ?>
						   		<a id="weixin-img" class="cd-weixin"><span class="fa fa-weixin"></span><div id="weixin-pic"><img src="<?php echo kratos_option('weixin_image') ?>"></div></a>
						   	<?php endif; ?>
						    <a class="cd-top cd-is-visible cd-fade-out"><span class="fa fa-chevron-up"></span></a>
						</div>
						<div class="container">
							<div class="row">
								<div class="col-md-6 col-md-offset-3 footer-list text-center">
									<p class="kratos-social-icons">
									<?php echo (!kratos_option('social_weibo')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_weibo') . '"><i class="fa fa-weibo"></i></a>'; ?>
									<?php echo (!kratos_option('social_tweibo')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_tweibo') . '"><i class="fa fa-tencent-weibo"></i></a>'; ?>
									<?php echo (!kratos_option('social_twitter')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_twitter') . '"><i class="fa fa-twitter"></i></a>'; ?>
									<?php echo (!kratos_option('social_facebook')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_facebook') . '"><i class="fa fa-facebook-official"></i></a>'; ?>
									<?php echo (!kratos_option('social_linkedin')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_linkedin') . '"><i class="fa fa-linkedin-square"></i></a>'; ?>
									<?php echo (!kratos_option('social_github')) ? '' : '<a target="_blank" rel="nofollow" href="' . kratos_option('social_github') . '"><i class="fa fa-github"></i></a>'; ?>
									</p>
									<p>Copyright 2017 <a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a>. All Rights Reserved.<br>Theme <a href="https://github.com/vtrois/kratos" target="_blank" rel="nofollow">Kratos</a> made by <a href="https://www.vtrois.com/" target="_blank" rel="nofollow">Vtrois</a>
									<?php if(kratos_option('icp_num')){?><br><a href="http://www.miitbeian.gov.cn/" rel="external nofollow" target="_blank"><?php echo kratos_option( 'icp_num' ); } ?></a><?php if(kratos_option('gov_num')){?><br><a href="<?php echo kratos_option( 'gov_link' ); ?>" rel="external nofollow" target="_blank"><i class="govimg"></i><?php echo kratos_option( 'gov_num' ); ?></a><?php }?></p><p><?php echo (!kratos_option('site_tongji')) ? '' : '<script>' . kratos_option('site_tongji') . '</script>'; ?></p>
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