<?php

/**
 * 主题页脚
 * @author Seaton Jiang <seatonjiang@vtrois.com>
 * @license GPL-3.0 License
 * @version 2021.08.20
 */
?>
<div class="k-footer">
    <div class="f-toolbox">
        <div class="gotop <?php if (kratos_option('g_wechat', false, 'g_wechat_fieldset')) {
                                echo 'gotop-haswechat';
                            } ?>">
            <div class="gotop-btn">
                <span class="kicon i-up"></span>
            </div>
        </div>
        <?php if (kratos_option('g_wechat', false, 'g_wechat_fieldset')) { ?>
            <div class="wechat">
                <span class="kicon i-wechat"></span>
                <div class="wechat-pic">
                    <img src="<?php echo kratos_option('g_wechat_url', ASSET_PATH . '/assets/img/200.png', 'g_wechat_fieldset'); ?>">
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
                    $socials = array('s_sina_url', 's_bilibili_url', 's_douban_url', 's_coding_url', 's_gitee_url', 's_twitter_url', 's_telegram_url', 's_linkedin_url', 's_youtube_url', 's_github_url', 's_stackflow_url', 's_email_url');
                    foreach ($socials as $value) {
                        if (kratos_option($value, '', 's_social_fieldset')) {
                            echo '<a target="_blank" rel="nofollow" href="' . kratos_option($value, '', 's_social_fieldset') . '"><i class="kicon i-' . str_replace(array("s_", "_url"), array('', ''), $value) . '"></i></a>';
                        }
                    }
                    ?>
                </p>
                <?php
                $sitename = get_bloginfo('name');
                echo '<p>' . kratos_option('s_copyright', 'COPYRIGHT © ' . date('Y') . ' ' . $sitename . '. ALL RIGHTS RESERVED.') . '</p>';
                echo '<p>THEME <a href="https://github.com/vtrois/kratos" target="_blank" rel="nofollow">KRATOS</a> MADE BY <a href="https://www.vtrois.com/" target="_blank" rel="nofollow">VTROIS</a></p>';
                if (kratos_option('s_icp')) {
                    echo '<p><a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow">' . kratos_option('s_icp') . '</a></p>';
                }
                if (kratos_option('s_gov')) {
                    echo '<p><a href="' . kratos_option('s_gov_link', '#') . '" target="_blank" rel="nofollow" ><i class="police-ico"></i>' . kratos_option('s_gov') . '</a></p>';
                }
                if (kratos_option('seo_statistical')) {
                    echo kratos_option('seo_statistical');
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>

</html>