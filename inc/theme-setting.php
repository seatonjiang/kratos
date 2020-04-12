<?php
/**
 * 站点相关函数
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.04.12
 */

// 标题配置
function title($title, $sep)
{
    global $paged, $page;
    if (is_feed()) {
        return $title;
    }
    $title .= get_bloginfo('name');
    $site_description = get_bloginfo('description', 'display');
    if ($site_description && (is_home() || is_front_page())) {
        $title = "{$title} {$sep} {$site_description}";
    }
    if ($paged >= 2 || $page >= 2) {
        $title = "{$title} {$sep} " . sprintf(__('第 %s 页', 'kratos'), max($paged, $page));
    }
    return $title;
}
add_filter('wp_title', 'title', 10, 2);

// Keywords 配置
function keywords()
{
    if (is_home() || is_front_page()) {
        echo kratos_option('seo_keywords');
    } elseif (is_category()) {
        echo kratos_option('seo_keywords') . ',';
        single_cat_title();
    } elseif (is_single()) {
        echo trim(wp_title('', false)) . ',';
        echo kratos_option('seo_keywords') . ',';
        if (has_tag()) {
            foreach (get_the_tags() as $tag) {
                echo $tag->name . ',';
            }
        }
        foreach (get_the_category() as $category) {
            echo $category->cat_name . ',';
        }
    } elseif (is_search()) {
        echo kratos_option('seo_keywords') . ',';
        the_search_query();
    } else {
        echo kratos_option('seo_keywords') . ',';
        echo trim(wp_title('', false));
    }
}

// Description 配置
function description()
{
    if (is_home() || is_front_page()) {
        echo trim(kratos_option('seo_description'));
    } elseif (is_category()) {
        $description = strip_tags(category_description());
        echo trim($description);
    } elseif (is_single()) {
        if (get_the_excerpt()) {
            echo get_the_excerpt();
        } else {
            global $post;
            $description = trim(str_replace(array("\r\n", "\r", "\n", "　", " "), " ", str_replace("\"", "'", strip_tags($post->post_content))));
            echo mb_substr($description, 0, 220, 'utf-8');
        }
    } elseif (is_search()) {
        echo '「';
        the_search_query();
        echo '」共找到 ';
        global $wp_query;
        echo $wp_query->found_posts;
        echo ' 个记录';
    } elseif (is_tag()) {
        $description = strip_tags(tag_description());
        echo trim($description);
    } else {
        $description = strip_tags(term_description());
        echo trim($description);
    }
}

// robots.txt 配置
add_filter('robots_txt', function ($output, $public) {
    if ('0' == $public) {
        return "User-agent: *\nDisallow: /\n";
    } else {
        if (!empty(kratos_option('seo_robots'))) {
            $output = esc_attr(strip_tags(kratos_option('seo_robots')));
        }
        return $output;
    }
}, 10, 2);

// 哀悼黑白站点
function mourning()
{
    if (is_home() && kratos_option('g_rip', false)) {
        echo '<style type="text/css">html{filter: grayscale(100%);-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);filter: progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);filter: gray;-webkit-filter: grayscale(1); } </style>';
    }
}

// 抓取图片链接（搜索引擎或者社交工具分享时抓取图片的链接）
function share_thumbnail_url()
{
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $post_thumbnail_id = get_post_thumbnail_id($post);
        $img = wp_get_attachment_image_src($post_thumbnail_id, 'full');
        $img = $img[0];
    } else {
        $content = $post->post_content;
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?); ?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        if (!empty($strResult[1])) {
            $img = $strResult[1][0];
        } else {
            $img = kratos_option('seo_shareimg', ASSET_PATH . '/assets/img/default.jpg');
        }
    }
    return $img;
}

// 支持上传 svg
add_filter('upload_mimes', 'upload_svg');
function upload_svg($existing_mimes = array())
{
    $existing_mimes['svg'] = 'image/svg+xml';
    return $existing_mimes;
}
