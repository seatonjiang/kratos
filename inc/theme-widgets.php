<?php

/**
 * 侧栏小工具
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.04.29
 */

// 添加小工具
function widgets_init()
{
    register_sidebar(array(
        'name' => __('主页侧边栏', 'kratos'),
        'id' => 'home_sidebar',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="title">',
        'after_title' => '</div>',
    ));
    register_sidebar(array(
        'name' => __('文章侧边栏', 'kratos'),
        'id' => 'single_sidebar',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="title">',
        'after_title' => '</div>',
    ));
    register_sidebar(array(
        'name' => __('页面侧边栏', 'kratos'),
        'id' => 'page_sidebar',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="title">',
        'after_title' => '</div>',
    ));
    // 去掉默认小工具
    $wp_widget = array(
        'WP_Widget_Block',
        'WP_Widget_Pages',
        'WP_Widget_Meta',
        'WP_Widget_Media_Image',
        'WP_Widget_Calendar',
        'WP_Widget_Recent_Posts',
        'WP_Widget_Recent_Comments',
        'WP_Widget_RSS',
        'WP_Widget_Search',
        'WP_Widget_Tag_Cloud',
        'WP_Nav_Menu_Widget',
    );

    foreach ($wp_widget as $wp_widget) {
        unregister_widget($wp_widget);
    }
}
add_action('widgets_init', 'widgets_init');

// 分类目录计数
function cat_count_span($links)
{
    $links = str_replace('</a> (', '<span> / ', $links);
    $links = str_replace(')', __('篇', 'kratos') . '</span></a>', $links);
    return $links;
}
add_filter('wp_list_categories', 'cat_count_span');

// 文章归档计数
function archive_count_span($links)
{
    $links = str_replace('</a>&nbsp;(', '<span> / ', $links);
    $links = str_replace(')', __('篇', 'kratos') . '</span></a>', $links);
    return $links;
}
add_filter('get_archives_link', 'archive_count_span');

// 小工具文章聚合 - 热点文章
function most_comm_posts($days = 30, $nums = 6)
{
    global $wpdb;

    $today = wp_date("Y-m-d H:i:s");
    $daysago = wp_date("Y-m-d H:i:s", strtotime($today) - ($days * 24 * 60 * 60));
    $result = $wpdb->get_results($wpdb->prepare("SELECT comment_count, ID, post_title, post_date FROM $wpdb->posts WHERE post_date BETWEEN %s AND %s and post_type = 'post' AND post_status = 'publish' ORDER BY comment_count DESC LIMIT 0, %d", $daysago, $today, $nums));
    $output = '';

    if (!empty($result)) {
        foreach ($result as $topten) {
            $postid = $topten->ID;
            $title = esc_attr(strip_tags($topten->post_title));
            $commentcount = $topten->comment_count;
            if ($commentcount >= 0) {
                $output .= '<a class="bookmark-item" title="' . $title . '" href="' . get_permalink($postid) . '" rel="bookmark"><i class="kicon i-book"></i>';
                $output .= $title;
                $output .= '</a>';
            }
        }
    }
    echo $output;
}

function timeago($time)
{
    $time = strtotime($time);
    $dtime = time() - $time;
    if ($dtime < 1) return __('刚刚', 'kratos');
    $intervals = [
        12 * 30 * 24 * 60 * 60 => __(' 年前', 'kratos'),
        30 * 24 * 60 * 60 => __(' 个月前', 'kratos'),
        7  * 24 * 60 * 60 => __(' 周前', 'kratos'),
        24 * 60 * 60 => __(' 天前', 'kratos'),
        60 * 60 => __(' 小时前', 'kratos'),
        60 => __(' 分钟前', 'kratos'),
        1 => __(' 秒前', 'kratos')
    ];
    foreach ($intervals as $sec => $str) {
        $v = $dtime / $sec;
        if ($v >= 1) return round($v) . $str;
    }
}

