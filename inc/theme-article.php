<?php

/**
 * 文章相关函数
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.05.01
 */

// 文章链接添加 target 和 rel
function content_nofollow($content)
{
    $regexp = "<a\s[^>]*href=['\"][^#]([^'\"]*?)\\1[^>]*>";
    if (preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
        if (!empty($matches)) {
            $srcUrl = get_option('siteurl');
            for ($i = 0; $i < count($matches); $i++) {
                $tag = $matches[$i][0];
                $tag2 = $matches[$i][0];
                $url = $matches[$i][0];
                $noFollow = '';
                $pattern = '/target\s*=\s*"\s*_blank\s*"/';
                preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                if (count($match) < 1) {
                    $noFollow .= ' target="_blank" ';
                }

                $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
                preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                if (count($match) < 1) {
                    $noFollow .= ' rel="nofollow" ';
                }

                $pos = strpos($url, $srcUrl);
                if ($pos === false) {
                    $tag = rtrim($tag, '>');
                    $tag .= $noFollow . '>';
                    $content = str_replace($tag2, $tag, $content);
                }
            }
        }
    }
    $content = str_replace(']]>', ']]>', $content);
    return $content;
}
add_filter('the_content', 'content_nofollow');

// 文章点赞
function love()
{
    global $wpdb, $post;
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ($action == 'love') {
        $raters = get_post_meta($id, 'love', true);
        $expire = time() + 99999999;
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie('love_' . $id, $id, $expire, '/', $domain, false);
        if (!$raters || !is_numeric($raters)) {
            update_post_meta($id, 'love', 1);
        } else {
            update_post_meta($id, 'love', ($raters + 1));
        }
        echo get_post_meta($id, 'love', true);
    }
    die;
}
add_action('wp_ajax_nopriv_love', 'love');
add_action('wp_ajax_love', 'love');

// 文章阅读次数统计
function set_post_views()
{
    if (is_singular()) {
        global $post;
        $post_ID = $post->ID;
        if ($post_ID) {
            $post_views = (int) get_post_meta($post_ID, 'views', true);
            if (!update_post_meta($post_ID, 'views', ($post_views + 1))) {
                add_post_meta($post_ID, 'views', 1, true);
            }
        }
    }
}
add_action('wp_head', 'set_post_views');

function get_post_views($echo = 1)
{
    global $post;
    $post_ID = $post->ID;
    $views = (int) get_post_meta($post_ID, 'views', true);
    return $views;
}

// 文章列表简介内容
function custom_excerpt_length($length)
{
    return kratos_option('g_excerpt_length', '260');
}
add_filter('excerpt_length', 'custom_excerpt_length');

// 开启特色图
add_theme_support("post-thumbnails");

// 生成适合特色图的比例图片
add_image_size('kratos-thumbnail', 512, 288, true);

// 强制图片链接到媒体文件
add_action('after_setup_theme', 'default_attachment_display_settings');
function default_attachment_display_settings()
{
    update_option('image_default_link_type', 'file');
}

// 文章特色图片
function post_thumbnail()
{
    global $post;
    $img_id = get_post_thumbnail_id();
    $img_url = wp_get_attachment_image_src($img_id, 'kratos-thumbnail');
    if (is_array($img_url)) {
        $img_url = $img_url[0];
    }
    if (has_post_thumbnail()) {
        echo '<img src="' . $img_url . '" />';
    } else {
        $content = $post->post_content;
        $img_preg = "/<img (.*?)src=\"(.+?)\".*?>/";
        preg_match($img_preg, $content, $img_src);
        $img_count = count($img_src) - 1;
        if (isset($img_src[$img_count])) {
            $img_val = $img_src[$img_count];
        }
        if (!empty($img_val)) {
            echo '<img src="' . $img_val . '" />';
        } else {
            echo '<img src="' . kratos_option('g_postthumbnail', ASSET_PATH . '/assets/img/default.jpg') . '" />';
        }
    }
}

