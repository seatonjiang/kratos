<?php
/**
 * The template for Settings content control
 *
 * @package Vtrois
 * @version 2.4
 */

function optionsframework_option_name() {

	$themename = wp_get_theme();
	$themename = preg_replace("/\W/", "_", strtolower($themename) );
	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
	
}

function optionsframework_options() {

	$imagepath =  get_template_directory_uri() . '/images/options/';

	$options = array();

	$options[] = array(
		'name' => '站点配置',
		'type' => 'heading');

	$options[] = array(
		'name' => '站点Logo',
		'desc' => '不添加显示文字标题，推荐图片尺寸 200px*50px，保存成功则自动显示Logo图片',
		'id' => 'site_logo',
		'type' => 'upload');
	$options[] = array(
		'name' => '背景颜色',
		'desc' => '针对整个站点背景颜色控制',
		'id' => 'background_index_color',
		'std' => '#f9f9f9',
		'type' => 'color' );
	$options[] = array(
		'name' => '列表布局',
		'desc' => '选择你喜欢的列表布局，默认显示新式列表布局',
		'id' => "list_layout",
		'std' => "new_layout",
		'type' => "images",
		'options' => array(
			'old_layout' => $imagepath . 'old-layout.png',
			'new_layout' => $imagepath . 'new-layout.png')
	);
	$options[] = array(
		'name' => '侧边栏随动',
		'desc' => '是否启用侧边栏小工具随动功能',
		'id' => 'site_sa',
		'std' => '0',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '分类页面',
		'desc' =>'是否启用分类页面的名称以及简介功能',
		'id' => 'show_head_cat',
		'std' => '0',
		'type' => 'checkbox');
	$options[] = array(
		'name' => '标签页面',
		'desc' =>'是否启用标签页面的名称以及简介功能',
		'id' => 'show_head_tag',
		'std' => '0',
		'type' => 'checkbox');
	$options[] = array(
		'name' => '站点黑白',
		'desc' => '是否启用站点黑白功能(一般常用于悼念日)',
		'id' => 'site_bw',
		'std' => '0',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '网易云音乐',
		'desc' => '是否启用网易云音乐自动播放功能',
		'id' => 'wy_music',
		'std' => '0',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '打赏连接',
		'desc' => '输入您的打赏介绍页面的连接，若没开启点赞打赏功能该项无效',
		'id' => 'donate_links',
		'type' => 'text'
	);	
	$options[] = array(
		'name' => '新浪分享AppKey',
		'desc' => '输入您的新浪分享AppKey，若留空不会影响分享功能',
		'id' => 'sina_appkey',
		'type' => 'text'
	);
	$options[] = array(
		'name' => '工信部备案信息',
		'desc' => '输入您的工信部备案号，针对国际版没有备案信息栏目的功能',
		'id' => 'icp_num',
		'type' => 'text'
	);	
	$options[] = array(
		'name' => '公安网备案信息',
		'desc' => '输入您的公安网备案号',
		'id' => 'gov_num',
		'type' => 'text'
	);	
	$options[] = array(
		'name' => '公安网备案连接',
		'desc' => '输入您的公安网备案的链接地址',
		'id' => 'gov_link',
		'type' => 'text'
	);
	$options[] = array(
		'name' => 'SEO配置',
		'type' => 'heading');
	$options[] = array(
		'name' => '关键词',
		'desc' => '每个关键词之间用英文逗号分割',
		'id' => 'site_keywords',
		'type' => 'text');
	$options[] = array(
		'name' => '站点描述',
		'id' => 'site_description',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'name' => '站点统计',
		'desc' => '填写时需要去掉&lt;script&gt;与&lt;/script&gt;标签',
		'id' => 'site_tongji',
		'std' => '',
		'type' => 'textarea');

	$options[] = array(
		'name' => '顶部配置',
		'type' => 'heading');
	$options[] = array(
		'name' => '顶部类型',
		'desc' => '选择您喜欢的顶部类型并修改其对应选项',
		'id' => 'background_mode',
		'std' => 'image',
		'type' => 'select',
		'class' => 'mini',
		'options' => array(
			'image' => '图片',
			'color' => '颜色')
	);
	$options[] = array(
		'name' => '图片样式',
		'desc' => '只有在类型中选择“图片”才起作用',
		'id' => 'background_image',
		'std' => get_template_directory_uri() . '/images/background.jpg',
		'class' => 'background_image',
		'type' => 'upload');
	$options[] = array(
		'name' => '图片文字-1',
		'id' => 'background_image_text1',
		'desc' => '只有在类型中选择“图片”才起作用',
		'std' => 'Kratos',
		'type' => 'text');
	$options[] = array(
		'name' => '图片文字-2',
		'id' => 'background_image_text2',
		'desc' => '只有在类型中选择“图片”才起作用',
		'std' => 'A responsible theme for WordPress',
		'type' => 'text');
	$options[] = array(
		'name' => '颜色样式',
		'desc' => '只有在类型中选择“颜色”才起作用',
		'id' => 'background_color',
		'std' => '#222831',
		'class' => "background_color",
		'type' => 'color' );

	$options[] = array(
		'name' => '内容页面',
		'type' => 'heading');
	$options[] = array(
		'name' => '文章布局',
		'desc' => '选择你喜欢的整体布局（显示左边栏，右边栏）默认显示右边栏',
		'id' => "side_bar",
		'std' => "right_side",
		'type' => "images",
		'options' => array(
			'left_side' => $imagepath . 'col-left.png',
			'right_side' => $imagepath . 'col-right.png')
	);
	$options[] = array(
		'name' => '版权声明',
		'desc' => '是否启用 CC BY-SA 4.0 声明',
		'id' => 'post_cc',
		'std' => '1',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '分享按钮',
		'desc' => '是否启用文章分享功能',
		'id' => 'post_share',
		'std' => '1',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '打赏按钮',
		'desc' => '是否启用文章打赏功能',
		'id' => 'post_like_donate',
		'std' => '0',
		'type' => 'checkbox',
	);
	$options[] = array(
		'name' => '模板页面',
		'type' => 'heading');
	$options[] = array(
		'name' => '页面布局',
		'desc' => '选择你喜欢的整体布局（显示左边栏，右边栏）默认显示右边栏',
		'id' => "page_side_bar",
		'std' => "right_side",
		'type' => "images",
		'options' => array(
			'left_side' => $imagepath . 'col-left.png',
			'right_side' => $imagepath . 'col-right.png')
	);	
	$options[] = array(
		'name' => '版权声明',
		'desc' => '是否启用 CC BY-SA 4.0 声明',
		'id' => 'page_cc',
		'std' => '0',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '分享按钮',
		'desc' => '是否启用文章分享功能',
		'id' => 'page_share',
		'std' => '0',
		'type' => 'checkbox'
	);
	$options[] = array(
		'name' => '打赏按钮',
		'desc' => '是否启用文章打赏功能',
		'id' => 'page_like_donate',
		'std' => '0',
		'type' => 'checkbox',
	);
	$options[] = array(
		'name' => '404页面',
		'type' => 'heading');
	$options[] = array(
		'name' => '页面标题',
		'id' => 'error_text1',
		'std' => '这里已经是废墟，什么东西都没有',
		'type' => 'text');
	$options[] = array(
		'name' => '简介说明',
		'id' => 'error_text2',
		'std' => 'That page can not be found',
		'type' => 'text');
	$options[] = array(
		'name' => '页面背景',
		'id' => 'error_image',
		'std' => get_template_directory_uri() . '/images/404.jpg',
		'class' => 'error_image',
		'type' => 'upload');

	$options[] = array(
		'name' => '轮播图片',
		'type' => 'heading');
	$options[] = array(
		'name' => '是否启用轮播',
		'desc' => '图片宽度建议大于750像素',
		'id' => 'kratos_banner',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini',
		'options' => array(
			'1' => '是',
			'0' => '否')
	);
	$options[] = array(
		'name' => '轮播图片-1',
		'id' => 'kratos_banner1',
		'type' => 'upload');
	$options[] = array(
		'name' => '轮播链接-1',
		'desc' => '链接可以留空',
		'id' => 'kratos_banner_url1',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-2',
		'id' => 'kratos_banner2',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接2',
		'desc' => '链接可以留空',
		'id' => 'kratos_banner_url2',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-3',
		'id' => 'kratos_banner3',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接3',
		'desc' => '链接可以留空',
		'id' => 'kratos_banner_url3',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-4',
		'id' => 'kratos_banner4',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接4',
		'desc' => '链接可以留空',
		'id' => 'kratos_banner_url4',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-5',
		'id' => 'kratos_banner5',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接5',
		'desc' => '链接可以留空',
		'id' => 'kratos_banner_url5',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => '底部组件',
		'type' => 'heading');
	$options[] = array(
		'name' => '新浪微博',
		'desc' => '连接前要带有 http:// 或者 https:// ',
		'id' => 'social_weibo',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '腾讯微博',
		'desc' => '连接前要带有 http:// 或者 https:// ',
		'id' => 'social_tweibo',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => 'Twitter',
		'desc' => '连接前要带有 http:// 或者 https:// ',
		'id' => 'social_twitter',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => 'FaceBook',
		'desc' => '连接前要带有 http:// 或者 https:// ',
		'id' => 'social_facebook',
		'std' => '',
		'type' => 'text');
		$options[] = array(
		'name' => 'LinkedIn',
		'desc' => '连接前要带有 http:// 或者 https:// ',
		'id' => 'social_linkedin',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => 'GitHub',
		'desc' => '连接前要带有 http:// 或者 https:// ',
		'id' => 'social_github',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '广告配置',
		'type' => 'heading');
	$options[] = array(
		'name' => '广告位置',
		'desc' => '选择图片广告放置的位置',
		'id' => 'ad_show',
		'std' => array(
				'top' => 0,
				'footer' => 0),
		'type' => 'multicheck',
		'options' => array(
			'top' => '文章页顶部',
			'footer' => '文章页底部'));
	$options[] = array(
		'name' => '广告图片',
		'desc' => '图片宽度大于750px',
		'id' => 'ad_img',
		'type' => 'upload');
	$options[] = array(
		'name' => '广告代码',
		'desc' => '可放置浮动广告HTML代码',
		'id' => 'ad_code',
		'std' => '',
		'type' => 'textarea');
	return $options;
}