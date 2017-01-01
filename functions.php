<?php
/**
 * Kratos functions and definitions
 *
 * @package Vtrois
 * @version 2.4
 */

define( 'KRATOS_VERSION', '2.4.5' );

require_once( get_template_directory() . '/inc/widgets.php');

/**
 * Theme updating
 */
require_once( get_template_directory() . '/inc/version.php' );
$kratos_update_checker = new ThemeUpdateChecker(
    'Kratos', 
    'https://soft.vtrois.com/wordpress/theme/kratos/upgrade.json'
);

/**
 * Replace Gravatar server
 */
function kratos_get_avatar( $avatar ) {
    $avatar = preg_replace( "/http:\/\/(www|\d).gravatar.com/", kratos_option('gravatar_server'), $avatar );
    return $avatar;
}
add_filter( 'get_avatar', 'kratos_get_avatar' );

/**
 * Disable automatic formatting
 */
function my_formatter($content) {
    $new_content = '';
    $pattern_full = '{(\[raw\].*?\[/raw\])}is';
    $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
    $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
foreach ($pieces as $piece) {
    if (preg_match($pattern_contents, $piece, $matches)) {
        $new_content .= $matches[1];
    } else {
        $new_content .= wptexturize(wpautop($piece));
    }
}
    return $new_content;
}
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');
add_filter('the_content', 'my_formatter', 99);

/**
 * Load scripts
 */  
function kratos_theme_scripts() {  
    $dir = get_template_directory_uri(); 
    if ( !is_admin() ) {  
        wp_enqueue_style( 'animate-style', $dir . '/css/animate.min.css', array(), '3.5.1'); 
        wp_enqueue_style( 'awesome-style', $dir . '/css/font-awesome.min.css', array(), '4.7.0');
        wp_enqueue_style( 'bootstrap-style', $dir . '/css/bootstrap.min.css', array(), '3.3.7');
        wp_enqueue_style( 'superfish-style', $dir . '/css/superfish.min.css', array(), 'r7');
        wp_enqueue_style( 'kratos-style', get_stylesheet_uri(), array(), KRATOS_VERSION);
        wp_enqueue_style( 'kratos-diy-style', $dir . '/css/kratos.diy.css', array(), KRATOS_VERSION);
        wp_enqueue_script( 'jquerys', $dir . '/js/jquery.min.js' , array(), '2.1.4');
        wp_enqueue_script( 'easing', $dir . '/js/jquery.easing.js', array(), '1.3.0'); 
        wp_enqueue_script( 'qrcode', $dir . '/js/jquery.qrcode.min.js', array(), KRATOS_VERSION);
        wp_enqueue_script( 'modernizr', $dir . '/js/modernizr.js' , array(), '2.6.2');
        wp_enqueue_script( 'bootstrap', $dir . '/js/bootstrap.min.js', array(), '3.3.7');
        wp_enqueue_script( 'waypoints', $dir . '/js/jquery.waypoints.min.js', array(), '4.0.0');
        wp_enqueue_script( 'stellar', $dir . '/js/jquery.stellar.min.js', array(), '0.6.2');
        wp_enqueue_script( 'hoverIntents', $dir . '/js/hoverIntent.js', array(), 'r7');
        wp_enqueue_script( 'superfish', $dir . '/js/superfish.js', array(), '1.0.0');
        wp_enqueue_script( 'kratos', $dir . '/js/kratos.js', array(),  KRATOS_VERSION);
        wp_enqueue_script( 'kratos-diy', $dir . '/js/kratos.diy.js', array(),  KRATOS_VERSION);
    }  
}  
add_action('wp_enqueue_scripts', 'kratos_theme_scripts');

/**
 * Remove the head code
 */
add_filter('rest_enabled', '_return_false');
add_filter('rest_jsonp_enabled', '_return_false');
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action( 'wp_head', 'rsd_link' );   
remove_action( 'wp_head', 'wlwmanifest_link' );   
remove_action( 'wp_head', 'index_rel_link' );   
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );   
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );   
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );   
remove_action( 'wp_head', 'locale_stylesheet' );   
remove_action( 'publish_future_post', 'check_and_publish_future_post', 10, 1 );   
remove_action( 'wp_head', 'noindex', 1 );   
remove_action( 'wp_head', 'wp_print_head_scripts', 9 );   
remove_action( 'wp_head', 'wp_generator' );   
remove_action( 'wp_head', 'rel_canonical' );   
remove_action( 'wp_footer', 'wp_print_footer_scripts' );   
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );   
remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 ); 

function disable_emojis() {
    global $wp_version;
    if ($wp_version >= 4.2) {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
    }
}
add_action( 'init', 'disable_emojis' );

function disable_open_sans( $translations, $text, $context, $domain )
{
    if ( 'Open Sans font: on or off' == $context && 'on' == $text ) {
        $translations = 'off';
    }
    return $translations;
}
add_filter('gettext_with_context', 'disable_open_sans', 888, 4 );

/**
 * Prohibit character escaping
 */