// 文章列表分页
function pagelist($range = 5)
{
    global $paged, $wp_query, $max_page;
    if (!$max_page) {
        $max_page = $wp_query->max_num_pages;
    }
    if ($max_page > 1) {
        if (!$paged) {
            $paged = 1;
        }
        echo "<div class='paginations'>";
        if ($paged > 1) {
            echo '<a href="' . get_pagenum_link($paged - 1) . '" class="prev" title="上一页"><i class="kicon i-larrows"></i></a>';
        }
        if ($max_page > $range) {
            if ($paged < $range) {
                for ($i = 1; $i <= $range; $i++) {
                    if ($i == $paged) {
                        echo '<span class="page-numbers current">' . $i . '</span>';
                    } else {
                        echo "<a href='" . get_pagenum_link($i) . "'>$i</a>";
                    }
                }
                echo '<span class="page-numbers dots">…</span>';
                echo "<a href='" . get_pagenum_link($max_page) . "'>$max_page</a>";
            } elseif ($paged >= ($max_page - ceil(($range / 2)))) {
                if ($paged != 1) {
                    echo "<a href='" . get_pagenum_link(1) . "' class='extend' title='首页'>1</a>";
                    echo '<span class="page-numbers dots">…</span>';
                }
                for ($i = $max_page - $range + 1; $i <= $max_page; $i++) {
                    if ($i == $paged) {
                        echo '<span class="page-numbers current">' . $i . '</span>';
                    } else {
                        echo "<a href='" . get_pagenum_link($i) . "'>$i</a>";
                    }
                }
            } elseif ($paged >= $range && $paged < ($max_page - ceil(($range / 2)))) {
                if ($paged != 1) {
                    echo "<a href='" . get_pagenum_link(1) . "' class='extend' title='首页'>1</a>";
                    echo '<span class="page-numbers dots">…</span>';
                }
                for ($i = ($paged - ceil($range / 3)); $i <= ($paged + ceil(($range / 3))); $i++) {
                    if ($i == $paged) {
                        echo '<span class="page-numbers current">' . $i . '</span>';
                    } else {
                        echo "<a href='" . get_pagenum_link($i) . "'>$i</a>";
                    }
                }
                echo '<span class="page-numbers dots">…</span>';
                echo "<a href='" . get_pagenum_link($max_page) . "'>$max_page</a>";
            }
        } else {
            for ($i = 1; $i <= $max_page; $i++) {
                if ($i == $paged) {
                    echo '<span class="page-numbers current">' . $i . '</span>';
                } else {
                    echo "<a href='" . get_pagenum_link($i) . "'>$i</a>";
                }
            }
        }
        if ($paged < $max_page) {
            echo '<a href="' . get_pagenum_link($paged + 1) . '" class="next" title="下一页"><i class="kicon i-rarrows"></i></a>';
        }
        echo "</div>";
    }
}

// 文章评论
function comment_scripts()
{
    wp_enqueue_script('comment', ASSET_PATH . '/assets/js/comments.min.js', array(), THEME_VERSION);
    wp_localize_script('comment', 'ajaxcomment', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'order' => get_option('comment_order'),
        'compost' => __('评论正在提交中', 'kratos'),
        'comsucc' => __('评论提交成功', 'kratos'),
    ));
}
add_action('wp_enqueue_scripts', 'comment_scripts');

function comment_err($a)
{
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo $a;
    exit;
}

function comment_callback()
{
    $comment = wp_handle_comment_submission(wp_unslash($_POST));
    if (is_wp_error($comment)) {
        $data = $comment->get_error_data();
        if (!empty($data)) {
            comment_err($comment->get_error_message());
        } else {
            exit;
        }
    }
    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);
    $GLOBALS['comment'] = $comment;
