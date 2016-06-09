				<footer>
					<div id="footer">
						<div class="container">
							<div class="row">
								<div class="col-md-6 col-md-offset-3 text-center">
									<p class="kratos-social-icons">
									<?php echo (!kratos_option('social_weibo')) ? '' : '<a target="_blank" href="' . kratos_option('social_weibo') . '"><i class="fa fa-weibo"></i></a>'; ?>
									<?php echo (!kratos_option('social_tweibo')) ? '' : '<a target="_blank" href="' . kratos_option('social_tweibo') . '"><i class="fa fa-tencent-weibo"></i></a>'; ?>
									<?php echo (!kratos_option('social_twitter')) ? '' : '<a target="_blank" href="' . kratos_option('social_twitter') . '"><i class="fa fa-twitter"></i></a>'; ?>
									<?php echo (!kratos_option('social_facebook')) ? '' : '<a target="_blank" href="' . kratos_option('social_facebook') . '"><i class="fa fa-facebook-official"></i></a>'; ?>
									<?php echo (!kratos_option('social_linkedin')) ? '' : '<a target="_blank" href="' . kratos_option('social_linkedin') . '"><i class="fa fa-linkedin-square"></i></a>'; ?>
									<?php echo (!kratos_option('social_github')) ? '' : '<a target="_blank" href="' . kratos_option('social_github') . '"><i class="fa fa-github-alt"></i></a>'; ?>
									</p>
									<p>Copyright 2016 <a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a>. All Rights Reserved.<br>Theme: <a href="https://github.com/vtrois/kratos" target="_blank">Kratos</a> made by <a href="http://www.vtrois.com/" target="_blank" >Vtrois</a><br><a href="http://www.miitbeian.gov.cn/" rel="external nofollow" target="_blank"><?php echo get_option( 'zh_cn_l10n_icp_num' );?></a><?php echo (!kratos_option('site_tongji')) ? '' : '<script ' . kratos_option('site_tongji') . '</script>'; ?></p>
								</div>
							</div>
						</div>
					</div>
				</footer>
			</div>
		</div>
		<?php wp_footer();?>
	</body>
</html>