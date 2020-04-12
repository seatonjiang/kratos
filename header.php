<?php
/**
 * 主题页眉
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.04.12
 */
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php wp_title( '-', true, 'right' ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="format-detection" content="telphone=no, email=no">
    <meta name="keywords" content="<?php keywords(); ?>">
    <meta name="description" itemprop="description" content="<?php description(); ?>">
    <meta name="theme-color" content="<?php echo kratos_option('g_chrome', '#282a2c'); ?>">
    <meta itemprop="image" content="<?php echo share_thumbnail_url(); ?>"/>
    <link rel="shortcut icon" href="<?php echo kratos_option('g_icon'); ?>">
    <?php wp_head(); mourning(); ?>
</head>
<?php flush(); ?>
<body>
<div class="k-header">
    <nav class="k-nav navbar navbar-expand-lg navbar-light fixed-top" <?php if(kratos_option('top_select', 'banner') !== 'banner'){ echo 'style="background:' . kratos_option('top_color', '#24292e') .'"';} ?>>
        <div class="container">
            <a class="navbar-brand" href="<?php echo get_option('home'); ?>">
                <?php
                    if (kratos_option('g_logo')){
                        echo '<img src="' . kratos_option('g_logo') . '">';
                    }else{
                        bloginfo('name');
                    }
                ?>
            </a>
            <?php if ( has_nav_menu('header_menu') ) { ?>
            <button class="navbar-toggler navbar-toggler-right" id="navbutton" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="line first-line"></span>
                <span class="line second-line"></span>
                <span class="line third-line"></span>
            </button>
            <?php }
                wp_nav_menu( array(
                    'theme_location'  => 'header_menu',
                    'depth'           => 2,
                    'container'       => 'div',
                    'container_class' => 'collapse navbar-collapse',
                    'container_id'    => 'navbarResponsive',
                    'menu_class'      => 'navbar-nav ml-auto',
                    'walker'          => new WP_Bootstrap_Navwalker(),
                ) );
            ?>
        </div>
    </nav>
    <?php if(kratos_option('top_select', 'banner') == 'banner'){ ?>
    <div class="banner">
        <div class="overlay"></div>
        <div class="content text-center" style="background-image: url(<?php 
            if(!kratos_option('top_img')){
                $img = ASSET_PATH . '/assets/img/background.png';
            } else {
                $img = kratos_option('top_img', ASSET_PATH . '/assets/img/background.png');
            }
            echo $img; ?>);">
            <div class="introduce animated fadeInUp">
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