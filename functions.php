<?php

define( '_KRATOS_VERSION', '1.1.1' );

require_once( get_template_directory() . '/inc/widgets.php');

/**
 * 去除头部无用代码
 * @version 1.0
 * @package Vtrois
 */  
remove_action( 'wp_head', 'feed_links', 2 );   
remove_action( 'wp_head', 'feed_links_extra', 3 );   
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
 * 加载脚本
 * @version 1.0
 * @package Vtrois
 */  
function kratos_theme_scripts() {  
	$dir = get_template_directory_uri(); 
    if ( !is_admin() ) {  
		wp_enqueue_style( 'animate-style', $dir . '/css/animate.css', array(), '3.5.1'); 
        wp_enqueue_style( 'awesome-style', $dir . '/css/font-awesome.css', array(), '4.6.2');
		wp_enqueue_style( 'bootstrap-style', $dir . '/css/bootstrap.css', array(), '3.3.6');
		wp_enqueue_style( 'superfish-style', $dir . '/css/superfish.css', array(), 'r7');
        wp_enqueue_style( 'kratos-style', get_stylesheet_uri(), array(), _KRATOS_VERSION); 
        wp_enqueue_script( 'modernizr', $dir . '/js/modernizr.js' , array(), '2.6.2');
        wp_enqueue_script( 'jquerys', $dir . '/js/jquery.js' , array(), '2.1.4');
        wp_enqueue_script( 'easing', $dir . '/js/jquery.easing.js', array(), '1.3.0');
		wp_enqueue_script( 'bootstrap', $dir . '/js/bootstrap.min.js', array(), '3.3.6');
		wp_enqueue_script( 'waypoints', $dir . '/js/jquery.waypoints.min.js', array(), '4.0.0');
		wp_enqueue_script( 'stellar', $dir . '/js/jquery.stellar.min.js', array(), '0.6.2');
		wp_enqueue_script( 'hoverIntents', $dir . '/js/hoverIntent.js', array(), 'r7');
		wp_enqueue_script( 'superfish', $dir . '/js/superfish.js', array(), '1.0.0');
        wp_enqueue_script( 'kratos', $dir . '/js/kratos.js', array(),  _KRATOS_VERSION);
    }  
}  
add_action('wp_enqueue_scripts', 'kratos_theme_scripts');

/**
 * 替换Gravatar服务器
 * @version 1.0
 * @package Vtrois
 */
function kratos_get_avatar( $avatar ) {
$avatar = preg_replace( "/http:\/\/(www|\d).gravatar.com/","http://cn.gravatar.com",$avatar );
return $avatar;
}
add_filter( 'get_avatar', 'kratos_get_avatar' );

/**
 * 关键词设置
 * @version 1.0
 * @package Vtrois
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
 * 描述设置
 * @version 1.0
 * @package Vtrois
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
 * 标题设置
 * @version 1.0
 * @package Vtrois
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
 * 主题更新
 * @version 1.0
 * @package Vtrois
 */
require_once( get_template_directory() . '/inc/version.php' );
$kratos_update_checker = new ThemeUpdateChecker(
	'Kratos', 
	'http://soft.vtrois.com/wordpress/theme/kratos/upgrade.json'
);

/**
 * 后台控制模块
 * @version 1.0
 * @package Vtrois
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
  $menu['menu_title'] = 'Kratos Options';
  $menu['menu_slug'] = 'kratos';
  return $menu;
}
add_filter( 'optionsframework_menu', 'kratos_options_menu_filter' );

/**
 * 菜单导航注册
 * @version 1.0
 * @package Vtrois
 */
function kratos_register_nav_menu() {
		register_nav_menus(array('header_menu' => '顶部菜单导航'));
	}
add_action('after_setup_theme', 'kratos_register_nav_menu');

/**
 * 高亮当前激活的菜单
 * @version 1.0
 * @package Vtrois
 */
function kratos_active_menu_class($classes) {
	if (in_array('current-menu-item', $classes) OR in_array('current-menu-ancestor', $classes))
		$classes[] = 'active';
	return $classes;
}
add_filter('nav_menu_css_class', 'kratos_active_menu_class');

/**
 * 文章缩略图
 * @version 1.0
 * @package Vtrois
 */
function kratos_blog_thumbnail() {

	global $post;
	if (has_post_thumbnail()) {
		the_post_thumbnail(array(750, ), array('class' => 'kratos-entry-thumb'));
	}else {}
}
add_theme_support( "post-thumbnails" );