function string_cut($string, $sublen, $start = 0, $code = 'UTF-8')
{
    if ($code == 'UTF-8') {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);
        if (count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)) . "...";
        return join('', array_slice($t_string[0], $start, $sublen));
    } else {
        $start = $start * 2;
        $sublen = $sublen * 2;
        $strlen = strlen($string);
        $tmpstr = '';
        for ($i = 0; $i < $strlen; $i++) {
            if ($i >= $start && $i < ($start + $sublen)) {
                if (ord(substr($string, $i, 1)) > 129) $tmpstr .= substr($string, $i, 2);
                else $tmpstr .= substr($string, $i, 1);
            }
            if (ord(substr($string, $i, 1)) > 129) $i++;
        }
        return $tmpstr;
    }
}

function latest_comments($list_number = 5, $cut_length = 50)
{
    global $wpdb, $output;
    $comments = $wpdb->get_results($wpdb->prepare("SELECT comment_ID, comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_content FROM {$wpdb->comments} LEFT OUTER JOIN {$wpdb->posts} ON {$wpdb->comments}.comment_post_ID = {$wpdb->posts}.ID WHERE comment_approved = '1' AND (comment_type = '' OR comment_type = 'comment') AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT %d", $list_number));
    foreach ($comments as $comment) {
        $nickname = esc_attr($comment->comment_author) ?: __('匿名', 'kratos');
        $output .= '<a href="' . get_the_permalink($comment->comment_post_ID) . '#commentform">
            <div class="meta clearfix">
                <div class="avatar float-left">' . get_avatar($comment, 60) . '</div>
                <div class="profile d-block">
                    <span class="date">' . $nickname . ' ' . __('发布于 ', 'kratos') . timeago($comment->comment_date_gmt) . '（' . wp_date(__('m月d日', 'kratos'), strtotime($comment->comment_date_gmt)) . '）</span>
                    <span class="message d-block">' . convert_smilies(esc_attr(string_cut(strip_tags($comment->comment_content), $cut_length))) . '</span>
                </div>
            </div>
        </a>';
    }
    return $output;
}

class widget_search extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array(
            'classname'                   => 'widget_search',
            'description'                 => __('A search form for your site.'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('search', _x('Search', 'Search widget'), $widget_ops);
    }

    public function widget($args, $instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        echo '<div class="widget w-search">';
        if ($title) {
            echo '<div class="title">' . $title . '</div>';
        }
        echo '<div class="item"> <form role="search" method="get" id="searchform" class="searchform" action="' . home_url('/') . '"> <div class="input-group mt-2 mb-2"> <input type="text" name="s" id="search-widgets" class="form-control" placeholder="' . __('搜点什么呢?', 'kratos') . '"> <div class="input-group-append"> <button class="btn btn-primary btn-search" type="submit" id="searchsubmit">' . __('搜索', 'kratos') . '</button> </div> </div> </form>';
        echo '</div></div>';
    }

    public function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title    = $instance['title'];
?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance          = $old_instance;
        $new_instance      = wp_parse_args((array) $new_instance, array('title' => ''));
        $instance['title'] = sanitize_text_field($new_instance['title']);
        return $instance;
    }
}