?>
    <li class="comment cleanfix" id="comment-<?php echo esc_attr(comment_ID()); ?>">
        <div class="avatar float-left d-inline-block mr-2">
            <?php if (function_exists('get_avatar') && get_option('show_avatars')) {
                echo get_avatar($comment, 50);
            } ?>
        </div>
        <div class="info clearfix">
            <cite class="author_name"><?php echo get_comment_author_link(); ?></cite>
            <div class="content pb-2">
                <?php comment_text(); ?>
            </div>
            <div class="meta clearfix">
                <div class="date d-inline-block float-left"><?php echo get_comment_date(); ?><?php if (current_user_can('edit_posts')) {
                                                                                                    echo '<span class="ml-2">';
                                                                                                    edit_comment_link(__('编辑', 'kratos'));
                                                                                                    echo '</span>';
                                                                                                }; ?>
                </div>
            </div>
        </div>
    </li>
<?php die();
}

add_action('wp_ajax_nopriv_ajax_comment', 'comment_callback');
add_action('wp_ajax_ajax_comment', 'comment_callback');

function comment_post($incoming_comment)
{
    $incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);
    $incoming_comment['comment_content'] = str_replace("'", '&apos;', $incoming_comment['comment_content']);
    return ($incoming_comment);
}
add_filter('preprocess_comment', 'comment_post', '', 1);

function comment_display($comment_to_display)
{
    $comment_to_display = str_replace('&apos;', "'", $comment_to_display);
    return $comment_to_display;
}
add_filter('comment_text', 'comment_display', '', 1);

function comment_callbacks($comment, $args, $depth = 2)
{
    $GLOBALS['comment'] = $comment; ?>
    <li class="comment cleanfix" id="comment-<?php echo esc_attr(comment_ID()); ?>">
        <div class="avatar float-left d-inline-block mr-2">
            <?php if (function_exists('get_avatar') && get_option('show_avatars')) {
                echo get_avatar($comment, 50);
            } ?>
        </div>
        <div class="info clearfix">
            <cite class="author_name"><?php echo get_comment_author_link(); ?></cite>
            <div class="content pb-2">
                <?php comment_text(); ?>
            </div>
            <div class="meta clearfix">
                <div class="date d-inline-block float-left"><?php echo get_comment_date(); ?><?php if (current_user_can('edit_posts')) {
                                                                                                    echo '<span class="ml-2">';
                                                                                                    edit_comment_link(__('编辑', 'kratos'));
                                                                                                    echo '</span>';
                                                                                                }; ?>
                </div>
                <div class="tool reply ml-2 d-inline-block float-right">
                    <?php
                    $defaults = array('add_below' => 'comment', 'respond_id' => 'respond', 'reply_text' => '<i class="kicon i-reply"></i><span class="ml-1">' . __('回复', 'kratos') . '</span>');
                    comment_reply_link(array_merge($defaults, array('depth' => $depth, 'max_depth' => $args['max_depth'])));
                    ?>
                </div>
            </div>
        </div>
    <?php
}

// 文章评论表情
if (empty(get_option('use_smilies'))) {
    update_option('use_smilies', 1);
}

function custom_smilies_src($img_src, $img, $siteurl)
{
    return ASSET_PATH . '/assets/img/smilies/' . $img;
}
add_filter('smilies_src', 'custom_smilies_src', 1, 10);

function disable_emojis_tinymce($plugins)
{
    return array_diff($plugins, array('wpemoji'));
}

function smilies_reset()
{
    global $wpsmiliestrans;

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
        ':lol:' => 'lol.png',
        ':mad:' => 'mad.png',
        ':drooling:' => 'drooling.png',
        ':persevering:' => 'persevering.png',
    );
}
smilies_reset();

function smilies_custom_button()
{
    printf('<style> .smilies-wrap { background: #fff !important; border: 1px solid #ccc !important; box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.24) !important; padding: 10px !important; position: absolute !important; top: 60px !important; width: 400px !important; display: none !important; } .smilies-wrap img { height: 24px !important; width: 24px !important; cursor: pointer !important; margin-bottom: 5px !important; } .is-active.smilies-wrap { display: block !important; } @media screen and (max-width: 782px) { #wp-content-media-buttons a { font-size: 14px !important; padding: 0 14px !important; } } </style><a id="insert-media-button" style="position:relative" class="button insert-smilies add_smilies" data-editor="content" href="javascript:;"><span class="dashicons dashicons-smiley" style="line-height: 26px;"></span>' . __('添加表情', 'kratos') . '</a> <div class="smilies-wrap">' . get_wpsmiliestrans() . '</div> <script>jQuery(document).ready(function () { jQuery(document).on("click", ".insert-smilies", function () { if (jQuery(".smilies-wrap").hasClass("is-active")) { jQuery(".smilies-wrap").removeClass("is-active"); } else { jQuery(".smilies-wrap").addClass("is-active"); } }); jQuery(document).on("click", ".add-smily", function () { send_to_editor(" " + jQuery(this).data("smilies") + " "); jQuery(".smilies-wrap").removeClass("is-active"); return false; }); });</script>');
}
add_action('media_buttons', 'smilies_custom_button');

