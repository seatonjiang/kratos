<?php
/**
 * 侧栏小工具
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.04.12
 */

// 添加小工具
function widgets_init()
{
    register_sidebar(array(
        'name' => __('侧边栏工具', 'kratos'),
        'id' => 'sidebar_tool',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="title">',
        'after_title' => '</div>',
    ));
    // 去掉默认小工具
    $wp_widget = array(
        'WP_Widget_Pages',
        'WP_Widget_Archives',
        'WP_Widget_Meta',
        'WP_Widget_Recent_Posts',
        'WP_Widget_Recent_Comments',
        'WP_Widget_RSS',
        'WP_Widget_Tag_Cloud',
        'WP_Nav_Menu_Widget',
    );

    foreach ($wp_widget as $wp_widget) {
        unregister_widget($wp_widget);
    }
}
add_action('widgets_init', 'widgets_init');

// 小工具文章聚合 - 热点文章
function most_comm_posts($days = 30, $nums = 6)
{
    global $wpdb;
    date_default_timezone_set("PRC");
    $today = date("Y-m-d H:i:s");
    $daysago = date("Y-m-d H:i:s", strtotime($today) - ($days * 24 * 60 * 60));
    $result = $wpdb->get_results("SELECT comment_count, ID, post_title, post_date FROM $wpdb->posts WHERE post_date BETWEEN '$daysago' AND '$today' and post_type='post' and post_status='publish' ORDER BY comment_count DESC LIMIT 0 , $nums");
    $output = '';
    if (!empty($result)) {
        foreach ($result as $topten) {
            $postid = $topten->ID;
            $title = $topten->post_title;
            $commentcount = $topten->comment_count;
            if ($commentcount >= 0) {
                $output .= '<a class="bookmark-item" title="' . $title . '" href="' . get_permalink($postid) . '" rel="bookmark"><i class="kicon i-book"></i>';
                $output .= strip_tags($title);
                $output .= '</a>';
            }
        }
    }
    echo $output;
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
        echo '</div><!-- .w-ad -->';
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
                <label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('副标题：', 'kratos');?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('链接地址：', 'kratos');?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_attr($url); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('广告图片:', 'kratos');?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" type="text" value="<?php echo esc_url($image); ?>" />
                <button type="button" class="button-update-media upload_ad"><?php _e('选择图片', 'kratos');?></button>
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
            'description' => __('可跳转后台的个人简介展示工具', 'kratos'),
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
        $introduce = kratos_option('a_about', __('保持饥渴的专注，追求最佳的品质', 'kratos'));
        $avatar = kratos_option('a_gravatar', ASSET_PATH . '/assets/img/gravatar.png');
        $background = !empty($instance['background']) ? $instance['background'] : ASSET_PATH . '/assets/img/about-background.png';

        echo '<div class="widget w-about">';
        echo '<div class="background" style="background:url(' . $background . ') no-repeat center center;-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div><div class="wrapper text-center">';
        if (current_user_can('manage_options')) {
            echo '<a href="' . admin_url() . '">';
        } else {
            echo '<a href="' . wp_login_url() . '">';
        }
        echo '<img src="' . $avatar . '"></a>';
        echo '</div><div class="textwidget text-center"><p>' . $introduce . '</p></div>';
        echo '</div><!-- .w-about -->';
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
                <label for="<?php echo $this->get_field_id('background'); ?>"><?php _e('背景图片:', 'kratos');?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('background'); ?>" name="<?php echo $this->get_field_name('background'); ?>" type="text" value="<?php echo esc_attr($background); ?>">
                <button type="button" class="button-update-media upload_background"><?php _e('选择图片', 'kratos');?></button>
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
        $tags = wp_tag_cloud(array(
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
        echo '</div><!-- .w-tags -->';
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
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示数量：', 'kratos');?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('显示排序：', 'kratos');?></label>
                <select name="<?php echo $this->get_field_name("order"); ?>" id='<?php echo $this->get_field_id("order"); ?>'>
                    <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>><?php _e('降序', 'kratos');?></option>
                    <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>><?php _e('升序', 'kratos');?></option>
                    <option value="RAND" <?php echo ($order == 'RAND') ? 'selected' : ''; ?>><?php _e('随机', 'kratos');?></option>
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

        echo '<div class="widget w-recommended">';
        ?>
        <div class="nav nav-tabs d-none d-xl-flex" id="nav-tab" role="tablist">
            <a class="nav-item nav-link" id="nav-new-tab" data-toggle="tab" href="#nav-new" role="tab" aria-controls="nav-new" aria-selected="false"><i class="kicon i-tabnew"></i><?php _e('最新', 'kratos');?></a>
            <a class="nav-item nav-link active" id="nav-hot-tab" data-toggle="tab" href="#nav-hot" role="tab" aria-controls="nav-hot" aria-selected="true"><i class="kicon i-tabhot"></i><?php _e('热点', 'kratos');?></a>
            <a class="nav-item nav-link" id="nav-random-tab" data-toggle="tab" href="#nav-random" role="tab" aria-controls="nav-random" aria-selected="false"><i class="kicon i-tabrandom"></i><?php _e('随机', 'kratos');?></a>
        </div>
        <div class="nav nav-tabs d-xl-none" id="nav-tab" role="tablist">
            <a class="nav-item nav-link" id="nav-new-tab" data-toggle="tab" href="#nav-new" role="tab" aria-controls="nav-new" aria-selected="false"><?php _e('最新', 'kratos');?></a>
            <a class="nav-item nav-link active" id="nav-hot-tab" data-toggle="tab" href="#nav-hot" role="tab" aria-controls="nav-hot" aria-selected="true"><?php _e('热点', 'kratos');?></a>
            <a class="nav-item nav-link" id="nav-random-tab" data-toggle="tab" href="#nav-random" role="tab" aria-controls="nav-random" aria-selected="false"><?php _e('随机', 'kratos');?></a>
        </div>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade" id="nav-new" role="tabpanel" aria-labelledby="nav-new-tab">
            <?php $myposts = get_posts('numberposts=' . $number . ' & offset=0');foreach ($myposts as $post): ?>
                <a class="bookmark-item" title="<?php echo $post->post_title; ?>" href="<?php echo get_permalink($post->ID); ?>" rel="bookmark"><i class="kicon i-book"></i><?php echo strip_tags($post->post_title) ?></a>
            <?php endforeach;?>
            </div>
            <div class="tab-pane fade show active" id="nav-hot" role="tabpanel" aria-labelledby="nav-hot-tab">
            <?php if (function_exists('most_comm_posts')) {most_comm_posts($days, $number);}?>
            </div>
            <div class="tab-pane fade" id="nav-random" role="tabpanel" aria-labelledby="nav-random-tab">
            <?php $myposts = get_posts('numberposts=' . $number . ' & offset=0 & orderby=rand');foreach ($myposts as $post): ?>
                <a class="bookmark-item" title="<?php echo $post->post_title; ?>" href="<?php echo get_permalink($post->ID); ?>" rel="bookmark"><i class="kicon i-book"></i><?php echo strip_tags($post->post_title) ?></a>
            <?php endforeach;?>
            </div>
        </div>
        <?php echo '</div><!-- .w-recommended -->';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['number'] = (!empty($new_instance['number'])) ? $new_instance['number'] : '';
        $instance['days'] = (!empty($new_instance['days'])) ? $new_instance['days'] : '';

        return $instance;
    }
    public function form($instance)
    {
        global $wpdb;
        $number = !empty($instance['number']) ? $instance['number'] : '6';
        $days = !empty($instance['days']) ? $instance['days'] : '30';
        ?>
        <div class="media-widget-control">
            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('展示数量：', 'kratos');?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('days'); ?>"><?php _e('统计天数：', 'kratos');?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" type="text" value="<?php echo esc_attr($days); ?>" />
            </p>
        </div>
        <?php
    }
}

function register_widgets()
{
    register_widget('widget_ad');
    register_widget('widget_about');
    register_widget('widget_tags');
    register_widget('widget_posts');
}
add_action('widgets_init', 'register_widgets');