class widget_ad extends WP_Widget
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'scripts'));

        $widget_ops = array(
            'name' => __('图片广告', 'kratos'),
            'description' => __('显示自定义图片广告的工具', 'kratos'),
        );

        parent::__construct(false, false, $widget_ops);
    }

    public function scripts()
    {
        wp_enqueue_script('media-upload');
        wp_enqueue_media();
        wp_enqueue_script('widget_scripts', ASSET_PATH . '/assets/js/widget.min.js', array('jquery'));
        wp_enqueue_style('widget_css', ASSET_PATH . '/assets/css/widget.min.css', array());
    }

    public function widget($args, $instance)
    {
        $subtitle = !empty($instance['subtitle']) ? $instance['subtitle'] : __('广告', 'kratos');
        $image = !empty($instance['image']) ? $instance['image'] : '';
        $url = !empty($instance['url']) ? $instance['url'] : '';

        echo '<div class="widget w-ad">';
        echo '<a href="' . $url . '" target="_blank" rel="noreferrer"><img src="' . $image . '"><div class="prompt">' . $subtitle . '</div></a>';
        echo '</div>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['subtitle'] = (!empty($new_instance['subtitle'])) ? $new_instance['subtitle'] : '';
        $instance['image'] = (!empty($new_instance['image'])) ? $new_instance['image'] : '';
        $instance['url'] = (!empty($new_instance['url'])) ? $new_instance['url'] : '';

        return $instance;
    }

    public function form($instance)
    {
        $subtitle = !empty($instance['subtitle']) ? $instance['subtitle'] : __('广告', 'kratos');
        $image = !empty($instance['image']) ? $instance['image'] : '';
        $url = !empty($instance['url']) ? $instance['url'] : '';
    ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('副标题：', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('链接地址：', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_attr($url); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('广告图片:', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" type="text" value="<?php echo esc_url($image); ?>" />
                <button type="button" class="button-update-media upload_ad"><?php _e('选择图片', 'kratos'); ?></button>
            </p>
        </div>
    <?php
    }
}

class widget_about extends WP_Widget
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'scripts'));

        $widget_ops = array(
            'name' => __('个人简介', 'kratos'),
            'description' => __('站长个人简介的展示工具', 'kratos'),
        );

        parent::__construct(false, false, $widget_ops);
    }

    public function scripts()
    {
        wp_enqueue_script('media-upload');
        wp_enqueue_media();
        wp_enqueue_script('widget_scripts', ASSET_PATH . '/assets/js/widget.min.js', array('jquery'));
        wp_enqueue_style('widget_css', ASSET_PATH . '/assets/css/widget.min.css', array());
    }

    public function widget($args, $instance)
    {
        $introduce = !empty(get_the_author_meta('description', '1')) ? get_the_author_meta('description', '1') : __('这个人很懒，什么都没留下', 'kratos');
        $username = get_the_author_meta('display_name', '1');
        $avatar = get_avatar_url('1', ['size' => '300']);
        $background = !empty($instance['background']) ? $instance['background'] : ASSET_PATH . '/assets/img/about-background.png';

        echo '<div class="widget w-about">';
        echo '<div class="background" style="background:url(' . $background . ') no-repeat center center;-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div><div class="wrapper text-center">';
        if (kratos_option('g_login', true)) {
            if (current_user_can('manage_options')) {
                echo '<a href="' . admin_url() . '">';
            } else {
                echo '<a href="' . wp_login_url() . '">';
            }
        }
        echo '<img src="' . $avatar . '">';
        if (kratos_option('g_login', true)) {
            echo '</a>';
        }
        $introduce = str_replace("\n", '<br>', $introduce);
        echo '</div><div class="textwidget text-center"><p class="username">' . $username . '</p><p class="about">' . $introduce . '</p></div>';
        echo '</div>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['background'] = (!empty($new_instance['background'])) ? $new_instance['background'] : '';

        return $instance;
    }

    public function form($instance)
    {
        $background = !empty($instance['background']) ? $instance['background'] : ASSET_PATH . '/assets/img/about-background.png';
    ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id('background'); ?>"><?php _e('背景图片:', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('background'); ?>" name="<?php echo $this->get_field_name('background'); ?>" type="text" value="<?php echo esc_attr($background); ?>">
                <button type="button" class="button-update-media upload_background"><?php _e('选择图片', 'kratos'); ?></button>
            </p>
        </div>
    <?php
    }
}

