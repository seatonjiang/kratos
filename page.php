<?php
/**
 * 页面模板
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.02.23
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
                        </div><!-- .header -->
                        <div class="content">
                            <?php the_content(); ?>
                        </div><!-- .content -->
                    </div><!-- .article -->
                <?php endif; ?>
                <?php comments_template(); ?>
            </div><!-- .details -->
            <div class="col-lg-4 sidebar d-none d-lg-block">
                <?php dynamic_sidebar('sidebar_tool'); ?>
            </div><!-- .sidebar -->
        </div>
    </div>
</div><!-- .k-main -->
<?php get_footer(); ?>