/**
 * 摘要长度及后缀
 * @version 1.0
 * @package Vtrois
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
 * 文章阅读量统计
 * @version 1.0
 * @package Vtrois
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
 * 轮播图片
 * @version 1.0
 * @package Vtrois
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
            $output .= '<div id="slide" class="carousel slide animate-box" data-ride="carousel">';
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
 * 文章点赞功能
 * @version 1.0
 * @package Vtrois
 */
function kratos_love(){
    global $wpdb,$post;
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ( $action == 'love'){
        $kratos_raters = get_post_meta($id,'kratos_love',true);
        $expire = time() + 99999999;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie('kratos_love_'.$id,$id,$expire,'/',$domain,false);
        if (!$kratos_raters || !is_numeric($kratos_raters)) {
            update_post_meta($id, 'kratos_love', 1);
        } 
        else {
            update_post_meta($id, 'kratos_love', ($kratos_raters + 1));
        }
        echo get_post_meta($id,'kratos_love',true);
    } 
    die;
}
add_action('wp_ajax_nopriv_kratos_love', 'kratos_love');
add_action('wp_ajax_kratos_love', 'kratos_love');

/**
 * 评论表情
 * @version 1.0
 * @package Vtrois
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
    ':mrgreen:' => 'icon_mrgreen.gif',
    ':exclaim:' => 'icon_exclaim.gif',
    ':neutral:' => 'icon_neutral.gif',
    ':twisted:' => 'icon_twisted.gif',
      ':arrow:' => 'icon_arrow.gif',
        ':eek:' => 'icon_eek.gif',
      ':smile:' => 'icon_smile.gif',
   ':confused:' => 'icon_confused.gif',
       ':cool:' => 'icon_cool.gif',
       ':evil:' => 'icon_evil.gif',
    ':biggrin:' => 'icon_biggrin.gif',
       ':idea:' => 'icon_idea.gif',
    ':redface:' => 'icon_redface.gif',
       ':razz:' => 'icon_razz.gif',
   ':rolleyes:' => 'icon_rolleyes.gif',
       ':wink:' => 'icon_wink.gif',
        ':cry:' => 'icon_cry.gif',
  ':surprised:' => 'icon_surprised.gif',
        ':lol:' => 'icon_lol.gif',
        ':mad:' => 'icon_mad.gif',
        ':sad:' => 'icon_sad.gif',
    );
}
smilies_reset();

/**
 * 邮件提醒
 * @version 1.0
 * @package Vtrois
 */
