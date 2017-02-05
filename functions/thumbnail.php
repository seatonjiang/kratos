<?php
/**
 * 文章缩略图获取(TimThumb)
 * @param  integer $width  宽
 * @param  integer $height 高
 * @param  boolean $flag   是否包含 a 链接
 * @return string
 */
function kratos_blog_thumbnail_tim( $width = 180,$height = 180 ,$flag = true) {
    global $post;

    $post_title = $post->post_title; // get_the_title();

    // 特色图片
    if ( has_post_thumbnail() ) {
        $thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $post_thumb = '<img src="' . get_bloginfo("template_url") . '/thumb.php?src='.$thumb_src[0].'&amp;h='.$height.'&amp;w='.$width.'&amp;zc=1" alt="'. $post_title . '" title="' . $post_title . '"/>';
        if($flag) {
            $post_thumb = '<a href="'.get_permalink().'">' . $post_thumb . '</a>';
        }

        return $post_thumb;
    }

    // 从文章中提取图片
    $content = $post->post_content;
    preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
    $n = count($strResult[1]);
    if ($n > 0) {
        $post_thumb = '<img src="'.get_bloginfo("template_url").'/thumb.php?w='.$width.'&amp;h='.$height.'&amp;src='.$strResult[1][0].'" title="' . $post_title . '" alt="' . $post_title . '"/>';
        if($flag) {
            $post_thumb = '<a href="'.get_permalink().'">' . $post_thumb . '</a>';
        }

        return $post_thumb;
    }

    // 使用随机图片
    $post_thumb = '<img src="'. kratos_option('default_image') .'" title="' . $post_title . '" alt="' . $post_title . '"/>';
    if($flag) {
        $post_thumb = '<a href="'.get_permalink().'">' . $post_thumb . '</a>';
    }

    return $post_thumb;
}

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
?>