function get_wpsmiliestrans()
{
    global $wpsmiliestrans;
    global $output;

    $wpsmilies = array_unique($wpsmiliestrans);
    foreach ($wpsmilies as $alt => $src_path) {
        $output .= '<a class="add-smily" data-smilies="' . $alt . '"><img class="wp-smiley" src="' . ASSET_PATH . '/assets/img/smilies/' . rtrim($src_path, "png") . 'png" /></a>';
    }
    return $output;
}

if (!kratos_option('g_gutenberg', false)) {
    // 禁用 Gutenberg 编辑器
    add_filter('use_block_editor_for_post', '__return_false');
    add_filter('gutenberg_use_widgets_block_editor', '__return_false');
    add_filter('use_widgets_block_editor', '__return_false');
    remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');

    // 删除前端的block library的css资源，
    add_action('wp_enqueue_scripts', 'remove_block_library_css', 100);
    function remove_block_library_css()
    {
        wp_dequeue_style('wp-block-library');
    }

    // 禁用 Auto Embeds
    remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);
}

// 文章评论增强
function comment_add_at($comment_text, $comment = null)
{
    if ($comment->comment_parent > 0) {
        $comment_text = '<span>@' . get_comment_author($comment->comment_parent) . '</span> ' . $comment_text;
    }

    return $comment_text;
}
add_filter('comment_text', 'comment_add_at', 20, 2);

function recover_comment_fields($comment_fields)
{
    $comment = array_shift($comment_fields);
    $comment_fields = array_merge($comment_fields, array('comment' => $comment));
    return $comment_fields;
}
add_filter('comment_form_fields', 'recover_comment_fields');

$new_meta_boxes =
    array(
        "description" => array(
            "name" => "seo_description",
            "std" => "",
            "title" => __('描述', 'kratos')
        ),
        "keywords" => array(
            "name" => "seo_keywords",
            "std" => "",
            "title" => __('关键词', 'kratos')
        )
    );

function seo_meta_boxes()
{
    $post_types = get_post_types();
    add_meta_box('meta-box-id', __('SEO 设置', 'kratos'), 'post_seo_callback', $post_types);
}
add_action('add_meta_boxes', 'seo_meta_boxes');

function post_seo_callback($post)
{
    global $new_meta_boxes;

    foreach ($new_meta_boxes as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'] . '_value', true);

        if ($meta_box_value == "")
            $meta_box_value = $meta_box['std'];

        echo '<h3 style="font-size: 14px; padding: 9px 0; margin: 0; line-height: 1.4;">' . $meta_box['title'] . '</h3>';
        echo '<textarea cols="60" rows="3" style="width:100%" name="' . $meta_box['name'] . '_value">' . $meta_box_value . '</textarea><br/>';
    }

    echo '<input type="hidden" name="metaboxes_nonce" id="metaboxes_nonce" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
}

if (kratos_option('g_image_filter', true)) {
    add_action('admin_footer-post-new.php', 'fanly_mediapanel_lock_uploaded');
    add_action('admin_footer-post.php', 'fanly_mediapanel_lock_uploaded');
    function fanly_mediapanel_lock_uploaded()
    {
        echo '<script type="text/javascript">var $i=0;jQuery(document).on("DOMNodeInserted", function(){if(jQuery("#media-attachment-filters").length>0&&$i==0){jQuery(\'select.attachment-filters [value="uploaded"]\').attr(\'selected\',true).parent().trigger(\'change\');$i++;}});</script>';
    }
}

