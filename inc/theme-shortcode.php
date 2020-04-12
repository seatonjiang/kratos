<?php
/**
 * 文章短代码
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.04.12
 */

function h2title($atts, $content = null, $code = "")
{
    $return = '<h2 class="title">';
    $return .= $content;
    $return .= '</h2>';
    return $return;
}
add_shortcode('h2title', 'h2title');

function success($atts, $content = null, $code = "")
{
    $return = '<div class="alert alert-success">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('success', 'success');

function info($atts, $content = null, $code = "")
{
    $return = '<div class="alert alert-info">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('info', 'info');

function warning($atts, $content = null, $code = "")
{
    $return = '<div class="alert alert-warning">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('warning', 'warning');

function danger($atts, $content = null, $code = "")
{
    $return = '<div class="alert alert-danger">';
    $return .= $content;
    $return .= '</div>';
    return $return;
}
add_shortcode('danger', 'danger');

function wymusic($atts, $content = null, $code = "")
{
    $return = '<div class="mb-3"><iframe style="width:100%" frameborder="no" border="0" marginwidth="0" marginheight="0" height=86 src="//music.163.com/outchain/player?type=2&id=';
    $return .= $content;
    $return .= '&auto=' . kratos_option('g_163mic', false) . '&height=66"></iframe></div>';
    return $return;
}
add_shortcode('music', 'wymusic');

function bdbtn($atts, $content = null, $code = "")
{
    $return = '<a class="downbtn" href="';
    $return .= $content;
    $return .= '" target="_blank"><i class="kicon i-download mr-1"></i>立即下载</a>';
    return $return;
}
add_shortcode('bdbtn', 'bdbtn');

function kbd($atts, $content = null, $code = "")
{
    $return = '<kbd>';
    $return .= $content;
    $return .= '</kbd>';
    return $return;
}
add_shortcode('kbd', 'kbd');

function nrmark($atts, $content = null, $code = "")
{
    $return = '<mark>';
    $return .= $content;
    $return .= '</mark>';
    return $return;
}
add_shortcode('mark', 'nrmark');

function striped($atts, $content = null, $code = "")
{
    $return = '<div class="progress"><div class="progress-bar" role="progressbar" style="width:';
    $return .= $content;
    $return .= '%;" aria-valuenow="';
    $return .= $content;
    $return .= '" aria-valuemin="0" aria-valuemax="100">';
    $return .= $content;
    $return .= '%</div></div>';
    return $return;
}
add_shortcode('striped', 'striped');

function successbox($atts, $content = null, $code = "")
{
    extract(shortcode_atts(array("title" => __('标题内容', 'kratos')), $atts));
    $return = '<div class="card border-success text-white mb-3"><div class="card-header bg-success">';
    $return .= $title;
    $return .= '</div><div class="card-body"><p class="card-text">';
    $return .= $content;
    $return .= '</p></div></div>';
    return $return;
}
add_shortcode('successbox', 'successbox');

function infobox($atts, $content = null, $code = "")
{
    extract(shortcode_atts(array("title" => __('标题内容', 'kratos')), $atts));
    $return = '<div class="card border-info text-white mb-3"><div class="card-header bg-info">';
    $return .= $title;
    $return .= '</div><div class="card-body"><p class="card-text">';
    $return .= $content;
    $return .= '</p></div></div>';
    return $return;
}
add_shortcode('infobox', 'infobox');

function warningbox($atts, $content = null, $code = "")
{
    extract(shortcode_atts(array("title" => __('标题内容', 'kratos')), $atts));
    $return = '<div class="card border-warning text-white mb-3"><div class="card-header bg-warning">';
    $return .= $title;
    $return .= '</div><div class="card-body"><p class="card-text">';
    $return .= $content;
    $return .= '</p></div></div>';
    return $return;
}
add_shortcode('warningbox', 'warningbox');

function dangerbox($atts, $content = null, $code = "")
{
    extract(shortcode_atts(array("title" => __('标题内容', 'kratos')), $atts));
    $return = '<div class="card border-danger text-white mb-3"><div class="card-header bg-danger">';
    $return .= $title;
    $return .= '</div><div class="card-body"><p class="card-text">';
    $return .= $content;
    $return .= '</p></div></div>';
    return $return;
}
add_shortcode('dangerbox', 'dangerbox');

function vqq($atts, $content = null, $code = "")
{
    $return = '<div class="video-container"><iframe frameborder="0" src="https://v.qq.com/txp/iframe/player.html?vid=';
    $return .= $content;
    $return .= '" allowFullScreen="true"></iframe></div>';
    return $return;
}
add_shortcode('vqq', 'vqq');

function youtube($atts, $content = null, $code = "")
{
    $return = '<div class="video-container"><iframe height="498" width="750" src="https://www.youtube.com/embed/';
    $return .= $content;
    $return .= '" frameborder="0" allowfullscreen="allowfullscreen"></iframe></div>';
    return $return;
}
add_shortcode('youtube', 'youtube');

function bilibili($atts, $content = null, $code = "")
{
    extract(shortcode_atts(array("cid" => 'cid'), $atts));
    $return = '<div class="video-container"><iframe src="//player.bilibili.com/player.html?cid=';
    $return .= $cid;
    $return .= '&aid=';
    $return .= $content;
    $return .= '&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe></div>';
    return $return;
}
add_shortcode('bilibili', 'bilibili');

add_action('init', 'more_button');
function more_button()
{
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    if (get_user_option('rich_editing') == 'true') {
        add_filter('mce_external_plugins', 'add_plugin');
        add_filter('mce_buttons', 'register_button');
    }
}

function add_more_buttons($buttons) {
    $buttons[] = 'hr';
    $buttons[] = 'wp_page';
    $buttons[] = 'fontsizeselect';
    $buttons[] = 'styleselect';
return $buttons;
}
add_filter("mce_buttons", "add_more_buttons");

function register_button($buttons)
{
    array_push($buttons, " ", "h2title");
    array_push($buttons, " ", "kbd");
    array_push($buttons, " ", "mark");
    array_push($buttons, " ", "striped");
    array_push($buttons, " ", "bdbtn");
    array_push($buttons, " ", "music");
    array_push($buttons, " ", "vqq");
    array_push($buttons, " ", "youtube");
    array_push($buttons, " ", "bilibili");
    array_push($buttons, " ", "success");
    array_push($buttons, " ", "info");
    array_push($buttons, " ", "warning");
    array_push($buttons, " ", "danger");
    array_push($buttons, " ", "successbox");
    array_push($buttons, " ", "infoboxs");
    array_push($buttons, " ", "warningbox");
    array_push($buttons, " ", "dangerbox");
    return $buttons;
}

function add_plugin($plugin_array)
{
    $plugin_array['h2title'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['kbd'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['mark'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['striped'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['bdbtn'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['music'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['vqq'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['youtube'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['bilibili'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['success'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['info'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['warning'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['danger'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['successbox'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['infoboxs'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['warningbox'] = ASSET_PATH . '/assets/js/buttons/more.js';
    $plugin_array['dangerbox'] = ASSET_PATH . '/assets/js/buttons/more.js';
    return $plugin_array;
}
