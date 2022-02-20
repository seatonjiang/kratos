<?php

/**
 * 文章列表
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.02.20
 */
?>
<div class="article-panel">
    <span class="a-card d-none d-md-block d-lg-block">
        <?php $article_comment = kratos_option('g_article_fieldset')['g_article_comment'] ?? '20';
        $article_love = kratos_option('g_article_fieldset')['g_article_love'] ?? '200';
        if (is_sticky()) { ?>
            <i class="kicon i-card-top"></i>
        <?php } elseif (findSinglecomments($post->ID) >= $article_comment || get_post_meta($post->ID, 'love', true) >= $article_love) { ?>
            <i class="kicon i-card-hot"></i>
        <?php } ?>
    </span>
    <?php if (kratos_option('g_thumbnail', true)) { ?>
        <div class="a-thumb">
            <a href="<?php the_permalink(); ?>">
                <?php post_thumbnail(); ?>
            </a>
        </div>
    <?php } ?>
    <div class="a-post <?php echo kratos_option('g_thumbnail', true) ?: 'a-none'; ?>">
        <div class="header">
            <?php
            $category = get_the_category();
            if ($category) {
                echo '<a class="label" href="' . get_category_link($category[0]->term_id) . '">' . $category[0]->cat_name . '<i class="label-arrow"></i></a>';
            } else {
                echo '<span class="label">' . __('页面', 'kratos') . '<i class="label-arrow"></i></span>';
            }
            ?>
            <h3 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        </div>
        <div class="content">
            <p><?php echo wp_trim_words(get_the_excerpt(), 260); ?></p>
        </div>
    </div>
    <div class="a-meta">
        <span class="float-left d-none d-md-block">
            <span class="mr-2"><i class="kicon i-calendar"></i><?php echo get_the_date(); ?></span>
            <?php if (kratos_option('g_post_comments', true)) { ?>
                <span class="mr-2"><i class="kicon i-comments"></i><?php comments_number('0', '1', '%'); _e('条评论', 'kratos'); ?></span>
            <?php } ?>
        </span>
        <span class="float-left d-block">
            <?php if (kratos_option('g_post_views', true)) { ?>
                <span class="mr-2"><i class="kicon i-hot"></i><?php echo get_post_views(); _e('点热度', 'kratos'); ?></span>
            <?php } if (kratos_option('g_post_loves', true)) { ?>
                <span class="mr-2"><i class="kicon i-good"></i><?php if (get_post_meta($post->ID, 'love', true)) { echo get_post_meta($post->ID, 'love', true); } else { echo '0'; } _e('人点赞', 'kratos'); ?></span>
            <?php } if (kratos_option('g_post_author', true)) { ?>
                <span class="mr-2"><i class="kicon i-author"></i><?php echo get_the_author_meta('display_name'); ?></span>
            <?php } ?>
        </span>
        <span class="float-right">
            <a href="<?php the_permalink(); ?>"><?php _e('阅读全文', 'kratos'); ?><i class="kicon i-rightbutton"></i></a>
        </span>
    </div>
</div>