$qmr_work_tags = array('the_title','the_excerpt','single_post_title','comment_author','comment_text','link_description','bloginfo','wp_title', 'term_description','category_description','widget_title','widget_text');
foreach ( $qmr_work_tags as $qmr_work_tag ) {
    remove_filter ($qmr_work_tag, 'wptexturize');
}
remove_filter('the_content', 'wptexturize');

/**
 * Add the page html
 */
add_action('init', 'html_page_permalink', -1);
function html_page_permalink() {
    if (kratos_option('page_html')==1){
        global $wp_rewrite;
        if ( !strpos($wp_rewrite->get_page_permastruct(), '.html')){
            $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
        }
    }
}

/**
 * Remove automatically saved
 */
wp_deregister_script('autosave');

/**
 * Remove the revision
 */
remove_action('post_updated','wp_save_post_revision' );

/**
 * Short code
 */
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 12);

/**
 * Link manager
 */  
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

/**
 * Remove the excess CSS selectors
 */
add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
function my_css_attributes_filter($var) {
    return is_array($var) ? array_intersect($var, array('current-menu-item','current-post-ancestor','current-menu-ancestor','current-menu-parent')) : '';
}

/**
 * Short code set
 */
function success($atts, $content=null, $code="") {
    $return = '<div class="alert alert-success">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('success' , 'success' );

function info($atts, $content=null, $code="") {
    $return = '<div class="alert alert-info">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('info' , 'info' );

function warning($atts, $content=null, $code="") {
    $return = '<div class="alert alert-warning">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('warning' , 'warning' );

function danger($atts, $content=null, $code="") {
    $return = '<div class="alert alert-danger">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('danger' , 'danger' );

function wymusic($atts, $content=null, $code="") {
    $return = '<iframe class="" style="width:100%" frameborder="no" border="0" marginwidth="0" marginheight="0" height=86 src="http://music.163.com/outchain/player?type=2&id=';
    $return .= $content;
    $return .= '&auto='. kratos_option('wy_music') .'&height=66"></iframe>';
    return $return;
}
add_shortcode('music' , 'wymusic' );

function bdbtn($atts, $content=null, $code="") {
    $return = '<a class="downbtn" href="';
    $return .= $content;
    $return .= '" target="_blank"><i class="fa fa-download"></i> 本地下载</a>';
    return $return;
}
add_shortcode('bdbtn' , 'bdbtn' );

function ypbtn($atts, $content=null, $code="") {
    $return = '<a class="downbtn downcloud" href="';
    $return .= $content;
    $return .= '" target="_blank"><i class="fa fa-cloud-download"></i> 云盘下载</a>';
    return $return;
}
add_shortcode('ypbtn' , 'ypbtn' );

function nrtitle($atts, $content=null, $code="") {
    $return = '<h2>';
    $return .= $content;
    $return .= '</h2>';
    return $return;
}
add_shortcode('title' , 'nrtitle' );

function kbd($atts, $content=null, $code="") {
    $return = '<kbd>';
    $return .= $content;
    $return .= '</kbd>';
    return $return;
}
add_shortcode('kbd' , 'kbd' );

function nrmark($atts, $content=null, $code="") {
    $return = '<mark>';
    $return .= $content;
    $return .= '</mark>';
    return $return;
}
add_shortcode('mark' , 'nrmark' );

function striped($atts, $content=null, $code="") {
    $return = '<div class="progress progress-striped active"><div class="progress-bar" style="width: ';
    $return .= $content;
    $return .= '%;"></div></div>';
    return $return;
}
add_shortcode('striped' , 'striped' );

function successbox($atts, $content=null, $code="") {
    extract(shortcode_atts(array("title"=>'标题内容'),$atts));
    $return = '<div class="panel panel-success"><div class="panel-heading"><h3 class="panel-title">';
    $return .= $title;
    $return .= '</h3></div><div class="panel-body">';
    $return .= $content;
    $return .= '</div></div>';
    return $return;
}
add_shortcode('successbox' , 'successbox' );

function infobox($atts, $content=null, $code="") {
    extract(shortcode_atts(array("title"=>'标题内容'),$atts));
    $return = '<div class="panel panel-info"><div class="panel-heading"><h3 class="panel-title">';
    $return .= $title;
    $return .= '</h3></div><div class="panel-body">';
    $return .= $content;
    $return .= '</div></div>';
    return $return;
}
add_shortcode('infobox' , 'infobox' );

function warningbox($atts, $content=null, $code="") {
    extract(shortcode_atts(array("title"=>'标题内容'),$atts));
    $return = '<div class="panel panel-warning"><div class="panel-heading"><h3 class="panel-title">';
    $return .= $title;
    $return .= '</h3></div><div class="panel-body">';
    $return .= $content;
    $return .= '</div></div>';
    return $return;
}
add_shortcode('warningbox' , 'warningbox' );

function dangerbox($atts, $content=null, $code="") {
    extract(shortcode_atts(array("title"=>'标题内容'),$atts));
    $return = '<div class="panel panel-danger"><div class="panel-heading"><h3 class="panel-title">';
    $return .= $title;
    $return .= '</h3></div><div class="panel-body">';
    $return .= $content;
    $return .= '</div></div>';
    return $return;
}
add_shortcode('dangerbox' , 'dangerbox' );

function youku($atts, $content=null, $code="") {
    $return = '<div class="video-container"><iframe height="498" width="750" src="http://player.youku.com/embed/';
    $return .= $content;
    $return .= '" frameborder="0" allowfullscreen="allowfullscreen"></iframe></div>';
    return $return;
}
add_shortcode('youku' , 'youku' );

function tudou($atts, $content=null, $code="") {
    extract(shortcode_atts(array("code"=>'0'),$atts));
    $return = '<div class="video-container"><iframe src="http://www.tudou.com/programs/view/html5embed.action?type=1&code=';
    $return .= $content;
    $return .= '&lcode=';
    $return .= $code;
    $return .= '&resourceId=0_06_05_99" allowtransparency="true" allowfullscreen="true" allowfullscreenInteractive="true" scrolling="no" border="0" frameborder="0"></iframe></div>';
    return $return;
}
add_shortcode('tudou' , 'tudou' );

function vqq($atts, $content=null, $code="") {
    extract(shortcode_atts(array("auto"=>'0'),$atts));
    $return = '<div class="video-container"><iframe frameborder="0" width="640" height="498" src="http://v.qq.com/iframe/player.html?vid=';
    $return .= $content;
    $return .= '&tiny=0&auto=';
    $return .= $auto;
    $return .= '" allowfullscreen></iframe></div>';
    return $return;
}
add_shortcode('vqq' , 'vqq' );

function youtube($atts, $content=null, $code="") {
    $return = '<div class="video-container"><iframe height="498" width="750" src="https://www.youtube.com/embed/';
    $return .= $content;
    $return .= '" frameborder="0" allowfullscreen="allowfullscreen"></iframe></div>';
    return $return;
}
add_shortcode('youtube' , 'youtube' );

function pptv($atts, $content=null, $code="") {
    $return = '<div class="video-container"><iframe src="http://player.pptv.com/iframe/index.html#id=';
    $return .= $content;
    $return .= '&ctx=o%3Dv_share" allowtransparency="true" width="640" height="400" scrolling="no" frameborder="0" ></iframe></div>';
    return $return;
}
add_shortcode('pptv' , 'pptv' );

function bilibili($atts, $content=null, $code="") {
    $return = '<div class="video-container"><embed height="415" width="544" quality="high" allowfullscreen="true" type="application/x-shockwave-flash" src="http://static.hdslb.com/miniloader.swf" flashvars="aid=';
    $return .= $content;
    $return .= '&page=1" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash"></embed></div>';
    return $return;
}
add_shortcode('bilibili' , 'bilibili' );

/**
 * Create precode function
 */
add_action('init', 'more_button_a');
function more_button_a() {
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
     return;
   }
   if ( get_user_option('rich_editing') == 'true' ) {
     add_filter( 'mce_external_plugins', 'add_plugin' );
     add_filter( 'mce_buttons', 'register_button' );
   }
}

add_action('init', 'more_button_b');
function more_button_b() {
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
     return;
   }
   if ( get_user_option('rich_editing') == 'true' ) {
     add_filter( 'mce_external_plugins', 'add_plugin_b' );
     add_filter( 'mce_buttons_3', 'register_button_b' );
   }
}

function register_button( $buttons ) {
    array_push( $buttons, " ", "title" );
    array_push( $buttons, " ", "kbd" );
    array_push( $buttons, " ", "mark" );
    array_push( $buttons, " ", "striped" );
    array_push( $buttons, " ", "bdbtn" );
    array_push( $buttons, " ", "ypbtn" );
    array_push( $buttons, " ", "music" );
    array_push( $buttons, " ", "youku" );
    array_push( $buttons, " ", "tudou" );
    array_push( $buttons, " ", "vqq" );
    array_push( $buttons, " ", "youtube" );
    array_push( $buttons, " ", "pptv" );
    array_push( $buttons, " ", "bilibili" );
    return $buttons;
}

function register_button_b( $buttons ) {
    array_push( $buttons, " ", "success" );
    array_push( $buttons, " ", "info" );
    array_push( $buttons, " ", "warning" );
    array_push( $buttons, " ", "danger" );
    array_push( $buttons, " ", "successbox" );
    array_push( $buttons, " ", "infoboxs" );
    array_push( $buttons, " ", "warningbox" );
    array_push( $buttons, " ", "dangerbox" );
    return $buttons;
}

function add_plugin( $plugin_array ) {
    $plugin_array['title'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['kbd'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['mark'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['striped'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['bdbtn'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['ypbtn'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['music'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['youku'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['tudou'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['vqq'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['youtube'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['pptv'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['bilibili'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    return $plugin_array;
}

function add_plugin_b( $plugin_array ) {
    $plugin_array['success'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['info'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['warning'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['danger'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['successbox'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['infoboxs'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['warningbox'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    $plugin_array['dangerbox'] = get_bloginfo( 'template_url' ) . '/js/buttons/more.js';
    return $plugin_array;
}

/**
 * Add more buttons
 */
function add_more_buttons($buttons) {
        $buttons[] = 'hr';
        $buttons[] = 'fontselect';
        $buttons[] = 'fontsizeselect';
        $buttons[] = 'styleselect';
    return $buttons;
}
add_filter("mce_buttons_2", "add_more_buttons");

/**
 * The article heat
 */
function most_comm_posts($days=30, $nums=5) {
    global $wpdb;
    date_default_timezone_set("PRC");
    $today = date("Y-m-d H:i:s");
    $daysago = date( "Y-m-d H:i:s", strtotime($today) - ($days * 24 * 60 * 60) );
    $result = $wpdb->get_results("SELECT comment_count, ID, post_title, post_date FROM $wpdb->posts WHERE post_date BETWEEN '$daysago' AND '$today' and post_type='post' and post_status='publish' ORDER BY comment_count DESC LIMIT 0 , $nums");
    $output = '';
    if(empty($result)) {
        $output = '<li>暂时没有数据</li>';
    } else {
        foreach ($result as $topten) {
            $postid = $topten->ID;
            $title = $topten->post_title;
            $commentcount = $topten->comment_count;
            if ($commentcount >= 0) {
                $output .= '<a class="list-group-item visible-lg" title="'. $title .'" href="'.get_permalink($postid).'" rel="bookmark"><i class="fa  fa-book"></i> ';
                    $output .= strip_tags($title);
                $output .= '</a>';
                $output .= '<a class="list-group-item visible-md" title="'. $title .'" href="'.get_permalink($postid).'" rel="bookmark"><i class="fa  fa-book"></i> ';
                    $output .= strip_tags($title);
                $output .= '</a>';
            }
        }
    }
    echo $output;
}

/**
 * Add article type
 */
add_theme_support( 'post-formats', array('gallery','video') );

/**
 * Keywords set
 */
function kratos_keywords(){
        if( is_home() || is_front_page() ){ echo kratos_option('site_keywords'); }
        elseif( is_category() ){ single_cat_title(); }
        elseif( is_single() ){
            echo trim(wp_title('',FALSE)).',';
            if ( has_tag() ) {foreach((get_the_tags()) as $tag ) { echo $tag->name.','; } }
            foreach((get_the_category()) as $category) { echo $category->cat_name.','; } 
        }
        elseif( is_search() ){ the_search_query(); }
        else{ echo trim(wp_title('',FALSE)); }
}

/**
 * Description set
 */ 
function kratos_description(){
        if( is_home() || is_front_page() ){ echo trim(kratos_option('site_description')); }
        elseif( is_category() ){ $description = strip_tags(category_description());echo trim($description);}
        elseif( is_single() ){ 
        if(get_the_excerpt()){
            echo get_the_excerpt();
        }else{
            global $post;
                        $description = trim( str_replace( array( "\r\n", "\r", "\n", "　", " "), " ", str_replace( "\"", "'", strip_tags( $post->post_content ) ) ) );
                        echo mb_substr( $description, 0, 220, 'utf-8' );
        }
    }
        elseif( is_search() ){ echo '“';the_search_query();echo '”为您找到结果 ';global $wp_query;echo $wp_query->found_posts;echo ' 个'; }
        elseif( is_tag() ){  $description = strip_tags(tag_description());echo trim($description); }
        else{ $description = strip_tags(term_description());echo trim($description); }
    }

/**
 * Article outside chain optimization
 */
function imgnofollow( $content ) {
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
    if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
        if( !empty($matches) ) {
            $srcUrl = get_option('siteurl');
            for ($i=0; $i < count($matches); $i++)
            {
                $tag = $matches[$i][0];
                $tag2 = $matches[$i][0];
                $url = $matches[$i][0];
                $noFollow = '';
                $pattern = '/target\s*=\s*"\s*_blank\s*"/';
                preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                if( count($match) < 1 )
                    $noFollow .= ' target="_blank" ';
                $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
                preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                if( count($match) < 1 )
                    $noFollow .= ' rel="nofollow" ';
                $pos = strpos($url,$srcUrl);
                if ($pos === false) {
                    $tag = rtrim ($tag,'>');
                    $tag .= $noFollow.'>';
                    $content = str_replace($tag2,$tag,$content);
                }
            }
        }
    }
    $content = str_replace(']]>', ']]>', $content);
    return $content;
}
add_filter( 'the_content', 'imgnofollow');

/**
 * The title set
 */
function kratos_wp_title( $title, $sep ) {
    global $paged, $page;
    if ( is_feed() )
        return $title;
    $title .= get_bloginfo( 'name' );
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        $title = "$title $sep $site_description";
    if ( $paged >= 2 || $page >= 2 )
        $title = "$title $sep " . sprintf( __( 'Page %s', 'kratos' ), max( $paged, $page ) );
    return $title;
}
add_filter( 'wp_title', 'kratos_wp_title', 10, 2 );

/**
 * Mail smtp setting
 */
add_action('phpmailer_init', 'mail_smtp');
function mail_smtp( $phpmailer ) {
    if(kratos_option('mail_smtps') == 1){
        $mail_name = kratos_option('mail_name');
        $mail_host = kratos_option('mail_host');
        $mail_port = kratos_option('mail_port');
        $mail_username = kratos_option('mail_username');
        $mail_passwd = kratos_option('mail_passwd');
        $mail_smtpsecure = kratos_option('mail_smtpsecure');
        $phpmailer->FromName = $mail_name ? $mail_name : 'Kratos'; 
        $phpmailer->Host = $mail_host ? $mail_host : 'smtp.vtrois.com';
        $phpmailer->Port = $mail_port ? $mail_port : '994';
        $phpmailer->Username = $mail_username ? $mail_username : 'no_reply@vtrois.com';
        $phpmailer->Password = $mail_passwd ? $mail_passwd : '123456789';
        $phpmailer->From = $mail_username ? $mail_username : 'no_reply@vtrois.com';
        $phpmailer->SMTPAuth = kratos_option('mail_smtpauth')==1 ? true : false ;
        $phpmailer->SMTPSecure = $mail_smtpsecure ? $mail_smtpsecure : 'ssl';
        $phpmailer->IsSMTP();
    }
}

/**
 * Comments email response system
 */
add_action('comment_unapproved_to_approved', 'kratos_comment_approved');
function kratos_comment_approved($comment) {
    if(is_email($comment->comment_author_email)) {
        $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
        $to = trim($comment->comment_author_email);
        $post_link = get_permalink($comment->comment_post_ID);
        $subject = '[通知]您的留言已经通过审核';
        $message = '
            <div style="background:#ececec;width: 100%;padding: 50px 0;text-align:center;">
            <div style="background:#fff;width:750px;text-align:left;position:relative;margin:0 auto;font-size:14px;line-height:1.5;">
                    <div style="zoom:1;padding:25px 40px;background:#518bcb; border-bottom:1px solid #467ec3;">
                        <h1 style="color:#fff; font-size:25px;line-height:30px; margin:0;"><a href="' . get_option('home') . '" style="text-decoration: none;color: #FFF;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</a></h1>
                    </div>
                <div style="padding:35px 40px 30px;">
                    <h2 style="font-size:18px;margin:5px 0;">Hi ' . trim($comment->comment_author) . ':</h2>
                    <p style="color:#313131;line-height:20px;font-size:15px;margin:20px 0;">您有一条留言通过了管理员的审核并显示在文章页面，摘要信息请见下表。</p>
                        <table cellspacing="0" style="font-size:14px;text-align:center;border:1px solid #ccc;table-layout:fixed;width:500px;">
                            <thead>
                                <tr>
                                    <th style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal;color:#a0a0a0;background:#eee;border-color:#dfdfdf;" width="280px;">文章</th>
                                    <th style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal;color:#a0a0a0;background:#eee;border-color:#dfdfdf;" width="270px;">内容</th>
                                    <th style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal;color:#a0a0a0;background:#eee;border-color:#dfdfdf;" width="110px;" >操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">《' . get_the_title($comment->comment_post_ID) . '》</td>
                                    <td style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'. trim($comment->comment_content) . '</td>
                                    <td style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><a href="'.get_comment_link( $comment->comment_ID ).'" style="color:#1E5494;text-decoration:none;vertical-align:middle;" target="_blank">查看留言</a></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                    <div style="font-size:13px;color:#a0a0a0;padding-top:10px">该邮件由系统自动发出，如果不是您本人操作，请忽略此邮件。</div>
                    <div class="qmSysSign" style="padding-top:20px;font-size:12px;color:#a0a0a0;">
                        <p style="color:#a0a0a0;line-height:18px;font-size:12px;margin:5px 0;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</p>
                        <p style="color:#a0a0a0;line-height:18px;font-size:12px;margin:5px 0;"><span style="border-bottom:1px dashed #ccc;" t="5" times="">' . date("Y年m月d日",time()) . '</span></p>
                    </div>
                </div>
            </div>
        </div>';
        $from = "From: \"" . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail( $to, $subject, $message, $headers );
    }
}
function comment_mail_notify($comment_id) {
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $spam_confirmed = $comment->comment_approved;
    if (($parent_id != '') && ($spam_confirmed != 'spam')) {
        $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
        $to = trim(get_comment($parent_id)->comment_author_email);
        $subject = '[通知]您的留言有了新的回复';
        $message = '
            <div style="background:#ececec;width: 100%;padding: 50px 0;text-align:center;">
            <div style="background:#fff;width:750px;text-align:left;position:relative;margin:0 auto;font-size:14px;line-height:1.5;">
                    <div style="zoom:1;padding:25px 40px;background:#518bcb; border-bottom:1px solid #467ec3;">
                        <h1 style="color:#fff; font-size:25px;line-height:30px; margin:0;"><a href="' . get_option('home') . '" style="text-decoration: none;color: #FFF;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</a></h1>
                    </div>
                <div style="padding:35px 40px 30px;">
                    <h2 style="font-size:18px;margin:5px 0;">Hi ' . trim(get_comment($parent_id)->comment_author) . ':</h2>
                    <p style="color:#313131;line-height:20px;font-size:15px;margin:20px 0;">您有一条留言有了新的回复，摘要信息请见下表。</p>
                        <table cellspacing="0" style="font-size:14px;text-align:center;border:1px solid #ccc;table-layout:fixed;width:500px;">
                            <thead>
                                <tr>
                                    <th style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal;color:#a0a0a0;background:#eee;border-color:#dfdfdf;" width="235px;">原文</th>
                                    <th style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal;color:#a0a0a0;background:#eee;border-color:#dfdfdf;" width="235px;">回复</th>
                                    <th style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal;color:#a0a0a0;background:#eee;border-color:#dfdfdf;" width="100px;">作者</th>
                                    <th style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal;color:#a0a0a0;background:#eee;border-color:#dfdfdf;" width="90px;" >操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . trim(get_comment($parent_id)->comment_content) . '</td>
                                    <td style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'. trim($comment->comment_content) . '</td>
                                    <td style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . trim($comment->comment_author) . '</td>
                                    <td style="padding:5px 0;text-indent:8px;border:1px solid #eee;border-width:0 1px 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><a href="'.get_comment_link( $comment->comment_ID ).'" style="color:#1E5494;text-decoration:none;vertical-align:middle;" target="_blank">查看回复</a></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                    <div style="font-size:13px;color:#a0a0a0;padding-top:10px">该邮件由系统自动发出，如果不是您本人操作，请忽略此邮件。</div>
                    <div class="qmSysSign" style="padding-top:20px;font-size:12px;color:#a0a0a0;">
                        <p style="color:#a0a0a0;line-height:18px;font-size:12px;margin:5px 0;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</p>
                        <p style="color:#a0a0a0;line-height:18px;font-size:12px;margin:5px 0;"><span style="border-bottom:1px dashed #ccc;" t="5" times="">' . date("Y年m月d日",time()) . '</span></p>
                    </div>
                </div>
            </div>
        </div>';
        $from = "From: \"" . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail( $to, $subject, $message, $headers );
    }
}
add_action('comment_post', 'comment_mail_notify');


/**
 * The admin control module
 */
if (!function_exists('optionsframework_init')) {
    define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/theme-options/');
    require_once dirname(__FILE__) . '/inc/theme-options/options-framework.php';
    $optionsfile = locate_template('options.php');
    load_template($optionsfile);
}
function kratos_options_menu_filter( $menu ) {
  $menu['mode'] = 'menu';
  $menu['page_title'] = '主题设置';
  $menu['menu_title'] = '主题设置';
  $menu['menu_slug'] = 'kratos';
  return $menu;
}
add_filter( 'optionsframework_menu', 'kratos_options_menu_filter' );

/**
 * The menu navigation registration
 */
function kratos_register_nav_menu() {
        register_nav_menus(array('header_menu' => '顶部菜单'));
    }
add_action('after_setup_theme', 'kratos_register_nav_menu');

/**
 * Highlighting the active menu
 */
function kratos_active_menu_class($classes) {
    if (in_array('current-menu-item', $classes) OR in_array('current-menu-ancestor', $classes))
        $classes[] = 'active';
    return $classes;
}
add_filter('nav_menu_css_class', 'kratos_active_menu_class');

/**
 * Post Thumbnails
 */
if ( function_exists( 'add_image_size' ) ){  
    add_image_size( 'kratos-thumb', 750);
}  
function kratos_blog_thumbnail() {    
    global $post;  
    $img_id = get_post_thumbnail_id();
    $img_url = wp_get_attachment_image_src($img_id,'kratos-entry-thumb');
    $img_url = $img_url[0];
    if ( has_post_thumbnail() ) {
        echo '<a href="'.get_permalink().'"><img src="'.$img_url.'" /></a>';  
    } 
}  
add_filter( 'add_image_size', create_function( '', 'return 1;' ) );
add_theme_support( "post-thumbnails" );

/**
 * Post Thumbnails New
 */
function kratos_blog_thumbnail_new() {
    global $post;
    $img_id = get_post_thumbnail_id();
    $img_url = wp_get_attachment_image_src($img_id,'kratos-entry-thumb');
    $img_url = $img_url[0];
    if ( has_post_thumbnail() ) {
        echo '<a href="'.get_permalink().'"><img src="'.$img_url.'" /></a>';
    } else {
        $content = $post->post_content;
        $img_preg = "/<img (.*?) src=\"(.+?)\".*?>/";
        preg_match($img_preg,$content,$img_src);
        $img_count=count($img_src)-1;
        $img_val = $img_src[$img_count];
        if(!empty($img_val)){
            echo '<a href="'.get_permalink().'"><img src="'.$img_val.'" /></a>';
        } else {
             echo '<a href="'.get_permalink().'"><img src="'. kratos_option('default_image') .'" /></a>';
        }
    }  
}

/**
 * The length and suffix
 */
function kratos_excerpt_length($length) {
    return 170;
}
add_filter('excerpt_length', 'kratos_excerpt_length');
function kratos_excerpt_more($more) {
    return '……';
}
add_filter('excerpt_more', 'kratos_excerpt_more');

/**
 * Share the thumbnail fetching
 */
function share_post_image(){
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $post_thumbnail_id = get_post_thumbnail_id( $post_id );
        $img = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
        $img = $img[0];
    }else{
        $content = $post->post_content;
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        if (!empty($strResult[1])) {
            $img = $strResult[1][0];
        }else{
            $img = '';
        }
    }
    return $img;
}

/**
 * The article reading quantity statistics
 */
function kratos_set_post_views()
{
    if (is_singular())
    {
      global $post;
      $post_ID = $post->ID;
      if($post_ID)
      {
          $post_views = (int)get_post_meta($post_ID, 'views', true);
          if(!update_post_meta($post_ID, 'views', ($post_views+1)))
          {
            add_post_meta($post_ID, 'views', 1, true);
          }
      }
    }
}
add_action('wp_head', 'kratos_set_post_views');
function kratos_get_post_views($before = '', $after = '', $echo = 1)
{
  global $post;
  $post_ID = $post->ID;
  $views = (int)get_post_meta($post_ID, 'views', true);
  if ($echo) echo $before, number_format($views), $after;
  else return $views;
}

/**
 * Banner
 */
function kratos_banner(){
    if( !$output = get_option('kratos_banners') ){
        $output = '';
        $kratos_banner_on = kratos_option("kratos_banner") ? kratos_option("kratos_banner") : 0;
        if($kratos_banner_on){
            for($i=1; $i<6; $i++){
                $kratos_banner{$i} = kratos_option("kratos_banner{$i}") ? kratos_option("kratos_banner{$i}") : "";
                $kratos_banner_url{$i} = kratos_option("kratos_banner_url{$i}") ? kratos_option("kratos_banner_url{$i}") : "";
                if($kratos_banner{$i} ){
                    $banners[] = $kratos_banner{$i};
                    $banners_url[] = $kratos_banner_url{$i};
                }
            }
            $count = count($banners);
            $output .= '<div id="slide" class="carousel slide" data-ride="carousel">';
            $output .= '<ol class="carousel-indicators">';
            for($i=0; $i<$count; $i++){
                $output .= '<li data-target="#slide" data-slide-to="'.$i.'"';
                if($i==0) $output .= 'class="active"';
                $output .= '></li>';
            };
            $output .='</ol>';
            $output .= '<div class="carousel-inner" role="listbox">';
            for($i=0;$i<$count;$i++){
                $output .= '<div class="item';
                if($i==0) $output .= ' active';
                $output .= '">';
                if(!empty($banners_url[$i])){
                    $output .= '<a href="'.$banners_url[$i].'"><img src="'.$banners[$i].'"/></a>';
                }else{
                    $output .= '<img src="'.$banners[$i].'"/>';
                }
                $output .= "</div>";
            };
            $output .= '</div>';
            $output .= '<a class="left carousel-control" href="#slide" role="button" data-slide="prev">';
            $output .= '<span class="fa fa-chevron-left glyphicon glyphicon-chevron-left"></span></a>';
            $output .= '<a class="right carousel-control" href="#slide" role="button" data-slide="next">';
            $output .= '<span class="fa fa-chevron-right glyphicon glyphicon-chevron-right"></span></a></div>';
            update_option('kratos_banners', $output);
        }
    }
    echo $output;
}

function clear_banner(){
    update_option('kratos_banners', '');
}
add_action( 'optionsframework_after_validate', 'clear_banner' );

/**
 * Appreciate the article
 */
function kratos_love(){
    global $wpdb,$post;
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ( $action == 'love'){
        $raters = get_post_meta($id,'love',true);
        $expire = time() + 99999999;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie('love_'.$id,$id,$expire,'/',$domain,false);
        if (!$raters || !is_numeric($raters)) {
            update_post_meta($id, 'love', 1);
        } 
        else {
            update_post_meta($id, 'love', ($raters + 1));
        }
        echo get_post_meta($id,'love',true);
    } 
    die;
}
add_action('wp_ajax_nopriv_love', 'kratos_love');
add_action('wp_ajax_love', 'kratos_love');

/**
 * Post title optimization
 */
add_filter( 'private_title_format', 'kratos_private_title_format' );
add_filter( 'protected_title_format', 'kratos_private_title_format' );
 
function kratos_private_title_format( $format ) {
    return '%s';
}

/**
 * Password protection articles
 */
add_filter( 'the_password_form', 'custom_password_form' );
function custom_password_form() {
    $url = get_option('siteurl');
    global $post; $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID ); $o = '
    <form class="protected-post-form" action="' . $url . '/wp-login.php?action=postpass" method="post">
        <div class="panel panel-pwd">
            <div class="panel-body text-center">
                <img class="post-pwd" src="' . get_template_directory_uri() . '/images/fingerprint.png"><br />
                <h4>这是一篇受保护的文章，请输入阅读密码！</h4>
                <div class="input-group" id="respond">
                    <div class="input-group-addon"><i class="fa fa-key"></i></div>
                    <p><input class="form-control" placeholder="输入阅读密码" name="post_password" id="'.$label.'" type="password" size="20"></p>
                </div>
                <div class="comment-form" style="margin-top:15px;"><button id="generate" class="btn btn-primary btn-pwd" name="Submit" type="submit">确认</button></div>
            </div>
        </div>
    </form>';
return $o;
}

/**
 * The article reviews quantity statistics
 */
function kratos_comments_users($postid=0,$which=0) {
    $comments = get_comments('status=approve&type=comment&post_id='.$postid);
    if ($comments) {
        $i=0; $j=0; $commentusers=array();
        foreach ($comments as $comment) {
            ++$i;
            if ($i==1) { $commentusers[] = $comment->comment_author_email; ++$j; }
            if ( !in_array($comment->comment_author_email, $commentusers) ) {
                $commentusers[] = $comment->comment_author_email;
                ++$j;
            }
        }
        $output = array($j,$i);
        $which = ($which == 0) ? 0 : 1;
        return $output[$which]; 
    }
    return 0; 
}

/**
 * Comments on the face
 */
add_filter('smilies_src','custom_smilies_src',1,10);
function custom_smilies_src ($img_src, $img, $siteurl){
    return get_bloginfo('template_directory').'/images/smilies/'.$img;
}
function disable_emojis_tinymce( $plugins ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
}
function smilies_reset() {
    global $wpsmiliestrans, $wp_smiliessearch, $wp_version;
    if ( !get_option( 'use_smilies' ) || $wp_version < 4.2)
        return;
    $wpsmiliestrans = array(
    ':mrgreen:' => 'icon_mrgreen.png',
    ':exclaim:' => 'icon_exclaim.png',
    ':neutral:' => 'icon_neutral.png',
    ':twisted:' => 'icon_twisted.png',
      ':arrow:' => 'icon_arrow.png',
        ':eek:' => 'icon_eek.png',
      ':smile:' => 'icon_smile.png',
   ':confused:' => 'icon_confused.png',
       ':cool:' => 'icon_cool.png',
       ':evil:' => 'icon_evil.png',
    ':biggrin:' => 'icon_biggrin.png',
       ':idea:' => 'icon_idea.png',
    ':redface:' => 'icon_redface.png',
       ':razz:' => 'icon_razz.png',
   ':rolleyes:' => 'icon_rolleyes.png',
       ':wink:' => 'icon_wink.png',
        ':cry:' => 'icon_cry.png',
  ':surprised:' => 'icon_surprised.png',
        ':lol:' => 'icon_lol.png',
        ':mad:' => 'icon_mad.png',
        ':sad:' => 'icon_sad.png',
    );
}
smilies_reset();

/**
 * Paging
 */
function kratos_pages($range = 5){
    global $paged, $wp_query;
    if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
    if($max_page > 1){if(!$paged){$paged = 1;}
    echo "<div class='text-center' id='page-footer'><ul class='pagination'>";
        if($paged != 1){
            echo "<li><a href='" . get_pagenum_link(1) . "' class='extend' title='首页'>&laquo;</a></li>";
        }
        if($paged>1) echo '<li><a href="' . get_pagenum_link($paged-1) .'" class="prev" title="上一页">&lt;</a></li>';
        if($max_page > $range){
            if($paged < $range){
                for($i = 1; $i <= ($range + 1); $i++){
                    echo "<li"; if($i==$paged)echo " class='active'";echo "><a href='" . get_pagenum_link($i) ."'>$i</a></li>";
                }
            }
            elseif($paged >= ($max_page - ceil(($range/2)))){
                for($i = $max_page - $range; $i <= $max_page; $i++){
                    echo "<li";
                    if($i==$paged)
                        echo " class='active'";echo "><a href='" . get_pagenum_link($i) ."'>$i</a></li>";
                }
            }
            elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
                for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){
                    echo "<li";
                    if($i==$paged)echo " class='active'";
                    echo "><a href='" . get_pagenum_link($i) ."'>$i</a></li>";
                }
            }
        }
        else{
            for($i = 1; $i <= $max_page; $i++){
                echo "<li";
                if($i==$paged)echo " class='active'";
                echo "><a href='" . get_pagenum_link($i) ."'>$i</a></li>";
            }
        }
        if($paged<$max_page) echo '<li><a href="' . get_pagenum_link($paged+1) .'" class="next" title="下一页">&gt;</a></li>';
        if($paged != $max_page){
            echo "<li><a href='" . get_pagenum_link($max_page) . "' class='extend' title='尾页'>&raquo;</a></li>";
        }
        echo "</ul></div>";
    }
}

/**
 * Admin footer text
 */
function kratos_admin_footer_text($text) {
       $text = '<span id="footer-thankyou">感谢使用 <a href=http://cn.wordpress.org/ target="_blank">WordPress</a>进行创作，并使用 <a href="https://www.vtrois.com/theme-kratos.html" target="_blank">Kratos</a>主题样式，<a target="_blank" rel="nofollow" href="http://shang.qq.com/wpa/qunwpa?idkey=182bd07a135c085c88ab7e3de38f2b2d9a86983292355a4708926b99dcd5b89f">点击</a> 加入主题讨论群。</span>';
    return $text;
}

add_filter('admin_footer_text', 'kratos_admin_footer_text');