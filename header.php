<?php

/**
 * 主题页眉
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.12.09
 */
?>
<!DOCTYPE html>
<html lang="<?php bloginfo('language'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php wp_title('-', true, 'right'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="format-detection" content="telphone=no, date=no, address=no, email=no">
    <meta name="theme-color" content="<?php echo kratos_option('g_chrome', '#282a2c'); ?>">

    <meta name="keywords" itemprop="keywords" content="<?php echo keywords(); ?>">
    <meta name="description" itemprop="description" content="<?php echo description(); ?>">
    <?php $ogImageUrl = is_home() || !have_posts() ? kratos_option('seo_shareimg', ASSET_PATH . '/assets/img/default.jpg') : share_thumbnail_url(); ?>
    <meta itemprop="image" content="<?php echo $ogImageUrl; ?>">

    <?php $isPostsIndexAsHome = is_home() && !is_front_page() ?>
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
    <meta property="og:url" content="<?php echo $isPostsIndexAsHome ? get_site_url() : the_permalink(); ?>">
    <?php $title = $isPostsIndexAsHome ? get_bloginfo('name') : get_the_title(); ?>
    <meta property="og:title" content="<?php echo $title; ?>">
    <meta property="og:type" content="article">
    <?php
        if (!$isPostsIndexAsHome) {
            $tags = get_the_tags();
            if (is_array($tags)) {
                foreach ($tags as $tag) { ?>
                    <meta property="og:article:tag" content="<?php echo $tag->name; ?>">
                <?php }
            }
        }
    ?>
    <meta property="og:image" content="<?php echo $ogImageUrl; ?>">
    <meta property="og:image:type" content="image/webp">
    <meta property="og:locale" content="<?php bloginfo('language'); ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $title; ?>">
    <meta name="twitter:creator" content="<?php echo the_author(); ?>">

    <?php
        if (kratos_option('g_icon')) {
            echo '<link rel="shortcut icon" href="' . kratos_option("g_icon") . '">';
        }
        wp_head();
        wp_print_scripts('jquery');
        mourning();
        if (kratos_option('seo_statistical')) {
            echo kratos_option('seo_statistical');
        }
    ?>
</head>
<?php flush(); ?>
<body>
    <div class="k-header">
        <nav class="k-nav navbar navbar-expand-lg navbar-light fixed-top" <?php echo kratos_option('top_img_switch', true) ? '' : 'style="background:' . kratos_option('top_color', '#24292e') . '"'; ?>>
            <div class="container">
                <a class="navbar-brand" href="<?php echo get_option('home'); ?>">
                    <?php
                    if (kratos_option('g_logo')) {
                        echo '<img src="' . kratos_option('g_logo') . '"><h1 style="display:none">' . get_bloginfo('name') . '</h1>';
                    } else {
                        echo '<h1>' . get_bloginfo('name') . '</h1>';
                    }
                    ?>
                </a>
                <?php if (has_nav_menu('header_menu')) { ?>
                    <button class="navbar-toggler navbar-toggler-right" id="navbutton" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="line first-line"></span>
                        <span class="line second-line"></span>
                        <span class="line third-line"></span>
                    </button>
                <?php }
                if (has_nav_menu('header_menu')) {
                    wp_nav_menu(array(
                        'theme_location'  => 'header_menu',
                        'depth'           => 2,
                        'container'       => 'div',
                        'container_class' => 'collapse navbar-collapse',
                        'container_id'    => 'navbarResponsive',
                        'menu_class'      => 'navbar-nav ml-auto',
                        'walker'          => new WP_Bootstrap_Navwalker(),
                    ));
                }
                ?>
            </div>
        </nav>
        <?php if (kratos_option('top_img_switch', true)) { ?>
            <div class="banner">
                <div class="overlay"></div>
                <div class="content text-center" style="background-image: url(<?php echo kratos_option('top_img', ASSET_PATH . '/assets/img/background.jpg'); ?>);">
                    <div class="introduce animate__animated animate__fadeInUp">
                        <?php
                        if (is_category() || is_tag()) {
                            echo '<div class="title">' . single_cat_title('', false) . '</div>';
                            echo '<div class="mate">' . strip_tags(category_description()) . '</div>';
                        } else {
                            echo '<div class="title">' . kratos_option('top_title', 'Kratos') . '</div>';
                            echo '<div class="mate">' . kratos_option('top_describe', __('一款专注于用户阅读体验的响应式博客主题', 'kratos')) . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
