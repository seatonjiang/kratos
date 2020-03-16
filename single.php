<?php
/**
 * 文章内容
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.03.16
 */

get_header(); ?>
<div class="k-main <?php echo kratos_option('top_select', 'banner'); ?>" style="background:<?php echo kratos_option('g_background', '#f5f5f5'); ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 details">
                <?php if (have_posts()) : the_post(); update_post_caches($posts); ?>
                    <div class="article">
                        <div class="breadcrumb-box">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-dark" href="<?php echo home_url(); ?>"> <?php _e('首页' , 'kratos'); ?></a>
                                </li>
                                <li class="breadcrumb-item">
                                <?php
                                    $category = get_the_category();
                                    echo '<a class="text-dark" href="'.get_category_link($category[0]->cat_ID).'">' . $category[0]->cat_name . '</a>';
                                ?>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"> <?php _e('正文' , 'kratos'); ?></li>
                            </ol>
                        </div>
                        <div class="header">
                            <h1 class="title"><?php the_title(); ?></h1>
                            <div class="meta">
                            <span><?php echo get_the_date('Y年m月d日'); ?></span>
                            <span ><?php echo get_post_views(); _e('点热度' , 'kratos'); ?></span>
                            <span><?php if (get_post_meta($post->ID, 'love', true)) { echo get_post_meta($post->ID, 'love', true); } else {echo '0'; } _e('人点赞', 'kratos'); ?></span>
                            <span><?php comments_number('0', '1', '%'); _e('条评论', 'kratos'); ?></span>
                            <?php if (current_user_can('edit_posts')){ echo '<span>'; edit_post_link(__('编辑文章', 'kratos')); echo '</span>'; }; ?>
                            </div>
                        </div>
                        <div class="content">
                            <?php
                            if(kratos_option('s_singletop',false)){
                                if(kratos_option('s_singletop_links')){
                                    echo '<a href="'. kratos_option('s_singletop_links') .'" target="_blank" rel="noreferrer">';
                                }
                                echo '<img src="'.kratos_option('s_singletop_url').'">';
                                if(kratos_option('s_singletop_links')){
                                    echo '</a>';
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
                            );
                            if(kratos_option('s_singledown',false)){
                                if(kratos_option('s_singledown_links')){
                                    echo '<a href="'. kratos_option('s_singledown_links') .'" target="_blank" rel="noreferrer">';
                                }
                                echo '<img src="'.kratos_option('s_singledown_url').'">';
                                if(kratos_option('s_singledown_links')){
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>
                        <?php if(kratos_option('g_cc_switch', false)){ 
                            $cc_array = array(
                                'one' => __('知识共享署名 4.0 国际许可协议', 'kratos'),
                                'two' => __('知识共享署名-非商业性使用 4.0 国际许可协议', 'kratos'),
                                'three' => __('知识共享署名-禁止演绎 4.0 国际许可协议', 'kratos'),
                                'four' => __('知识共享署名-非商业性使用-禁止演绎 4.0 国际许可协议', 'kratos'),
                                'five' => __('知识共享署名-相同方式共享 4.0 国际许可协议', 'kratos'),
                                'six' => __('知识共享署名-非商业性使用-相同方式共享 4.0 国际许可协议', 'kratos'),
                            );
                            $select_cc = $cc_array[kratos_option('g_cc', 'one')];
                            echo '<div class="copyright"><span class="text-center">';
                            printf( __( '本作品采用 %s 进行许可','kratos' ) , $select_cc );
                            echo '</span></div>';
                        } ?>
                        <div class="footer clearfix">
                            <div class="tags float-left">
                                <span><?php _e('标签：' , 'kratos'); ?></span>
                                <?php if ( get_the_tags() ) { the_tags('', ' ', ''); } else{ echo '<a>' . __( '暂无' , 'kratos') . '</a>';  }?>
                            </div>
                            <div class="tool float-right d-none d-lg-block">
                                <div data-toggle="tooltip" data-html="true" data-original-title="<?php _e('最后更新：','kratos'); the_modified_date( 'Y-m-d H:i' ) ?>">
                                    <span><?php _e('最后更新：','kratos'); ?><?php the_modified_date('Y年m月d日'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php require get_template_directory() . '/pages/page-toolbar.php'; ?>
                <nav class="navigation post-navigation clearfix" role="navigation">
                    <?php
                    $prev_post = get_previous_post(TRUE);
                    if(!empty($prev_post)){
                        echo '<div class="nav-previous clearfix"><a title="'.$prev_post->post_title .'" href="'.get_permalink($prev_post->ID).'">'. __('< 上一篇','kratos') .'</a></div>';
                    }
                    $next_post = get_next_post(TRUE);
                    if(!empty($next_post)){
                        echo '<div class="nav-next"><a title="'. $next_post->post_title .'" href="'. get_permalink($next_post->ID) .'">'. __('下一篇 >','kratos') .'</a></div>';
                    }?>
                </nav>
                <?php comments_template(); ?>
            </div>
            <div class="col-lg-4 sidebar d-none d-lg-block">
                <?php dynamic_sidebar('sidebar_tool'); ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>