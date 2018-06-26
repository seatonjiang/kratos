<?php
/**
 * Kratos functions and definitions
 *
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */

define( 'KRATOS_VERSION', '2.6' );

require_once( get_template_directory() . '/inc/widgets.php');

/**
 * Replace Gravatar server
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
function kratos_get_avatar( $avatar ) {
    $avatar = str_replace( array( 'www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', '3.gravatar.com', 'secure.gravatar.com' ), 'cn.gravatar.com', $avatar );
    return $avatar;
}
add_filter( 'get_avatar', 'kratos_get_avatar' );

/**
 * Disable automatic formatting
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */ 
function kratos_theme_scripts() {  
    $dir = get_template_directory_uri(); 
    if ( !is_admin() ) {  
        wp_enqueue_style( 'animate', $dir . '/css/animate.min.css', array(), '3.5.1'); 
        wp_enqueue_style( 'awesome', $dir . '/css/font-awesome.min.css', array(), '4.7.0');
        wp_enqueue_style( 'bootstrap', $dir . '/css/bootstrap.min.css', array(), '3.3.7');
        wp_enqueue_style( 'superfish', $dir . '/css/superfish.min.css', array(), 'r7');
        wp_enqueue_style( 'layer', $dir . '/css/layer.min.css', array(), KRATOS_VERSION);
        wp_enqueue_style( 'kratos', get_stylesheet_uri(), array(), KRATOS_VERSION);
        wp_enqueue_script( 'jquery', $dir . '/js/jquery.min.js' , array(), '2.1.4');
        wp_enqueue_script( 'easing', $dir . '/js/jquery.easing.min.js', array(), '1.3.0'); 
        wp_enqueue_script( 'qrcode', $dir . '/js/jquery.qrcode.min.js', array(), KRATOS_VERSION);
        wp_enqueue_script( 'layer', $dir . '/js/layer.min.js', array(), '3.0.3');
        wp_enqueue_script( 'modernizr', $dir . '/js/modernizr.min.js' , array(), '2.6.2');
        wp_enqueue_script( 'bootstrap', $dir . '/js/bootstrap.min.js', array(), '3.3.7');
        wp_enqueue_script( 'waypoints', $dir . '/js/jquery.waypoints.min.js', array(), '4.0.0');
        wp_enqueue_script( 'stellar', $dir . '/js/jquery.stellar.min.js', array(), '0.6.2');
        wp_enqueue_script( 'hoverIntents', $dir . '/js/hoverIntent.min.js', array(), 'r7');
        wp_enqueue_script( 'superfish', $dir . '/js/superfish.js', array(), '1.0.0');
        wp_enqueue_script( 'kratos', $dir . '/js/kratos.js', array(),  KRATOS_VERSION);
    }  
}  
add_action('wp_enqueue_scripts', 'kratos_theme_scripts');

/**
 * Remove the head code
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
add_filter( 'emoji_svg_url', '__return_false' );
add_filter( 'show_admin_bar', '__return_false' );
remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
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


add_action( 'wp_enqueue_scripts', 'mt_enqueue_scripts', 1 );
function mt_enqueue_scripts() {
  wp_deregister_script('jquery');
}

/**
 * Prohibit character escaping
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
$qmr_work_tags = array('the_title','the_excerpt','single_post_title','comment_author','comment_text','link_description','bloginfo','wp_title', 'term_description','category_description','widget_title','widget_text');
foreach ( $qmr_work_tags as $qmr_work_tag ) {
    remove_filter ($qmr_work_tag, 'wptexturize');
}
remove_filter('the_content', 'wptexturize');

/**
 * Add the page html
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * Remove the revision
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
remove_action('post_updated','wp_save_post_revision' );

/**
 * Short code
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 12);

/**
 * Link manager
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */  
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

/**
 * Auto post link
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */  
function kratos_auto_post_link($content) {
  global $post;
  $content = preg_replace('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', "<img layer-src=\"$2\" src=\"$2\" alt=\"《".$post->post_title."》\" />", $content);
  return $content;
  }
add_filter ('the_content', 'kratos_auto_post_link',0);

