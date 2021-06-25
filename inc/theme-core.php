<?php
/**
 * 核心函数
 * @author Seaton Jiang <seatonjiang@vtrois.com>
 * @license MIT License
 * @version 2021.06.25
 */

if (kratos_option('g_cdn', false)) {
    $asset_path = 'https://cdn.jsdelivr.net/gh/vtrois/kratos@' . THEME_VERSION;
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
        wp_enqueue_style('bootstrap', ASSET_PATH . '/assets/css/bootstrap.min.css', array(), '4.5.0');
        wp_enqueue_style('kicon', ASSET_PATH . '/assets/css/iconfont.min.css', array(), THEME_VERSION);
        wp_enqueue_style('layer', ASSET_PATH . '/assets/css/layer.min.css', array(), '3.1.1');
        if (kratos_option('g_animate', false)) {
            wp_enqueue_style('animate', ASSET_PATH . '/assets/css/animate.min.css', array(), '4.1.1');
        }
        if (kratos_option('g_fontawesome', false)) {
            wp_enqueue_style('fontawesome', ASSET_PATH . '/assets/css/fontawesome.min.css', array(), '5.15.2');
        }
        wp_enqueue_style('kratos', kratos_option('g_cdn', false)
            ? 'https://cdn.jsdelivr.net/gh/vtrois/kratos@' . THEME_VERSION . '/style.css'
            : get_parent_theme_file_uri('style.css'),
            array(), THEME_VERSION);
        wp_enqueue_style('kratos-child', get_stylesheet_uri(), array(), THEME_VERSION);
        if (kratos_option('g_adminbar', true)) {
            $admin_bar_css = "
            @media screen and (min-width: 782px) {
                .k-nav {
                    padding-top: 40px;
                }
            }
            @media screen and (max-width: 782px) {
                .k-nav {
                    padding-top: 54px;
                }
            }
            @media screen and (min-width: 992px) {
                .k-nav {
                    height: 102px;
                }
            }";
            if (current_user_can('level_10')) {
                wp_add_inline_style('kratos', $admin_bar_css);
            }
        }
        if (kratos_option('g_sticky', false)) {
            $sticky_css = ".sticky-sidebar{position: sticky;top: 25px;height:100%}";
            wp_add_inline_style('kratos', $sticky_css);
        }
        // js
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', ASSET_PATH . '/assets/js/jquery.min.js', array(), '3.4.1', false);
        wp_enqueue_script('bootstrap-bundle', ASSET_PATH . '/assets/js/bootstrap.bundle.min.js', array(), '4.5.0', true);
        wp_enqueue_script('layer', ASSET_PATH . '/assets/js/layer.min.js', array(), '3.1.1', true);
        wp_enqueue_script('dplayer', ASSET_PATH . '/assets/js/DPlayer.min.js', array(), THEME_VERSION, true);
        wp_enqueue_script('kratos', ASSET_PATH . '/assets/js/kratos.js', array(), THEME_VERSION, true);

        $data = array(
            'site' => home_url(),
            'directory' => get_stylesheet_directory_uri(),
            'alipay' => kratos_option('g_donate_alipay', ASSET_PATH . '/assets/img/donate.png'),
            'wechat' => kratos_option('g_donate_wechat', ASSET_PATH . '/assets/img/donate.png'),
            'repeat' => __('您已经赞过了', 'kratos'),
            'thanks' => __('感谢您的支持', 'kratos'),
            'donate' => __('打赏作者', 'kratos'),
            'scan'   => __('扫码支付', 'kratos'),
        );
        wp_localize_script('kratos', 'kratos', $data);
    }
}
add_action('wp_enqueue_scripts', 'theme_autoload');

// 前台管理员导航
if (! kratos_option('g_adminbar', true)) {
    add_filter('show_admin_bar', '__return_false');
}

// 移除自动保存、修订版本
if (kratos_option('g_post_revision', true)) {
    remove_action('post_updated', 'wp_save_post_revision');
}

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
add_filter('get_avatar_url', 'get_https_avatar');

// 主题更新检测
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://cdn.jsdelivr.net/gh/vtrois/kratos/inc/update-checker/update.json',
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
        unset($sizes['full']);
        unset($sizes['medium_large']);
        unset($sizes['1536x1536']);
        unset($sizes['2048x2048']);
        return $sizes;
    }
    add_filter('intermediate_image_sizes_advanced', 'remove_default_images');
    
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
}
add_filter('big_image_size_threshold', '__return_false');

// 媒体文件使用 md5 值重命名，指定文件前缀
add_filter('wp_handle_sideload_prefilter', 'custom_upload_perfilter');
add_filter('wp_handle_upload_prefilter', 'custom_upload_filter');

