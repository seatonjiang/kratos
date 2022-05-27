<?php

/**
 * 文章内容
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.05.27
 */

get_header();
$col_array = array(
    'one_side' => 'col-lg-12',
    'two_side' => 'col-lg-8'
);
?>
<div class="k-main <?php echo kratos_option('top_img_switch', true) ? 'banner' : 'color' ?>" style="background:<?php echo kratos_option('g_background', '#f5f5f5'); ?>">
    <div class="container">
        <div class="row">
            <div class="<?php echo $col_array[kratos_option('g_article_widgets', 'two_side')] ?> details">
                <?php if (have_posts()) : the_post();
                    update_post_caches($posts); ?>
                    <div class="article">
                        <?php if (!is_attachment()) { ?>
                            <div class="breadcrumb-box">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a class="text-dark" href="<?php echo home_url(); ?>"> <?php _e('首页', 'kratos'); ?></a>
                                    </li>
                                    <?php
                                    $cat_id = get_the_category()[0]->term_id;
                                    $if_parent = TRUE;
                                    $breadcrumb = "";
                                    while ($if_parent == TRUE) {
                                        $cat_object = get_category($cat_id);
                                        $cat = $cat_object->term_id;
                                        $categoryURL = get_category_link($cat);
                                        $name = $cat_object->name;
                                        $cat_id = $cat_object->parent;
                                        $add_link = '<li class="breadcrumb-item"> <a class="text-dark" href="' . $categoryURL . '">' . $name . '</a></li>';
                                        $breadcrumb = substr_replace($breadcrumb, $add_link, 0, 0);
                                        if ($cat_id == 0) {
                                            $if_parent = FALSE;
                                        }
                                    }
                                    echo $breadcrumb;
                                    ?>
                                    <li class="breadcrumb-item active" aria-current="page"> <?php _e('正文', 'kratos'); ?></li>
                                </ol>
                            </div>
                        <?php } ?>
                        <div class="header">
                            <h1 class="title"><?php the_title(); ?></h1>
                            <div class="meta">
                                <span><?php echo get_the_date(); ?></span>
                                <?php if (kratos_option('g_post_views', true)) { ?>
                                    <span><?php echo get_post_views();
                                            _e('点热度', 'kratos'); ?></span>
                                <?php }
                                if (kratos_option('g_post_loves', true)) { ?>
                                    <span><?php if (get_post_meta($post->ID, 'love', true)) {
                                                echo get_post_meta($post->ID, 'love', true);
                                            } else {
                                                echo '0';
                                            }
                                            _e('人点赞', 'kratos'); ?></span>
                                <?php }
                                if (kratos_option('g_post_comments', true)) { ?>
                                    <span><?php comments_number('0', '1', '%');
                                            _e('条评论', 'kratos'); ?></span>
                                <?php } ?>
                                <?php if (current_user_can('edit_posts')) {
                                    echo '<span>';
                                    edit_post_link(__('编辑文章', 'kratos'));
                                    echo '</span>';
                                }; ?>
                            </div>
                        </div>
                        <div class="content" id="lightgallery">
                            <?php
                            if (!empty(kratos_option('single_ad_top_group'))) {
                                foreach (kratos_option('single_ad_top_group') as $group_item) {
                                    if ($group_item['ad_switcher']) {
                                        echo '<div style="margin-bottom:5px"><a href="' . $group_item['ad_url'] . '" target="_blank" rel="noreferrer"><img src="' . $group_item['ad_img'] . '"></a></div>';
                                    }
                                }
                            }
                            the_content();
                            wp_link_pages(
                                array(
                                    'before' => '<div class="paginations text-center">',
                                    'after' => '',
                                    'next_or_number' => 'next',
                                    'previouspagelink' => __('<span>上一页</span>', 'kratos'),
                                    'nextpagelink' => ''
                                )
                            );
                            wp_link_pages(
                                array(
                                    'before' => '',
                                    'after' => '',
                                    'next_or_number' => 'number',
                                    'link_before' => '<span>',
                                    'link_after' => '</span>'
                                )
                            );
                            wp_link_pages(
                                array(
                                    'before' => '',
                                    'after' => '</div>',
                                    'next_or_number' => 'next',
                                    'previouspagelink' => '',
                                    'nextpagelink' => __('<span>下一页</span>', 'kratos')
                                )
                            );
                            if (!empty(kratos_option('single_ad_bottom_group'))) {
                                foreach (kratos_option('single_ad_bottom_group') as $group_item) {
                                    if ($group_item['ad_switcher']) {
                                        echo '<div style="margin-bottom:5px"><a href="' . $group_item['ad_url'] . '" target="_blank" rel="noreferrer"><img src="' . $group_item['ad_img'] . '"></a></div>';
                                    }
                                }
                            }
                            ?>
                        </div>
                        <?php if (kratos_option('g_cc_fieldset')['g_cc_switch'] ?? false) {
                            $cc_array = array(
                                'one' => __('知识共享署名 4.0 国际许可协议', 'kratos'),
                                'two' => __('知识共享署名-非商业性使用 4.0 国际许可协议', 'kratos'),
                                'three' => __('知识共享署名-禁止演绎 4.0 国际许可协议', 'kratos'),
                                'four' => __('知识共享署名-非商业性使用-禁止演绎 4.0 国际许可协议', 'kratos'),
                                'five' => __('知识共享署名-相同方式共享 4.0 国际许可协议', 'kratos'),
                                'six' => __('知识共享署名-非商业性使用-相同方式共享 4.0 国际许可协议', 'kratos'),
                            );
                            echo '<div class="copyright"><span class="text-center">';
                            printf(__('本作品采用 %s 进行许可', 'kratos'), $cc_array[kratos_option('g_cc_fieldset')['g_cc']]);
                            echo '</span></div>';
                        } ?>
                        <div class="footer clearfix">
                            <div class="tags float-left">
                                <span><?php _e('标签：', 'kratos'); ?></span>
                                <?php if (get_the_tags()) {
                                    the_tags('', ' ', '');
                                } else {
                                    echo '<a>' . __('暂无', 'kratos') . '</a>';
                                } ?>
                            </div>
                            <div class="tool float-right d-none d-lg-block">
                                <span><?php _e('最后更新：', 'kratos'); ?><?php the_modified_date(); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php require get_template_directory() . '/pages/page-toolbar.php'; ?>
                <nav class="navigation post-navigation clearfix" role="navigation">
                    <?php
                    $prev_post = get_previous_post(TRUE);
                    if (!empty($prev_post)) {
                        echo '<div class="nav-previous clearfix"><a title="' . $prev_post->post_title . '" href="' . get_permalink($prev_post->ID) . '">' . __('< 上一篇', 'kratos') . '</a></div>';
                    }
                    $next_post = get_next_post(TRUE);
                    if (!empty($next_post)) {
                        echo '<div class="nav-next"><a title="' . $next_post->post_title . '" href="' . get_permalink($next_post->ID) . '">' . __('下一篇 >', 'kratos') . '</a></div>';
                    } ?>
                </nav>
                <?php comments_template(); ?>
            </div>
            <?php if (kratos_option('g_article_widgets', 'two_side') == 'two_side') { ?>
                <div class="col-lg-4 sidebar sticky-sidebar d-none d-lg-block">
                    <?php dynamic_sidebar('single_sidebar'); ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>