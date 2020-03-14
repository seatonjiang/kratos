<?php
/**
 * 页面模板
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.03.14
 */

get_header(); ?>
<div class="k-main <?php echo kratos_option('top_select', 'banner'); ?>" style="background:<?php echo kratos_option('g_background', '#f5f5f5'); ?>">
    <div class="container">
        <div class="row">
        <div class="col-lg-8 details">
                <?php if (have_posts()) : the_post(); update_post_caches($posts); ?>
                    <div class="article py-4">
                        <div class="header text-center">
                            <h1 class="title m-0"><?php the_title(); ?></h1>
                        </div>
                        <div class="content">
                            <?php
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
                                    'link_before' =>'<span>',
                                    'link_after'=>'</span>'
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
                            ); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php comments_template(); ?>
            </div>
            <div class="col-lg-4 sidebar d-none d-lg-block">
                <?php dynamic_sidebar('sidebar_tool'); ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>