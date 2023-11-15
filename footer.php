<?php

/**
 * 主题页脚
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.05.27
 */
?>
<div class="k-footer">
    <div class="f-toolbox">
        <div class="gotop <?php echo kratos_option('g_wechat_fieldset')['g_wechat'] ? 'gotop-haswechat' : ''; ?>">
            <div class="gotop-btn">
                <span class="kicon i-up"></span>
            </div>
        </div>
		<?php if (!empty(kratos_option('g_wechat_fieldset')['g_wechat'])) { ?>
            <div class="wechat">
                <span class="kicon i-wechat"></span>
                <div class="wechat-pic">
                    <img src="<?php echo kratos_option('g_wechat_fieldset')['g_wechat_img']; ?>">
                </div>
            </div>
		<?php } ?>
        <div class="search">
            <span class="kicon i-find"></span>
            <form class="search-form" role="search" method="get" action="<?php echo home_url('/'); ?>">
                <input type="text" name="s" id="search-footer" placeholder="<?php _e('搜点什么呢?', 'kratos'); ?>" style="display:none" />
            </form>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <p class="social">
					<?php
					if (!empty(kratos_option('s_social_fieldset'))) {
						foreach (kratos_option('s_social_fieldset') as $key => $value) {
							if (kratos_option('s_social_fieldset')[$key]) {
								echo '<a target="_blank" rel="nofollow" href="' . kratos_option('s_social_fieldset')[$key] . '"><i class="kicon i-' . str_replace(array("s_", "_url"), array('', ''), $key) . '"></i></a>';
							}
						}
					}
					?>
                </p>
                <p class="social">
					<?php
					$bookmarks = get_bookmarks([
						'orderby' => 'name',
						'order' => 'ASC'
					]);
					foreach ($bookmarks as $bookmark) {
						echo '<a href="' . esc_url($bookmark->link_url) . '">' . esc_html($bookmark->link_name) . '</a>';
					}
					?>
                </p>
				<?php
				if (kratos_option('s_icp')) {
					echo '<p><a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow">' . kratos_option('s_icp') . '</a></p>';
				}
				if (kratos_option('g_performance')) {
					echo '<p>'. sprintf( '请求次数：%d 次，加载用时：%.3f 秒，内存占用：%.2f MB', get_num_queries(), timer_stop(), memory_get_peak_usage() / 1024 / 1024 ) . '</p>';
				}
				echo '<p>' . kratos_option('s_copyright', 'COPYRIGHT © ' . wp_date('Y') . ' ' . get_bloginfo('name') . '. ALL RIGHTS RESERVED.') . '</p>';
				if (kratos_option('s_icp')) {
					echo '<p><a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow">' . kratos_option('s_icp') . '</a></p>';
				}
				if (kratos_option('s_gov')) {
					echo '<p><a href="' . kratos_option('s_gov_link') . '" target="_blank" rel="nofollow" ><i class="police-ico"></i>' . kratos_option('s_gov') . '</a></p>';
				}
				?>
            </div>
        </div>
    </div>
</div>

<?php if (kratos_option('g_pjax')): ?>
<div class='loader'>
    <div class='loader_overlay'></div>
    <div class='loader_cogs'>
        <div class='loader_cogs__top'>
            <div class='top_part'></div>
            <div class='top_part'></div>
            <div class='top_part'></div>
            <div class='top_hole'></div>
        </div>
        <div class='loader_cogs__left'>
            <div class='left_part'></div>
            <div class='left_part'></div>
            <div class='left_part'></div>
            <div class='left_hole'></div>
        </div>
        <div class='loader_cogs__bottom'>
            <div class='bottom_part'></div>
            <div class='bottom_part'></div>
            <div class='bottom_part'></div>
            <div class='bottom_hole'></div>
        </div>
        <p>Loading...</p>
    </div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>

<?php if (kratos_option('g_pjax')): ?>
    <script>
        let myhkTipsTime = null;
        let myhkTips = {
            show: function (a) {
                clearTimeout(myhkTipsTime);
                jQuery("#myhkTips")["text"](a)["addClass"]("show");
                this["hide"]();
            },
            hide: function () {
                myhkTipsTime = setTimeout(function () {
                    jQuery("#myhkTips")["removeClass"]("show");
                }, 3000);
            }
        };

        +(function ($) {
            let pjax_container = '#pjax',
                pjax_timeout = 15000
            $(document).pjax('a[target!=_blank]', pjax_container, {
                fragment: pjax_container,
                timeout: pjax_timeout
            })
            $(document).on('submit', 'form.search-form', function (event) {
                $.pjax.submit(event, pjax_container, {
                    fragment: pjax_container,
                    timeout: pjax_timeout
                })
            })
            $(document).on('pjax:send', function () {
                $(".loader").css("display", "block")
            })
            $(document).on('pjax:complete', function (xhr, textStatus, options) {
                $(".loader").css("display", "none")
                if ("undefined" != typeof myhkplayer) {
                    myhkTips.show(document.title + " 加载完成")
                }
            })
        })(jQuery);
    </script>
<?php endif; ?>

<?php if (kratos_option('myhk_player_fieldset')['myhk_player'] ?? false): ?>
    <script defer src="https://myhkw.cn/player/js/player.js" id="myhk" key="<?php echo kratos_option('myhk_player_fieldset')['myhk_player_key']; ?>" m="1"></script>
<?php endif; ?>

</body>

</html>