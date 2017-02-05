<?php
/**
 * widget: author
 * @author renxia <l@lzw.me>
 */

add_action( 'widgets_init', create_function('', 'return register_widget("kratos_widget_author");'));

class kratos_widget_author extends WP_Widget {
    function kratos_widget_author() {
        $widget_ops = array(
            'classname' => 'kratos_widget_author',
            'name'        => 'Kratos - 作者简介',
            'description' => 'Kratos主题特色组件 - 作者简介'
        );
        // parent::WP_Widget( false, false, $widget_ops );
        // $widget_ops = array( 'classname' => 'kratos_widget_author', 'description' => '显示作者的信息简介' );
        $this->__construct( false, '作者信息', $widget_ops );
    }

    function widget( $args, $instance ) {
        global $wpdb;
        extract( $args );
        $profile = $instance['profile'] ? $instance['profile'] : '';
        $imgurl = $instance['imgurl'] ? $instance['imgurl'] : '';
        $bkimgurl = $instance['bkimgurl'] ? $instance['bkimgurl'] : '';
        $statistics = $instance['statistics'] ? true : false;
        $startdate = $instance['startdate'] ? $instance['startdate'] : '2009-06-28';

        echo $before_widget;
        ?>
                <?php if(!empty($bkimgurl)) {?>
                <div class="photo-background">
                    <div class="photo-background" style="background:url(<?php echo $bkimgurl;?>) no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>
                </div>
                <?php }else{?>
                <div class="photo-background" style="background:url(<?php echo bloginfo('template_url'); ?>/images/about.jpg) no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>
                <?php }?>
                <?php if(!empty($imgurl)) {?>
                <div class="photo-wrapper clearfix">
                    <div class="photo-wrapper-tip text-center">
                        <img class="about-photo" src="<?php echo $imgurl; ?>" />
                    </div>
                </div>
                <?php }else{?>
                <div class="photo-wrapper clearfix">
                    <div class="photo-wrapper-tip text-center">
                        <img class="about-photo" src="<?php echo bloginfo('template_url'); ?>/images/avatar.png" />
                    </div>
                </div>
                <?php }?>
                <?php if(!empty($profile)) {?>
                <div class="textwidget">
                    <p><?php echo $profile; ?></p>
                </div>
                <?php }?>

                <?php if($statistics) {?>
                <div class="r_statistics">
                    <ul>
                        <li>日志总数：<?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish;?> 篇</li>
                        <li>评论总数：<?php echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments where comment_author!='William'");?> 篇</li>
                        <li>标签数量：<?php echo $count_tags = wp_count_terms('post_tag'); ?> 个</li>
                        <li>链接总数：<?php $link = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'"); echo $link; ?> 个</li>
                        <li>建站日期：<?php echo $startdate; ?> </li>
                        <li>运行天数：<?php echo floor((time()-strtotime($startdate))/86400); ?> 天</li>
                        <li>最后更新：<?php $last = $wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page') AND (post_status = 'publish' OR post_status = 'private')");$last = date('Y-n-j', strtotime($last[0]->MAX_m));echo $last; ?></li>
                    </ul>
                </div>
                <?php }?>
        <?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }

    function form( $instance ) {
        @$imgurl = esc_attr( $instance['imgurl'] );
        @$bkimgurl = esc_attr( $instance['bkimgurl'] );
        @$profile = esc_attr( $instance['profile'] );
        $statistics = esc_attr($instance['statistics']);
        $startdate = esc_attr($instance['startdate']);
        ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'imgurl' ); ?>">
                    头像地址：
                    <input class="widefat" id="<?php echo $this->get_field_id( 'imgurl' ); ?>" name="<?php echo $this->get_field_name( 'imgurl' ); ?>" type="text" value="<?php echo $imgurl; ?>" />
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'profile' ); ?>">
                    简介内容：
                    <textarea class="widefat" rows="4" id="<?php echo $this->get_field_id( 'profile' ); ?>" name="<?php echo $this->get_field_name( 'profile' ); ?>" ><?php echo $profile; ?></textarea>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'bkimgurl' ); ?>">
                    卡片背景：
                    <input class="widefat" id="<?php echo $this->get_field_id( 'bkimgurl' ); ?>" name="<?php echo $this->get_field_name( 'bkimgurl' ); ?>" type="text" value="<?php echo $bkimgurl; ?>" />
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'statistics' ); ?>">
                    显示统计信息：
                    <input class="widefat" type="checkbox"
                        id="<?php echo $this->get_field_id( 'statistics' ); ?>"
                        name="<?php echo $this->get_field_name( 'statistics' ); ?>"
                        <?php checked( $statistics, 'on' ); ?> />
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'startdate' ); ?>">
                    建站日期：
                    <input class="widefat" type="text"
                        id="<?php echo $this->get_field_id( 'startdate' ); ?>"
                        name="<?php echo $this->get_field_name( 'startdate' ); ?>"
                        value="<?php echo $startdate; ?>" />
                </label>
            </p>
        <?php
    }
}

?>