function custom_upload_filter($file)
{
    $info = pathinfo($file['name']);

    $ext = '.' . $info['extension'];

    $prdfix = kratos_option('g_renameother_prdfix', '') . '-';

    $img_mimes = array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'webp', 'svg');

    $str = kratos_option('g_renameother_mime', '');
    $arr = explode("|", $str);
    $arr = array_filter($arr);

    foreach ($arr as $value) {
        $compressed_mimes[] = $value;
    }

    if (kratos_option('g_renameimg', false)) {
        foreach ($img_mimes as $img_mime) {
            if ($info['extension'] == $img_mime) {
                $charid = strtolower(md5($file['name']));
                $hyphen = chr(45);
                $uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
                $file['name'] = $uuid . $ext;
            }
        }
    }

    if (kratos_option('g_renameother', false)) {
        foreach ($compressed_mimes as $compressed_mime) {
            if ($info['extension'] == $compressed_mime) {
                $file['name'] = $prdfix . $file['name'];
            }
        }
    }

    return $file;
}

// 仅搜索文章标题
if (kratos_option('g_search', false)) {
    add_filter('posts_search', 'search_enhancement', 10, 2);

    function search_enhancement($search, $wp_query)
    {
        if (!empty($search) && !empty($wp_query->query_vars['search_terms']))
        {
            global $wpdb;
        
            $q = $wp_query->query_vars;    
            $n = !empty($q['exact']) ? '' : '%';
        
            $search = array();

            foreach ((array)$q['search_terms'] as $term)
            {
                $search[] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n);
            }

            if (!is_user_logged_in())
            {
                $search[] = "$wpdb->posts.post_password = ''";
            }

            $search = ' AND ' . implode(' AND ', $search);
        }

        return $search;
    }
}

// 腾讯云验证码
if (kratos_option('g_007', false)) {
    add_action('login_head', 'add_login_head');
    function add_login_head()
    {
        echo '<script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>';
    }

    add_action('login_form', 'add_captcha_body');
    function add_captcha_body(){ ?>
        <label for="codeVerifyButton">人机验证</label>
        <input type="button" name="TencentCaptcha" id="TencentCaptcha" data-appid="<?php echo kratos_option('g_007_appid'); ?>" data-cbfn="callback" class="button" value="验证" style="width: 100%;margin-bottom: 16px;height:40px;" />
        <input type="hidden" id="codeCaptcha" name="codeCaptcha" value="" />
        <input type="hidden" id="codeVerifyTicket" name="codeVerifyTicket" value="" />
        <input type="hidden" id="codeVerifyRandstr" name="codeVerifyRandstr" value="" />
        <script>
            window.callback = function(res){
                if(res.ret === 0)
                {
                    var verifybutton = document.getElementById("TencentCaptcha");
                    document.getElementById("codeVerifyTicket").value = res.ticket;
                    document.getElementById("codeVerifyRandstr").value = res.randstr;
                    document.getElementById("codeCaptcha").value = 1;
                    verifybutton.setAttribute("disabled", "disabled");
                    verifybutton.style.cssText = "background-color:#4fb845!important; color:#fff!important; width:100%; margin-bottom:16px; height: 40px;pointer-events:none;";
                    verifybutton.value = "验证成功";
                }
            }
        </script>
    <?php
    }

    add_filter('wp_authenticate_user', 'validate_tcaptcha_login', 100, 1);
    function validate_tcaptcha_login($user) { 
        $slide = $_POST['codeCaptcha'];
        if($slide == '')
        {
            return  new WP_Error('broke', __("错误：请进行真人验证"));
        }else{
            $result = validate_login($_POST['codeVerifyTicket'], $_POST['codeVerifyRandstr']);
            if($result['result'])
            {
                return $user;
            }else{
                return new WP_Error('broke', $result['message']);
            }
        }
    }

    function validate_login($ticket,$randstr){
        $appid = kratos_option('g_007_appid');
        $appsecretkey = kratos_option('g_007_appsecretkey');
        $userip = $_SERVER["REMOTE_ADDR"];
        $url = "https://ssl.captcha.qq.com/ticket/verify";
        $params = array(
            "aid"          => $appid,
            "AppSecretKey" => $appsecretkey,
            "Ticket"       => $ticket,
            "Randstr"      => $randstr,
            "UserIP"       => $userip
        );
        $paramstring = http_build_query($params);
        $content = txcurl($url, $paramstring);
        $result = json_decode($content, true);

        if($result){
            if($result['response'] == 1){
                return array(
                    'result'  => 1,
                    'message' => ''
                );
            }else{
                return array(
                    'result'  => 0,
                    'message' => $result['err_msg']
                );
            }
        }else{
            return array(
                'result'  => 0,
                'message' => '错误：请求异常，请稍后再试'
            );
        }
    }

    function txcurl($url, $params=false, $ispost=0)
    {
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if($ispost)
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        }else{
            if($params)
            {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            }else{
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE)
        {
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));

        curl_close($ch);
        return $response;
    }
}
