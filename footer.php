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
                <?php
                echo '<p>' . kratos_option('s_copyright', 'COPYRIGHT © ' . wp_date('Y') . ' ' . get_bloginfo('name') . '. ALL RIGHTS RESERVED.') . '</p>';
                echo '<p>Theme <a href="https://github.com/seatonjiang/kratos" target="_blank" rel="nofollow">Kratos</a> Made By <a href="https://seatonjiang.com" target="_blank" rel="nofollow">Seaton Jiang</a></p>';
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
<?php wp_footer(); ?>
</body>

</html>