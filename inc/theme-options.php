<?php
/**
 * 主题选项
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.04.12
 */

function getrobots()
{
    $site_url = parse_url(site_url());
    $path = (!empty($site_url['path'])) ? $site_url['path'] : '';

    $robots = "User-agent: *\n\n";
    $robots .= "Disallow: $path/wp-admin/\n";
    $robots .= "Disallow: $path/wp-includes/\n";
    $robots .= "Disallow: $path/wp-content/plugins/\n";
    $robots .= "Disallow: $path/wp-content/themes/\n";

    return $robots;
}

function kratos_options()
{
    $sitename = get_bloginfo('name');

    $seorobots = '<a href="' . home_url() . '/robots.txt" target="_blank">robots.txt</a>';
    $seoreading = '<a href="' . admin_url('options-reading.php') . '" target="_blank">' . __('设置-阅读-对搜索引擎的可见性', 'kratos') . '</a>';

    $cc_array = array(
        'one' => __('知识共享署名 4.0 国际许可协议', 'kratos'),
        'two' => __('知识共享署名-非商业性使用 4.0 国际许可协议', 'kratos'),
        'three' => __('知识共享署名-禁止演绎 4.0 国际许可协议', 'kratos'),
        'four' => __('知识共享署名-非商业性使用-禁止演绎 4.0 国际许可协议', 'kratos'),
        'five' => __('知识共享署名-相同方式共享 4.0 国际许可协议', 'kratos'),
        'six' => __('知识共享署名-非商业性使用-相同方式共享 4.0 国际许可协议', 'kratos'),
    );

	$top_array = array(
		'banner' => __( '图片导航', 'kratos' ),
		'color' => __( '颜色导航', 'kratos' ),
	);

	$cdn_array = array(
        'maocloud' => __( '猫云', 'kratos' ),
		'jsdelivr' => __( 'jsDelivr', 'kratos' ),
	);

    $options = array();

    $options[] = array(
        'name' => __('全站配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('图片 Logo', 'kratos'),
        'desc' => __('不选择图片则显示文字标题', 'kratos'),
        'id' => 'g_logo',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('Favicon 图片', 'kratos'),
        'desc' => __('浏览器收藏夹和地址栏中显示的图标', 'kratos'),
        'id' => 'g_icon',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('背景颜色', 'kratos'),
        'desc' => __('全站页面的背景颜色，需填写十六进制颜色码', 'kratos'),
        'id' => 'g_background',
        'std' => '#f5f5f5',
        'type' => 'color',
    );

    $options[] = array(
        'name' => __('CSS 动画库', 'kratos'),
        'desc' => __('开启 animate.css 效果', 'kratos'),
        'std' => '0',
        'id' => 'g_animate',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('Font Awesome', 'kratos'),
        'desc' => __('开启 Font Awesome 字体', 'kratos'),
        'std' => '0',
        'id' => 'g_fontawesome',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('静态资源加速', 'kratos'),
        'desc' => __('开启静态资源加速（CSS、JS、Font）', 'kratos'),
        'std' => '1',
        'id' => 'g_cdn',
        'type' => 'checkbox',
    );

    $options[] = array(
        'desc' => __('选择 CDN 加速节点', 'kratos'),
        'id' => 'g_cdn_n3',
        'std' => 'maocloud',
        'type' => 'select',
        'class' => 'hidden',
        'options' => $cdn_array,
    );

    $options[] = array(
        'name' => __('禁止生成缩略图', 'kratos'),
        'desc' => __('是否禁止生成多种尺寸图片资源', 'kratos'),
        'id' => 'g_removeimgsize',
        'std' => '0',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('媒体资源托管', 'kratos'),
        'desc' => __('是否将媒体资源托管到 DogeCloud 存储', 'kratos'),
        'id' => 'g_cos',
        'std' => '0',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('存储空间名称', 'kratos'),
        'id' => 'g_cos_bucketname',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('加速域名', 'kratos'),
        'id' => 'g_cos_url',
        'placeholder' => __('例如：https://cdn.xxx.com', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('AccessKey', 'kratos'),
        'id' => 'g_cos_accesskey',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('SecretKey', 'kratos'),
        'id' => 'g_cos_secretkey',
        'class' => 'hidden',
        'type' => 'password',
    );

    $options[] = array(
        'name' => __('Gutenberg 编辑器', 'kratos'),
        'desc' => __('开启 Gutenberg 编辑器', 'kratos'),
        'std' => '0',
        'id' => 'g_gutenberg',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('Gravatar 加速', 'kratos'),
        'desc' => __('开启 Gravatar 头像加速', 'kratos'),
        'std' => '0',
        'id' => 'g_gravatar',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('Chrome 导航栏颜色', 'kratos'),
        'desc' => __('Chrome 移动端浏览器导航栏的颜色', 'kratos'),
        'id' => 'g_chrome',
        'std' => '#282a2c',
        'type' => 'color',
    );

    $options[] = array(
        'name' => __('微信二维码', 'kratos'),
        'desc' => __('开启页面右下角浮动微信二维码', 'kratos'),
        'id' => 's_wechat',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_wechat_url',
        'std' => ASSET_PATH . '/assets/img/wechat.png',
        'type' => 'upload',
        'class' => 'hidden',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('404 页面图片', 'kratos'),
        'id' => 'g_404',
        'std' => ASSET_PATH . '/assets/img/404.jpg',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('收录配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('分享图片', 'kratos'),
        'desc' => __('搜索引擎或者社交工具分享首页时抓取的图片', 'kratos'),
        'id' => 'seo_shareimg',
        'std' => ASSET_PATH . '/assets/img/default.jpg',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('关键词', 'kratos'),
        'desc' => __('每个关键词之间需要用「英文逗号」分割', 'kratos'),
        'id' => 'seo_keywords',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('站点描述', 'kratos'),
        'id' => 'seo_description',
        'type' => 'textarea',
    );

    $options[] = array(
        'name' => __('统计代码', 'kratos'),
        'desc' => __('注意：输入 HTML/JS 代码时请注意辨别代码安全！', 'kratos'),
        'id' => 'seo_statistical',
        'type' => 'textarea',
    );

    $options[] = array(
        'name' => __('robots.txt 配置', 'kratos'),
        'desc' => __('- 需要 ', 'kratos') . $seoreading . __(' 是开启的状态，下面的配置才会生效', 'kratos'),
        'type' => 'info',
    );

    $options[] = array(
        'desc' => __('- 如果网站根目录下已经有 robots.txt 文件，下面的配置不会生效', 'kratos'),
        'type' => 'info',
    );

    $options[] = array(
        'desc' => __('- 点击 ', 'kratos') . $seorobots . __(' 查看配置是否生效，如果网站开启了 CDN，可能需要刷新缓存才会生效', 'kratos'),
        'type' => 'info',
    );

    $options[] = array(
        'id' => 'seo_robots',
        'std' => getrobots(),
        'type' => 'textarea',
    );

    $options[] = array(
        'name' => __('首页配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('哀悼功能', 'kratos'),
        'desc' => __('开启站点首页黑白功能（用于R.I.P.）', 'kratos'),
        'id' => 'g_rip',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('特色图片', 'kratos'),
        'desc' => __('开启站点首页特色图片功能', 'kratos'),
        'std' => '1',
        'id' => 'g_thumbnail',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('默认特色图', 'kratos'),
        'desc' => __('当文章中没有图片并且没有设置特色图时在首页显示', 'kratos'),
        'id' => 'g_postthumbnail',
        'class' => 'hidden',
        'std' => ASSET_PATH . '/assets/img/default.jpg',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('无内容图片', 'kratos'),
        'desc' => __('当搜索不到文章或文章分类中没有文章时显示', 'kratos'),
        'id' => 'g_nothing',
        'std' => ASSET_PATH . '/assets/img/nothing.svg',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('文章配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('知识共享协议', 'kratos'),
        'desc' => __('开启文章知识共享协议', 'kratos'),
        'id' => 'g_cc_switch',
        'type' => 'checkbox',
    );

    $options[] = array(
        'desc' => __('选择文章的知识共享协议', 'kratos'),
        'id' => 'g_cc',
        'std' => 'one',
        'type' => 'select',
        'class' => 'hidden',
        'options' => $cc_array,
    );

    $options[] = array(
        'name' => __('网易云音乐', 'kratos'),
        'desc' => __('开启网易云音乐自动播放', 'kratos'),
        'id' => 'g_163mic',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('文章打赏', 'kratos'),
        'desc' => __('开启文章页面打赏功能', 'kratos'),
        'id' => 'g_donate',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('支付宝二维码', 'kratos'),
        'id' => 'g_donate_alipay',
        'std' => ASSET_PATH . '/assets/img/donate.png',
        'type' => 'upload',
        'class' => 'hidden',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('微信二维码', 'kratos'),
        'id' => 'g_donate_wechat',
        'std' => ASSET_PATH . '/assets/img/donate.png',
        'type' => 'upload',
        'class' => 'hidden',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('站长配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('个人头像', 'kratos'),
        'id' => 'a_gravatar',
        'std' => ASSET_PATH . '/assets/img/gravatar.png',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('个人昵称', 'kratos'),
        'id' => 'a_nickname',
        'std' => 'Kratos',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('个人简介', 'kratos'),
        'std' => __('保持饥渴的专注，追求最佳的品质', 'kratos'),
        'id' => 'a_about',
        'type' => 'textarea',
    );

    $options[] = array(
        'name' => __('邮件配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('SMTP 服务', 'kratos'),
        'desc' => __('开启 SMTP 服务功能', 'kratos'),
        'id' => 'm_smtp',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name' => __('邮件服务器', 'kratos'),
        'desc' => __('填写发件服务器地址', 'kratos'),
        'id' => 'm_host',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('服务器端口', 'kratos'),
        'desc' => __('填写发件服务器端口', 'kratos'),
        'id' => 'm_port',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('授权方式', 'kratos'),
        'desc' => __('填写登录鉴权的方式', 'kratos'),
        'id' => 'm_sec',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('邮箱帐号', 'kratos'),
        'desc' => __('填写邮箱账号', 'kratos'),
        'id' => 'm_username',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('邮箱密码', 'kratos'),
        'desc' => __('填写邮箱密码', 'kratos'),
        'id' => 'm_passwd',
        'class' => 'hidden',
        'type' => 'password',
    );

    $options[] = array(
        'id' => 'm_sendmail',
        'class' => 'hidden',
        'type' => 'sendmail',
    );

    $options[] = array(
        'name' => __('顶部配置', 'kratos'),
        'type' => 'heading',
    );

	$options[] = array(
        'name' => __( '顶部样式', 'kratos' ),
        'desc' => __('请选择顶部样式（颜色导航或图片导航）', 'kratos'),
		'id' => 'top_select',
		'std' => 'banner',
		'type' => 'select',
		'options' => $top_array
	);

    $options[] = array(
        'name' => __('颜色导航', 'kratos'),
        'id' => 'top_color',
        'std' => '#24292e',
        'class' => 'hidden',
        'type' => 'color',
    );

    $options[] = array(
        'name' => __('图片导航', 'kratos'),
        'id' => 'top_img',
        'std' => ASSET_PATH . '/assets/img/background.png',
        'class' => 'hidden',
        'type' => 'upload',
    );

    $options[] = array(
        'name' => __('副标题', 'kratos'),
        'id' => 'top_title',
        'std' => 'Kratos',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('标题描述', 'kratos'),
        'std' => __('一款专注于用户阅读体验的响应式博客主题', 'kratos'),
        'id' => 'top_describe',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('页脚配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('选择需要开启的社交图标', 'kratos'),
        'desc' => __('国内平台', 'kratos'),
        'type' => 'info',
    );

    $options[] = array(
        'desc' => __('新浪微博', 'kratos'),
        'id' => 's_sina',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_sina_url',
        'placeholder' => __('例如：https://weibo.com/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('哔哩哔哩', 'kratos'),
        'id' => 's_bilibili',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_bilibili_url',
        'placeholder' => __('例如：https://space.bilibili.com/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('CODING', 'kratos'),
        'id' => 's_coding',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_coding_url',
        'placeholder' => __('例如：https://xxxxx.coding.net/u/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('码云 Gitee', 'kratos'),
        'id' => 's_gitee',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_gitee_url',
        'placeholder' => __('例如：https://gitee.com/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('海外平台', 'kratos'),
        'type' => 'info',
    );

    $options[] = array(
        'desc' => __('Twitter', 'kratos'),
        'id' => 's_twitter',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_twitter_url',
        'placeholder' => __('例如：https://twitter.com/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('Telegram', 'kratos'),
        'id' => 's_telegram',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_telegram_url',
        'placeholder' => __('例如：https://t.me/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('LinkedIn', 'kratos'),
        'id' => 's_linkedin',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_linkedin_url',
        'placeholder' => __('例如：https://www.linkedin.com/in/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('YouTube', 'kratos'),
        'id' => 's_youtube',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_youtube_url',
        'placeholder' => __('例如：https://www.youtube.com/channel/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('Github', 'kratos'),
        'id' => 's_github',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_github_url',
        'placeholder' => __('例如：https://github.com/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('Stack Overflow', 'kratos'),
        'id' => 's_stackflow',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_stackflow_url',
        'placeholder' => __('例如：https://stackoverflow.com/users/xxxxx', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('其他', 'kratos'),
        'type' => 'info',
    );

    $options[] = array(
        'desc' => __('电子邮箱', 'kratos'),
        'id' => 's_email',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_email_url',
        'placeholder' => __('例如：mailto:xxxxx@gmail.com', 'kratos'),
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('工信部备案信息', 'kratos'),
        'id' => 's_icp',
        'placeholder' => __('例如：京ICP证xxxxxx号', 'kratos'),
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('公安网备案信息', 'kratos'),
        'id' => 's_gov',
        'placeholder' => __('例如：京公网安备 xxxxxxxxxxxx号', 'kratos'),
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('公安网备案连接', 'kratos'),
        'id' => 's_gov_link',
        'placeholder' => __('例如：http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=xxxxx', 'kratos'),
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('版权信息', 'kratos'),
        'id' => 's_copyright',
        'std' => 'COPYRIGHT © 2020 ' . $sitename . '. ALL RIGHTS RESERVED.',
        'type' => 'textarea',
    );

    $options[] = array(
        'name' => __('广告配置', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'name' => __('文章页面广告', 'kratos'),
        'desc' => __('开启顶部广告', 'kratos'),
        'id' => 's_singletop',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_singletop_url',
        'class' => 'hidden',
        'std' => ASSET_PATH . '/assets/img/ad.png',
        'type' => 'upload',
    );

    $options[] = array(
        'desc' => __('选填广告连接，如果不填则只显示图片', 'kratos'),
        'id' => 's_singletop_links',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'desc' => __('开启底部广告', 'kratos'),
        'id' => 's_singledown',
        'type' => 'checkbox',
    );

    $options[] = array(
        'id' => 's_singledown_url',
        'class' => 'hidden',
        'std' => ASSET_PATH . '/assets/img/ad.png',
        'type' => 'upload',
    );

    $options[] = array(
        'desc' => __('选填广告连接，如果不填则只显示图片', 'kratos'),
        'id' => 's_singledown_links',
        'class' => 'hidden',
        'type' => 'text',
    );

    $options[] = array(
        'name' => __('关于主题', 'kratos'),
        'type' => 'heading',
    );

    $options[] = array(
        'type' => 'about',
    );

    return $options;
}