class widget_tags extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array(
            'name' => __('标签聚合', 'kratos'),
            'description' => __('文章标签的展示工具', 'kratos'),
        );

        parent::__construct(false, false, $widget_ops);
    }

    public function widget($args, $instance)
    {
        $number = !empty($instance['number']) ? $instance['number'] : '8';
        $order = !empty($instance['order']) ? $instance['order'] : 'RAND';
        $tags = wp_tag_cloud(
            array(
                'unit' => 'px',
                'smallest' => 14,
                'largest' => 14,
                'number' => $number,
                'format' => 'flat',
                'orderby' => 'count',
                'order' => $order,
                'echo' => false,
            )
        );
        echo '<div class="widget w-tags">';
        echo '<div class="title">' . __('标签聚合', 'kratos') . '</div>';
        echo '<div class="item">' . $tags . '</div>';
        echo '</div>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['number'] = (!empty($new_instance['number'])) ? $new_instance['number'] : '';
        $instance['order'] = (!empty($new_instance['order'])) ? $new_instance['order'] : '';

        return $instance;
    }

    public function form($instance)
    {
        global $wpdb;
        $number = !empty($instance['number']) ? $instance['number'] : '8';
        $order = !empty($instance['order']) ? $instance['order'] : 'RAND';
    ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量：', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('显示排序：', 'kratos'); ?></label>
                <select name="<?php echo $this->get_field_name("order"); ?>" id='<?php echo $this->get_field_id("order"); ?>'>
                    <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>><?php _e('降序', 'kratos'); ?></option>
                    <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>><?php _e('升序', 'kratos'); ?></option>
                    <option value="RAND" <?php echo ($order == 'RAND') ? 'selected' : ''; ?>><?php _e('随机', 'kratos'); ?></option>
                </select>
            </p>
        </div>
    <?php
    }
}