function kratos_comment_mail_notify($comment_id) {
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $spam_confirmed = $comment->comment_approved;
    if (($parent_id != '') && ($spam_confirmed != 'spam')) {
        $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])); //e-mail 發出點, no-reply 可改為可用的 e-mail.
        $to = trim(get_comment($parent_id)->comment_author_email);
        $subject = '你在 ' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) .' 的留言有了新回复';
        $message = '
            <div style="background: #F1F1F1;width: 100%;padding: 50px 0;">
                <div style="background: #FFF;width: 750px;margin: 0 auto;">
                    <div style="padding: 10px 60px;background: #50A5E6;color: #FFF;font-size: 24px; font-weight: bold;"><a href="' . get_option('home') . '" style="text-decoration: none;color: #FFF;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</a></div>
                    <h1 style="text-align: center;font-size: 26px;line-height: 50px;margin: 30px 60px;font-weight: bold;font-family: 宋体,微软雅黑,serif;">
                        你在 [' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '] 的留言有了新回复
                    </h1>
                    <div style="border-bottom: 1px solid #333;height: 0px;margin: 0 60px;"></div>
                    <div style="margin: 30px 60px;color: #363636;">
                        <p style="font-size: 16px;font-weight: bold;line-height: 30px;">Hi，' . trim(get_comment($parent_id)->comment_author) . '！</p>
                        <div style="font-size: 16px;">
                            <p><strong>你曾在本博客《' . get_the_title($comment->comment_post_ID) . '》的留言为：</strong></p>
                            <blockquote style="border-left: 4px solid #ddd; padding: 5px 10px; line-height: 22px;">' . trim(get_comment($parent_id)->comment_content) . '</blockquote>
                        </div>
                        <div style="font-size: 16px;">
                            <p><strong>' . trim($comment->comment_author) . ' 给你的回复为：</strong></p>
                            <blockquote style="border-left: 4px solid #ddd; padding: 5px 10px; line-height: 22px;">' . trim($comment->comment_content) . ' </blockquote>
                        </div>
                        <p style="font-size: 16px;line-height: 30px;">
                            你可以点击此链接 <a href="' . htmlspecialchars(get_comment_link($parent_id)) . '" style="text-decoration: none;color: #50A5E6;">查看完整回复内容</a> | 欢迎再次来访 <a href="' . get_option('home') . '" style="text-decoration: none;color: #50A5E6;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</a>
                        </p>
                        <p style="color: #999;">(此邮件由系统自动发出，请勿回复！)</p>
                    </div>
                    <div style="border-bottom: 1px solid #dfdfdf;height: 0px;margin: 0 60px;"></div>
                    <div style="text-align: right;padding: 30px 60px;color: #999;">
                        <p>Powered by ' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) .'</p>
                    </div>
                </div>
            </div>';

        $from = "From: \"" . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail( $to, $subject, $message, $headers );
    }
}
function kratos_comment_approved($comment) {
    if(is_email($comment->comment_author_email)) {

        $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])); //e-mail 發出點, no-reply 可改為可用的 e-mail.
        $to = trim($comment->comment_author_email);
        $post_link = get_permalink($comment->comment_post_ID);
        $subject = '你在 ' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) .' 的留言已通过审核';
        $message = '
            <div style="background: #F1F1F1;width: 100%;padding: 50px 0;">
                <div style="background: #FFF;width: 750px;margin: 0 auto;">
                    <div style="padding: 10px 60px;background: #50A5E6;color: #FFF;font-size: 24px; font-weight: bold;"><a href="' . get_option('home') . '" style="text-decoration: none;color: #FFF;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</a></div>
                    <h1 style="text-align: center;font-size: 26px;line-height: 50px;margin: 30px 60px;font-weight: bold;font-family: 宋体,微软雅黑,serif;">
                        你在 [' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '] 的留言通过了审核
                    </h1>
                    <div style="border-bottom: 1px solid #333;height: 0px;margin: 0 60px;"></div>
                    <div style="margin: 30px 60px;color: #363636;">
                        <p style="font-size: 16px;font-weight: bold;line-height: 30px;">Hi，' . trim($comment->comment_author) . '！</p>
                        <div style="font-size: 16px;">
                            <p><strong>你在本博客《' . get_the_title($comment->comment_post_ID) . '》中的留言：</strong></p>
                            <blockquote style="border-left: 4px solid #ddd; padding: 5px 10px; line-height: 22px;">'. trim($comment->comment_content) . '</blockquote>
                            <p>
                                通过了管理员的审核。
                            </p>
                        </div>

                        <p style="font-size: 16px;line-height: 30px;">
                            你可以点击此链接 <a href="'.get_comment_link( $comment->comment_ID ).'" style="text-decoration: none;color: #50A5E6;" >查看完整回复内容</a> | 欢迎再次来访 <a href="' . get_option('home') . '" style="text-decoration: none;color: #50A5E6;">' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . '</a>
                        </p>
                        <p style="color: #999;">(此邮件由系统自动发出，请勿回复！)</p>
                    </div>
                    <div style="border-bottom: 1px solid #dfdfdf;height: 0px;margin: 0 60px;"></div>
                    <div style="text-align: right;padding: 30px 60px;color: #999;">
                        <p>Powered by ' . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) .'</p>
                    </div>
                </div>
            </div>';

        $from = "From: \"" . htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail( $to, $subject, $message, $headers );
    }
}
add_action('comment_post', 'kratos_comment_mail_notify');
add_action('comment_unapproved_to_approved', 'kratos_comment_approved');

/**
 * 分页
 * @version 1.0
 * @package Vtrois
 */
function kratos_pages($range = 5){
    global $paged, $wp_query;
    if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
    if($max_page > 1){if(!$paged){$paged = 1;}
	echo "<ul class='pagination pull-right'>";
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
        echo "</ul>";
	}
}

/**
 * 后台左侧页脚文字
 * @version 1.1
 * @package Vtrois
 */
function kratos_admin_footer_text($text) {
	   $text = '<span id="footer-thankyou">感谢使用 <a href=http://cn.wordpress.org/ target="_blank">WordPress</a> 进行创作，并使用 <a href="http://www.vtrois.com/projects/theme-kratos.html" target="_blank">Kratos</a> 主题样式，<a target="_blank" rel="nofollow" href="http://shang.qq.com/wpa/qunwpa?idkey=b7fc2227eaa18a1ec540006ac6de1f3bbc1673fe1ce25ed14a9de68a832c379d">点击</a> 加入主题讨论群。</span>';
	return $text;
}

add_filter('admin_footer_text', 'kratos_admin_footer_text');