/**
 * Init theme
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
add_action( 'load-themes.php', 'Init_theme' );
function Init_theme(){
  global $pagenow;
  if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
    wp_redirect( admin_url( 'themes.php?page=kratos' ) );
    exit;
  }
}

/**
 * Remove the excess CSS selectors
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
function my_css_attributes_filter($var) {
    return is_array($var) ? array_intersect($var, array('current-menu-item','current-post-ancestor','current-menu-ancestor','current-menu-parent')) : '';
}

/**
 * Short code set
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
    $return = '<iframe class="" style="width:100%" frameborder="no" border="0" marginwidth="0" marginheight="0" height=86 src="//music.163.com/outchain/player?type=2&id=';
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
    $return = '<div class="video-container"><iframe frameborder="0" width="640" height="498" src="//v.qq.com/iframe/player.html?vid=';
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
add_theme_support( 'post-formats', array('gallery','video') );

/**
 * Keywords set
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * Fix the password reset url error
 * 
 * @author MoeDog <xb@fczbl.vip>
 * @license GPL-3.0
 */
add_filter('retrieve_password_message','kratos_reset_password_message',null,2);
function kratos_reset_password_message($message,$key){
    add_filter('wp_mail_content_type',create_function('','return "text/html";'));
    if(strpos($_POST['user_login'],'@')){
        $user_data = get_user_by('email',trim($_POST['user_login']));
    }else{
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login',$login);
    }
    $msg = '<div class="emailcontent" style="width:100%;max-width:720px;text-align:left;margin:0 auto;padding-top:80px;padding-bottom:20px"><div class="emailtitle"><h1 style="color:#fff;background:#51a0e3;line-height:70px;font-size:24px;font-weight:400;padding-left:40px;margin:0">密码重设通知</h1><div class="emailtext" style="background:#fff;padding:20px 32px 20px"><div style="padding:0;font-weight:700;color:#6e6e6e;font-size:16px">尊敬的'.$user_data->display_name.',您好！</div><p style="color:#6e6e6e;font-size:13px;line-height:24px">有人要求重设您在['.get_option('blogname').']的密码，若不是您本人请求，请忽略本邮件。</p><table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-top:1px solid #eee;border-left:1px solid #eee;color:#6e6e6e;font-size:16px;font-weight:normal"><thead><tr><th colspan="2" style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;background:#f8f8f8">密码重设信息</th></tr></thead><tbody><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">用户名</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">'.$user_data->user_login.'</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">登录邮箱</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">'.$user_data->user_email.'</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center">密码重设地址</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px"><a href="'.network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_data->user_login),'login').'">单击访问</a></td></tr></tbody></table><p style="color:#6e6e6e;font-size:13px;line-height:24px">如果您的账号有异常，请您在第一时间和我们取得联系哦，联系邮箱：'.get_bloginfo('admin_email').'</p></div></div></div>';
    return $msg;
}

