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

<?php if (kratos_option('g_pjax', true)): ?>
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
          console.clear()
          $.getScript("<?php echo get_stylesheet_directory_uri() ?>/assets/js/kratos.js", function () {
            afterPjax()
          })
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

<?php if (kratos_option('g_l2d')): ?>
    <div class="l2d_xb" data-api="<?php echo get_stylesheet_directory_uri() ?>/2233">
        <div class="waifu">
            <div class="waifu-tips"></div>
            <canvas id="live2d" width="220" height="250" class="live2d"></canvas>
            <div class="waifu-tool">
                <span class="home"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z"></path></svg></span>
                <span class="comments"><svg width="19" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M416 192c0-88.4-93.1-160-208-160S0 103.6 0 192c0 34.3 14.1 65.9 38 92-13.4 30.2-35.5 54.2-35.8 54.5-2.2 2.3-2.8 5.7-1.5 8.7S4.8 352 8 352c36.6 0 66.9-12.3 88.7-25 32.2 15.7 70.3 25 111.3 25 114.9 0 208-71.6 208-160zm122 220c23.9-26 38-57.7 38-92 0-66.9-53.5-124.2-129.3-148.1.9 6.6 1.3 13.3 1.3 20.1 0 105.9-107.7 192-240 192-10.8 0-21.3-.8-31.7-1.9C207.8 439.6 281.8 480 368 480c41 0 79.1-9.2 111.3-25 21.8 12.7 52.1 25 88.7 25 3.2 0 6.1-1.9 7.3-4.8 1.3-2.9.7-6.3-1.5-8.7-.3-.3-22.4-24.2-35.8-54.5z"></path></svg></span>
                <span class="drivers-license-o"><svg width="18" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M528 32H48C21.5 32 0 53.5 0 80v16h576V80c0-26.5-21.5-48-48-48zM0 432c0 26.5 21.5 48 48 48h480c26.5 0 48-21.5 48-48V128H0v304zm352-232c0-4.4 3.6-8 8-8h144c4.4 0 8 3.6 8 8v16c0 4.4-3.6 8-8 8H360c-4.4 0-8-3.6-8-8v-16zm0 64c0-4.4 3.6-8 8-8h144c4.4 0 8 3.6 8 8v16c0 4.4-3.6 8-8 8H360c-4.4 0-8-3.6-8-8v-16zm0 64c0-4.4 3.6-8 8-8h144c4.4 0 8 3.6 8 8v16c0 4.4-3.6 8-8 8H360c-4.4 0-8-3.6-8-8v-16zM176 192c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zM67.1 396.2C75.5 370.5 99.6 352 128 352h8.2c12.3 5.1 25.7 8 39.8 8s27.6-2.9 39.8-8h8.2c28.4 0 52.5 18.5 60.9 44.2 3.2 9.9-5.2 19.8-15.6 19.8H82.7c-10.4 0-18.8-10-15.6-19.8z"></path></svg></span>
                <span class="street-view"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M367.9 329.76c-4.62 5.3-9.78 10.1-15.9 13.65v22.94c66.52 9.34 112 28.05 112 49.65 0 30.93-93.12 56-208 56S48 446.93 48 416c0-21.6 45.48-40.3 112-49.65v-22.94c-6.12-3.55-11.28-8.35-15.9-13.65C58.87 345.34 0 378.05 0 416c0 53.02 114.62 96 256 96s256-42.98 256-96c0-37.95-58.87-70.66-144.1-86.24zM256 128c35.35 0 64-28.65 64-64S291.35 0 256 0s-64 28.65-64 64 28.65 64 64 64zm-64 192v96c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-96c17.67 0 32-14.33 32-32v-96c0-26.51-21.49-48-48-48h-11.8c-11.07 5.03-23.26 8-36.2 8s-25.13-2.97-36.2-8H208c-26.51 0-48 21.49-48 48v96c0 17.67 14.33 32 32 32z"></path></svg></span>
                <span class="camera"><svg width="19" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M512 144v288c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48h88l12.3-32.9c7-18.7 24.9-31.1 44.9-31.1h125.5c20 0 37.9 12.4 44.9 31.1L376 96h88c26.5 0 48 21.5 48 48zM376 288c0-66.2-53.8-120-120-120s-120 53.8-120 120 53.8 120 120 120 120-53.8 120-120zm-32 0c0 48.5-39.5 88-88 88s-88-39.5-88-88 39.5-88 88-88 88 39.5 88 88z"></path></svg></span>
                <span class="info-circle"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path></svg></span>
                <span class="close"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"></path></svg></span>
            </div>
        </div>
    </div>
    <link rel='stylesheet' id='waifu-css' href='<?php echo get_stylesheet_directory_uri() ?>/2233/css/waifu.min.css?ver=1.7' type='text/css' media='all' />
    <script defer src="<?php echo get_stylesheet_directory_uri() ?>/2233/js/live2d.js?ver=l2d"></script>
    <script defer src="<?php echo get_stylesheet_directory_uri() ?>/2233/js/waifu-tips.js?ver=1.7"></script>
<?php endif; ?>

</body>

</html>