class widget_posts extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array(
            'name' => __('文章聚合', 'kratos'),
            'description' => __('展示最热、随机、最新文章的工具', 'kratos'),
        );

        parent::__construct(false, false, $widget_ops);
    }

    public function widget($args, $instance)
    {
        $number = !empty($instance['number']) ? $instance['number'] : '6';
        $days = !empty($instance['days']) ? $instance['days'] : '30';
        $order = !empty($instance['order']) ? $instance['order'] : 'hot';

        echo '<div class="widget w-recommended">';
    ?>
        <div class="nav nav-tabs d-none d-xl-flex" id="nav-tab" role="tablist">
            <a class="nav-item nav-link <?php echo $active = ($order == 'new') ? 'active' : null; ?>" id="nav-new-tab" data-toggle="tab" href="#nav-new" role="tab" aria-controls="nav-new" aria-selected="<?php echo $selected = ($order == 'new') ? 'true' : 'false'; ?>"><i class="kicon i-tabnew"></i><?php _e('最新', 'kratos'); ?></a>
            <a class="nav-item nav-link <?php echo $active = ($order == 'hot') ? 'active' : null; ?>" id="nav-hot-tab" data-toggle="tab" href="#nav-hot" role="tab" aria-controls="nav-hot" aria-selected="<?php echo $selected = ($order == 'hot') ? 'true' : 'false'; ?>"><i class="kicon i-tabhot"></i><?php _e('热点', 'kratos'); ?></a>
            <a class="nav-item nav-link <?php echo $active = ($order == 'random') ? 'active' : null; ?>" id="nav-random-tab" data-toggle="tab" href="#nav-random" role="tab" aria-controls="nav-random" aria-selected="<?php echo $selected = ($order == 'random') ? 'true' : 'false'; ?>"><i class="kicon i-tabrandom"></i><?php _e('随机', 'kratos'); ?></a>
        </div>
        <div class="nav nav-tabs d-xl-none" id="nav-tab" role="tablist">
            <a class="nav-item nav-link <?php echo $active = ($order == 'new') ? 'active' : null; ?>" id="nav-new-tab" data-toggle="tab" href="#nav-new" role="tab" aria-controls="nav-new" aria-selected="<?php echo $selected = ($order == 'new') ? 'true' : 'false'; ?>"><?php _e('最新', 'kratos'); ?></a>
            <a class="nav-item nav-link <?php echo $active = ($order == 'hot') ? 'active' : null; ?>" id="nav-hot-tab" data-toggle="tab" href="#nav-hot" role="tab" aria-controls="nav-hot" aria-selected="<?php echo $selected = ($order == 'hot') ? 'true' : 'false'; ?>"><?php _e('热点', 'kratos'); ?></a>
            <a class="nav-item nav-link <?php echo $active = ($order == 'random') ? 'active' : null; ?>" id="nav-random-tab" data-toggle="tab" href="#nav-random" role="tab" aria-controls="nav-random" aria-selected="<?php echo $selected = ($order == 'random') ? 'true' : 'false'; ?>"><?php _e('随机', 'kratos'); ?></a>
        </div>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade <?php echo $active = ($order == 'new') ? 'show active' : null; ?>" id="nav-new" role="tabpanel" aria-labelledby="nav-new-tab">
                <?php $myposts = get_posts('numberposts=' . $number . ' & offset=0');
                foreach ($myposts as $post) : ?>
                    <a class="bookmark-item" rel="bookmark" title="<?php echo esc_attr(strip_tags($post->post_title)); ?>" href="<?php echo get_permalink($post->ID); ?>"><i class="kicon i-book"></i><?php echo esc_attr(strip_tags($post->post_title)); ?></a>
                <?php endforeach; ?>
            </div>
            <div class="tab-pane fade <?php echo $active = ($order == 'hot') ? 'show active' : null; ?>" id="nav-hot" role="tabpanel" aria-labelledby="nav-hot-tab">
                <?php if (function_exists('most_comm_posts')) {
                    most_comm_posts($days, $number);
                } ?>
            </div>
            <div class="tab-pane fade <?php echo $active = ($order == 'random') ? 'show active' : null; ?>" id="nav-random" role="tabpanel" aria-labelledby="nav-random-tab">
                <?php $myposts = get_posts('numberposts=' . $number . ' & offset=0 & orderby=rand');
                foreach ($myposts as $post) : ?>
                    <a class="bookmark-item" rel="bookmark" title="<?php echo esc_attr(strip_tags($post->post_title)); ?>" href="<?php echo get_permalink($post->ID); ?>"><i class="kicon i-book"></i><?php echo esc_attr(strip_tags($post->post_title)); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php echo '</div>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['number'] = (!empty($new_instance['number'])) ? $new_instance['number'] : '';
        $instance['days'] = (!empty($new_instance['days'])) ? $new_instance['days'] : '';
        $instance['order'] = (!empty($new_instance['order'])) ? $new_instance['order'] : '';

        return $instance;
    }
    public function form($instance)
    {
        global $wpdb;
        $number = !empty($instance['number']) ? $instance['number'] : '6';
        $days = !empty($instance['days']) ? $instance['days'] : '30';
        $order = !empty($instance['order']) ? $instance['order'] : 'hot';
    ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('展示数量：', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('days'); ?>"><?php _e('统计天数：', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" type="text" value="<?php echo esc_attr($days); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('默认显示：', 'kratos'); ?></label>
                <select name="<?php echo $this->get_field_name("order"); ?>" id='<?php echo $this->get_field_id("order"); ?>'>
                    <option value="new" <?php echo ($order == 'new') ? 'selected' : ''; ?>><?php _e('最新', 'kratos'); ?></option>
                    <option value="hot" <?php echo ($order == 'hot') ? 'selected' : ''; ?>><?php _e('热点', 'kratos'); ?></option>
                    <option value="random" <?php echo ($order == 'random') ? 'selected' : ''; ?>><?php _e('随机', 'kratos'); ?></option>
                </select>
            </p>
        </div>
    <?php
    }
}