add_filter('password_change_email','__return_false');
add_action('user_register','kratos_pwd_register_mail',101);
add_filter('wp_new_user_notification_email','__return_false');
function kratos_pwd_register_mail($user_id){
    $user = get_user_by('id',$user_id);
    $blogname = get_option('blogname');
    if(kratos_option('mail_reg')){
        $message = '<div class="emailcontent" style="width:100%;max-width:720px;text-align:left;margin:0 auto;padding-top:80px;padding-bottom:20px"><div class="emailtitle"><h1 style="color:#fff;background:#51a0e3;line-height:70px;font-size:24px;font-weight:400;padding-left:40px;margin:0">注册成功通知</h1><div class="emailtext" style="background:#fff;padding:20px 32px 20px"><div style="padding:0;font-weight:700;color:#6e6e6e;font-size:16px">尊敬的'.$user->nickname.',您好！</div><p style="color:#6e6e6e;font-size:13px;line-height:24px">欢迎您注册['.$blogname.']，下面是您的账号信息，请妥善保管！</p><table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-top:1px solid #eee;border-left:1px solid #eee;color:#6e6e6e;font-size:16px;font-weight:normal"><thead><tr><th colspan="2" style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;background:#f8f8f8">您的详细注册信息</th></tr></thead><tbody><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">用户名</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">'.$user->user_login.'</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">登录邮箱</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">'.$user->user_email.'</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center">登录密码</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">您设定的密码</td></tr></tbody></table><p style="color:#6e6e6e;font-size:13px;line-height:24px">如果您的账号有异常，请您在第一时间和我们取得联系哦，联系邮箱：'.get_bloginfo('admin_email').'</p></div></div></div>';
    }else{
        $pwd = wp_generate_password(10,false);
        $user->user_pass = $pwd;
        $new_user_id = wp_update_user($user);
        $message = '<div class="emailcontent" style="width:100%;max-width:720px;text-align:left;margin:0 auto;padding-top:80px;padding-bottom:20px"><div class="emailtitle"><h1 style="color:#fff;background:#51a0e3;line-height:70px;font-size:24px;font-weight:400;padding-left:40px;margin:0">注册成功通知</h1><div class="emailtext" style="background:#fff;padding:20px 32px 20px"><div style="padding:0;font-weight:700;color:#6e6e6e;font-size:16px">尊敬的'.$user->nickname.',您好！</div><p style="color:#6e6e6e;font-size:13px;line-height:24px">欢迎您注册['.$blogname.']，请使用下面的信息登录并修改密码！</p><table cellpadding="0" cellspacing="0" border="0" style="width:100%;border-top:1px solid #eee;border-left:1px solid #eee;color:#6e6e6e;font-size:16px;font-weight:normal"><thead><tr><th colspan="2" style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;background:#f8f8f8">您的注册信息</th></tr></thead><tbody><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">用户名</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">'.$user->user_login.'</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">登录邮箱</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">'.$user->user_email.'</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center">临时密码</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px">'.$pwd.'</td></tr><tr><td style="padding:10px 0;border-right:1px solid #eee;border-bottom:1px solid #eee;text-align:center;width:100px">登录地址</td><td style="padding:10px 20px 10px 30px;border-right:1px solid #eee;border-bottom:1px solid #eee;line-height:30px"><a href="'.wp_login_url().'">单击访问</a></td></tr></tbody></table><p style="color:#6e6e6e;font-size:13px;line-height:24px">如果您的账号有异常，请您在第一时间和我们取得联系哦，联系邮箱：'.get_bloginfo('admin_email').'</p></div></div></div>';
    }
    $headers = "Content-Type:text/html;charset=UTF-8\n";
    wp_mail($user->user_email,'['.$blogname.']欢迎注册',$message,$headers);
}

/**
 * Add extra register fields
 * 
 * @author MoeDog <xb@fczbl.vip>
 * @license GPL-3.0
 */
add_action('register_form','kratos_show_extra_register_fields');
add_action('register_post','kratos_check_extra_register_fields',10,3);
add_action('user_register','kratos_register_extra_fields',100);
function kratos_show_extra_register_fields(){ ?>
    <p>
        <label for="nickname">昵称<br/>
            <input id="nickname" class="input" type="text" name="nickname" value="" size="20" />
        </label>
    </p>
    <?php if(kratos_option('mail_reg')){ ?>
    <p>
        <label for="password">密码<br/>
            <input id="password" class="input" type="password" name="password" value="" size="25" />
        </label>
    </p>
    <p>
        <label for="repeat_password">重复密码<br/>
            <input id="repeat_password" class="input" type="password" name="repeat_password" value="" size="25" />
        </label>
    </p><?php
    }
}
function kratos_check_extra_register_fields($login,$email,$errors){
    if($_POST['nickname']=='') $errors->add('no_nickname',"<strong>错误</strong>：昵称一栏不能为空。");
    if($_POST['password']!==$_POST['repeat_password']&&kratos_option('mail_reg')) $errors->add('passwords_not_matched',"<strong>错误</strong>：两次输入的密码不一致。");
    if(strlen($_POST['password'])<6&&kratos_option('mail_reg')) $errors->add('password_too_short',"<strong>错误</strong>：密码长度必须大于6位。");
}
function kratos_register_extra_fields($user_id){
    $userdata = array();
    $userdata['ID'] = $user_id;
    if(kratos_option('mail_reg')) $userdata['user_pass'] = $_POST['password'];
    $userdata['nickname'] = $_POST['nickname'];
    $userdata['display_name'] = $_POST['nickname'];
    $new_user_id = wp_update_user($userdata);
}

