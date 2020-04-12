<?php
/**
 * 核心函数
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.04.12
 */

if (kratos_option('g_cdn', false)) {
    $cdn_array = array(
        'maocloud' => 'https://n3.cdn.vtrois.com/kratos/' . THEME_VERSION,
        'jsdelivr' => 'https://cdn.jsdelivr.net/gh/vtrois/kratos@' . THEME_VERSION,
    );
    $asset_path = $cdn_array[kratos_option('g_cdn_n3', 'maocloud')];
} else {
    $asset_path = get_template_directory_uri();
}
define('ASSET_PATH', $asset_path);

// 自动跳转主题设置
function init_theme()
{
    global $pagenow;
    if ('themes.php' == $pagenow && isset($_GET['activated'])) {
        wp_redirect(admin_url('admin.php?page=kratos_options'));
        exit;
    }
}
add_action('load-themes.php', 'init_theme');

// 语言国际化
function theme_languages()
{
    load_theme_textdomain('kratos', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'theme_languages');

// 资源加载
function theme_autoload()
{
    if (!is_admin()) {
        // css
        wp_enqueue_style('bootstrap', ASSET_PATH . '/assets/css/bootstrap.min.css', array(), '4.4.1');
        wp_enqueue_style('kicon', ASSET_PATH . '/assets/css/iconfont.min.css', array(), THEME_VERSION);
        wp_enqueue_style('layer', ASSET_PATH . '/assets/css/layer.min.css', array(), '3.1.1');
        if (kratos_option('g_animate', false)) {
            wp_enqueue_style('animate', ASSET_PATH . '/assets/css/animate.min.css', array(), '3.7.2');
        }
        if (kratos_option('g_fontawesome', false)) {
            wp_enqueue_style('fontawesome', ASSET_PATH . '/assets/css/fontawesome.min.css', array(), '5.13.0');
        }
        wp_enqueue_style('kratos', ASSET_PATH . '/assets/css/kratos.min.css', array(), THEME_VERSION);
        wp_enqueue_style('custom', get_template_directory_uri() . '/custom/custom.css', array(), THEME_VERSION);
        // js
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', ASSET_PATH . '/assets/js/jquery.min.js', array(), '3.4.1', false);
        wp_enqueue_script('bootstrap', ASSET_PATH . '/assets/js/bootstrap.min.js', array(), '4.4.1', true);
        wp_enqueue_script('layer', ASSET_PATH . '/assets/js/layer.min.js', array(), '3.1.1', true);
        wp_enqueue_script('kratos', ASSET_PATH . '/assets/js/kratos.min.js', array(), THEME_VERSION, true);
        wp_enqueue_script('custom', get_template_directory_uri() . '/custom/custom.js', array(), THEME_VERSION, true);

        $data = array(
            'site' => home_url(),
            'directory' => get_stylesheet_directory_uri(),
            'alipay' => kratos_option('g_donate_alipay', ASSET_PATH . '/assets/img/donate.png'),
            'wechat' => kratos_option('g_donate_wechat', ASSET_PATH . '/assets/img/donate.png'),
            'repeat' => __('您已经赞过了', 'kratos'),
            'thanks' => __('感谢您的支持', 'kratos'),
            'donate' => __('打赏作者', 'kratos'),
            'scan' => __('扫码支付', 'kratos'),
        );
        wp_localize_script('kratos', 'kratos', $data);
    }
}
add_action('wp_enqueue_scripts', 'theme_autoload');

// 禁用 Admin Bar
add_filter('show_admin_bar', '__return_false');

// 移除自动保存、修订版本
remove_action('post_updated', 'wp_save_post_revision');

// 添加友情链接
add_filter('pre_option_link_manager_enabled', '__return_true');

// 禁用转义
$qmr_work_tags = array('the_title', 'the_excerpt', 'single_post_title', 'comment_author', 'comment_text', 'link_description', 'bloginfo', 'wp_title', 'term_description', 'category_description', 'widget_title', 'widget_text');

foreach ($qmr_work_tags as $qmr_work_tag) {
    remove_filter($qmr_work_tag, 'wptexturize');
}

remove_filter('the_content', 'wptexturize');
add_filter('run_wptexturize', '__return_false');

// 禁用 Emoji
add_filter('emoji_svg_url', '__return_false');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('embed_head', 'print_emoji_detection_script');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// 禁用 Trackbacks
add_filter('xmlrpc_methods', function ($methods) {
    $methods['pingback.ping'] = '__return_false';
    $methods['pingback.extensions.getPingbacks'] = '__return_false';
    return $methods;
});
remove_action('do_pings', 'do_all_pings', 10);
remove_action('publish_post', '_publish_post_hook', 5);

// 优化 wp_head() 内容
foreach (array('rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head') as $action) {
    remove_action($action, 'the_generator');
}
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10);
remove_action('wp_head', 'start_post_rel_link', 10);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'wp_shortlink_header', 11);
remove_action('template_redirect', 'rest_output_link_header', 11);

// 禁用 WordPress 拼写修正
remove_filter('the_title', 'capital_P_dangit', 11);
remove_filter('the_content', 'capital_P_dangit', 11);
remove_filter('comment_text', 'capital_P_dangit', 31);

// 禁用后台 Google Fonts
add_filter('style_loader_src', function ($href) {
    if (strpos($href, "fonts.googleapis.com") === false) {
        return $href;
    }
    return false;
});

// 禁用 Auto Embeds
remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);

// 替换国内 Gravatar 源
function get_https_avatar($avatar)
{
    if (kratos_option('g_gravatar', false)) {
        $cdn = "gravatar.loli.net";
    } else {
        $cdn = "cn.gravatar.com";
    }

    $avatar = str_replace(array("www.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com", "3.gravatar.com", "secure.gravatar.com"), $cdn, $avatar);
    $avatar = str_replace("http://", "https://", $avatar);
    return $avatar;
}
add_filter('get_avatar', 'get_https_avatar');

// 主题更新检测
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://n3.cdn.vtrois.com/kratos/version.json',
    get_template_directory() . '/functions.php',
    'Kratos'
);

// 禁止生成多种尺寸图片
if (kratos_option('g_removeimgsize', false)) {
    function remove_default_images($sizes)
    {
        unset($sizes['thumbnail']);
        unset($sizes['medium']);
        unset($sizes['large']);
        unset($sizes['medium_large']);
        return $sizes;
    }
    add_filter('intermediate_image_sizes_advanced', 'remove_default_images');
}