class widget_comments extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array(
            'name' => __('最近评论', 'kratos'),
            'description' => __('展示站点最近的评论', 'kratos'),
        );

        parent::__construct(false, false, $widget_ops);
    }

    public function widget($args, $instance)
    {
        $number = !empty($instance['number']) ? $instance['number'] : '5';
        $title = !empty($instance['title']) ? $instance['title'] : __('最近评论', 'kratos');

        echo '<div class="widget w-comments"><div class="title">' . $title . '</div><div class="comments">';
        echo latest_comments($number, 50);
        echo '</div></div>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['number'] = (!empty($new_instance['number'])) ? $new_instance['number'] : '';
        $instance['title'] = (!empty($new_instance['title'])) ? $new_instance['title'] : '';

        return $instance;
    }
    public function form($instance)
    {
        global $wpdb;
        $number = !empty($instance['number']) ? $instance['number'] : '5';
        $title = !empty($instance['title']) ? $instance['title'] : __('最近评论', 'kratos');
    ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('栏目标题：', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('展示数量：', 'kratos'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
            </p>
        </div>
<?php
    }
}

class widget_toc extends WP_Widget
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'scripts'));

        $widget_ops = array(
            'name' => __('文章目录', 'kratos'),
            'description' => __('仅在有目录规则的文章中显示目录的工具', 'kratos'),
        );

        parent::__construct(false, false, $widget_ops);
    }

    public function scripts()
    {
        wp_enqueue_script('media-upload');
        wp_enqueue_media();
        wp_enqueue_script('widget_scripts', ASSET_PATH . '/assets/js/widget.min.js', array('jquery'));
        wp_enqueue_style('widget_css', ASSET_PATH . '/assets/css/widget.min.css', array());
    }

    public function widget($args, $instance)
    {
        global $toc;

        $index = wp_cache_get(get_the_ID(), 'toc');

        if ($index === false && $toc) {
            $index = '<ul class="ul-toc">' . "\n";
            $prev_depth = '';
            $to_depth = 0;
            foreach ($toc as $toc_item) {
                $toc_depth = $toc_item['depth'];
                if ($prev_depth) {
                    if ($toc_depth == $prev_depth) {
                        $index .= '</li>' . "\n";
                    } elseif ($toc_depth > $prev_depth) {
                        $to_depth++;
                        $index .= '<ul class="ul-' . $toc_depth . '">' . "\n";
                    } else {
                        $to_depth2 = $to_depth > $prev_depth - $toc_depth ? $prev_depth - $toc_depth : $to_depth;
                        if ($to_depth2) {
                            for ($i = 0; $i < $to_depth2; $i++) {
                                $index .= '</li>' . "\n" . '</ul>' . "\n";
                                $to_depth--;
                            }
                        }
                        $index .= '</li>';
                    }
                }
                $index .= '<li class="li-' . $toc_depth . '"><a href="#toc-' . $toc_item['count'] . '">' . str_replace(array('[h2title]', '[/h2title]'), array('', ''), $toc_item['text']) . '</a>';
                $prev_depth = $toc_item['depth'];
            }
            for ($i = 0; $i <= $to_depth; $i++) {
                $index .= '</li>' . "\n" . '</ul>' . "\n";
            }
            wp_cache_set(get_the_ID(), $index, 'toc', 360000);
            $index = '<div class="widget w-toc">' . "\n" . '<div class="title">文章目录</div>' . "\n" . '<div class="item">' . $index . '</div>' . "\n" . '</div>';
        }

        echo $index;
    }
}

function register_widgets()
{
    register_widget('widget_ad');
    register_widget('widget_about');
    register_widget('widget_tags');
    register_widget('widget_search');
    register_widget('widget_posts');
    register_widget('widget_comments');
    register_widget('widget_toc');
}
add_action('widgets_init', 'register_widgets');