function wpdocs_save_meta_box($post_id)
{
    global $new_meta_boxes;

    if (!wp_verify_nonce(isset($_POST['metaboxes_nonce']) ? $_POST['metaboxes_nonce'] : null, plugin_basename(__FILE__)))
        return;

    if (!current_user_can('edit_posts', $post_id))
        return;

    foreach ($new_meta_boxes as $meta_box) {
        $data = $_POST[$meta_box['name'] . '_value'];

        if ($data == "")
            delete_post_meta($post_id, $meta_box['name'] . '_value', get_post_meta($post_id, $meta_box['name'] . '_value', true));
        else
            update_post_meta($post_id, $meta_box['name'] . '_value', $data);
    }
}
add_action('save_post', 'wpdocs_save_meta_box');

// 主页轮播
function kratos_carousel()
{
    if (kratos_option('g_carousel', false)) {
        $slide = 0;
        $item = 0;
        $output = '<div id="indexCarousel" class="carousel article-carousel slide" data-ride="carousel"> <ol class="carousel-indicators">';

        if (!empty(kratos_option('carousel_group'))) {

            foreach (kratos_option('carousel_group') as $group_item) {
                $active = null;
                if ($slide == 0) {
                    $active = 'class="active"';
                }
                $output .= '<li data-target="#indexCarousel" data-slide-to="' . $slide . '" ' . $active . '></li>';
                $slide++;
            }

            $output .= '</ol><div class="carousel-inner">';

            foreach (kratos_option('carousel_group') as $group_item) {
                $active = null;
                if ($item == 0) {
                    $active = 'active';
                }
                $output .= '
                <div class="carousel-item ' . $active . '">
                <a href="' . $group_item['c_url'] . '"><img src="' . $group_item['c_img'] . '" class="d-block w-100"></a>
                    <div class="carousel-caption d-none d-md-block">
                        <h5 style="color:' . $group_item['c_color'] . '">' . $group_item['c_title'] . '</h5>
                        <p style="color:' . $group_item['c_color'] . '">' . $group_item['c_subtitle'] . '</p>
                    </div>
                </div>';
                $item++;
            }

            $output .= '</div> <a class="carousel-control-prev" href="#indexCarousel" role="button" data-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span> </a> <a class="carousel-control-next" href="#indexCarousel" role="button" data-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span> </a> </div>';
        }

        echo $output;
    }
}

// 获取文章评论数量
function findSinglecomments($postid = 0, $which = 0)
{
    $comments = get_comments('status=approve&type=comment&post_id=' . $postid);
    if ($comments) {
        $i = 0;
        $j = 0;
        $commentusers = array();
        foreach ($comments as $comment) {
            ++$i;
            if ($i == 1) {
                $commentusers[] = $comment->comment_author_email;
                ++$j;
            }
            if (!in_array($comment->comment_author_email, $commentusers)) {
                $commentusers[] = $comment->comment_author_email;
                ++$j;
            }
        }
        $output = array($j, $i);
        $which = ($which == 0) ? 0 : 1;
        return $output[$which];
    }
    return 0;
}

// 文章目录功能
function toc_content($content)
{
    if (is_singular()) {
        global $toc_count;
        global $toc;

        $toc = array();
        $toc_count = 0;
        $toc_depth = 3;

        $regex = '#<h([1-' . $toc_depth . '])(.*?)>(.*?)</h\\1>#';
        $content = preg_replace_callback($regex, 'toc_replace_heading', $content);
    }
    return $content;
}
add_filter('the_content', 'toc_content');

function toc_replace_heading($content)
{
    global $toc_count;
    global $toc;

    $toc_count++;

    $toc[] = array('text' => trim(strip_tags($content[3])), 'depth' => $content[1], 'count' => $toc_count);

    return "<h{$content[1]} {$content[2]}><a name=\"toc-{$toc_count}\"></a>{$content[3]}</h{$content[1]}>";
}