/**
 * The admin control module
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
function kratos_register_nav_menu() {
        register_nav_menus(array('header_menu' => '顶部菜单'));
    }
add_action('after_setup_theme', 'kratos_register_nav_menu');

/**
 * Highlighting the active menu
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
function kratos_active_menu_class($classes) {
    if (in_array('current-menu-item', $classes) OR in_array('current-menu-ancestor', $classes))
        $classes[] = 'active';
    return $classes;
}
add_filter('nav_menu_css_class', 'kratos_active_menu_class');

/**
 * Photo Thumbnails
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
function kratos_photo_thumbnail() {  
    global $post;  
    if ( has_post_thumbnail() ) {  
       the_post_thumbnail(array(750, ), array('class' => 'img-responsive'));
    } else { 
        $content = $post->post_content;  
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);  
        $n = count($strResult[1]);  
        if($n > 0){ 
            echo '<img src="'.$strResult[1][0].'" class="img-responsive" />';  
        }else {
            echo '<img src="'.get_bloginfo('template_url').'/images/default.jpg" class="img-responsive" />';  
        }  
    }  
}

function kratos_thumbnail_url(){
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $post_thumbnail_id = get_post_thumbnail_id( $post );
        $img = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
        $img = $img[0];
    }else{
        $content = $post->post_content;
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        if (!empty($strResult[1])) {
            $img = $strResult[1][0];
        }else{
            $img = get_bloginfo('template_url').'/images/default.jpg';
        }
    };
    return $img;
}

/**
 * Post Thumbnails
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
        $img_preg = "/<img (.*?)src=\"(.+?)\".*?>/";
        preg_match($img_preg,$content,$img_src);
        $img_count=count($img_src)-1;
        if (isset($img_src[$img_count]))
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
add_filter( 'private_title_format', 'kratos_private_title_format' );
add_filter( 'protected_title_format', 'kratos_private_title_format' );
 
function kratos_private_title_format( $format ) {
    return '%s';
}

/**
 * Password protection articles
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
 * Comments on the face
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
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
    ':mrgreen:' => 'mrgreen.png',
    ':exclaim:' => 'exclaim.png',
    ':neutral:' => 'neutral.png',
    ':twisted:' => 'twisted.png',
      ':arrow:' => 'arrow.png',
        ':eek:' => 'eek.png',
      ':smile:' => 'smile.png',
   ':confused:' => 'confused.png',
       ':cool:' => 'cool.png',
       ':evil:' => 'evil.png',
    ':biggrin:' => 'biggrin.png',
       ':idea:' => 'idea.png',
    ':redface:' => 'redface.png',
       ':razz:' => 'razz.png',
   ':rolleyes:' => 'rolleyes.png',
       ':wink:' => 'wink.png',
        ':cry:' => 'cry.png',
  ':surprised:' => 'surprised.png',
        ':lol:' => 'lol.png',
        ':mad:' => 'mad.png',
   ':drooling:' => 'drooling.png',
     ':cowboy:' => 'cowboy.png',
':persevering:' => 'persevering.png',
    ':symbols:' => 'symbols.png',
       ':shit:' => 'shit.png',
    );
}
smilies_reset();

/**
 * Paging
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
function kratos_pages($range = 5){
    global $paged, $wp_query,$max_page;
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
 * Theme notice
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
add_action( 'welcome_panel', 'Kratos_admin_notice' );
function Kratos_admin_notice() {
  ?>
  <style type="text/css">
    .about-description a{
      text-decoration:none;
    }
  </style>
  <div class="notice notice-info">
  <p class="about-description">嗨，欢迎使用 Kratos 主题开始创作，同时欢迎您加入主题交流群：<a target="_blank" rel="nofollow" href="http://shang.qq.com/wpa/qunwpa?idkey=182bd07a135c085c88ab7e3de38f2b2d9a86983292355a4708926b99dcd5b89f">51880737</a></p>
  </div>
  <?php
}

/**
 * Admin footer text
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
function kratos_admin_footer_text($text) {
       $text = '<span id="footer-thankyou">感谢使用 <a href=http://cn.wordpress.org/ target="_blank">WordPress</a>进行创作，<a target="_blank" rel="nofollow" href="http://shang.qq.com/wpa/qunwpa?idkey=182bd07a135c085c88ab7e3de38f2b2d9a86983292355a4708926b99dcd5b89f">点击</a> 加入主题讨论群。</span>';
    return $text;
}

add_filter('admin_footer_text', 'kratos_admin_footer_text');
