<?php

function optionsframework_option_name() {

	$themename = wp_get_theme();
	$themename = preg_replace("/\W/", "_", strtolower($themename) );
	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
	
}

function optionsframework_options() {

	$options = array();

	$options[] = array(
		'name' => '站点配置',
		'type' => 'heading');
	$options[] = array(
		'name' => '关键词',
		'desc' => '提示：每个关键词之间用英文逗号分割',
		'id' => 'site_keywords',
		'type' => 'text');
	$options[] = array(
		'name' => '站点描述',
		'id' => 'site_description',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'name' => '站点统计',
		'desc' => '提示：填写时需要去掉前面的 &lt;script 与后面的 &lt;/script&gt; ',
		'id' => 'site_tongji',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'name' => '站点黑白',
		'desc' => '提示：是否启用站点黑白功能(用于悼念日)',
		'id' => 'site_bw',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini',
		'options' => array(
			'0' => '否',
			'1' => '是')
	);
	$options[] = array(
		'name' => '站点Logo',
		'desc' => '提示：不添加显示文字标题，推荐图片尺寸 200px*50px，保存成功则自动显示Logo图片',
		'id' => 'site_logo',
		'type' => 'upload');
	$options[] = array(
		'name' => '头部类型',
		'desc' => '提示：选择您喜欢的头部类型并修改其对应选项',
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
		'desc' => '提示：只有在类型中选择“图片”才起作用',
		'id' => 'background_image',
		'std' => get_template_directory_uri() . '/images/background.jpg',
		'class' => 'background_image',
		'type' => 'upload');
	$options[] = array(
		'name' => '图片文字-1',
		'id' => 'background_image_text1',
		'desc' => '提示：只有在类型中选择“图片”才起作用',
		'std' => 'Kratos',
		'type' => 'text');
	$options[] = array(
		'name' => '图片文字-2',
		'id' => 'background_image_text2',
		'desc' => '提示：只有在类型中选择“图片”才起作用',
		'std' => 'A responsible theme for WordPress',
		'type' => 'text');
	$options[] = array(
		'name' => '颜色样式',
		'desc' => '提示：只有在类型中选择“颜色”才起作用',
		'id' => 'background_color',
		'std' => '#222831',
		'class' => "background_color",
		'type' => 'color' );

	$options[] = array(
		'name' => '文章页面',
		'type' => 'heading');
	$options[] = array(
		'name' => '版权声明',
		'desc' => '提示：是否启用 CC BY-SA 4.0 声明',
		'id' => 'post_cc',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini',
		'options' => array(
			'0' => '是',
			'1' => '否')
	);
	$options[] = array(
		'name' => '点赞打赏',
		'desc' => '提示：是否启用点赞打赏功能',
		'id' => 'post_like_donate',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini',
		'options' => array(
			'0' => '是',
			'1' => '否')
	);
	$options[] = array(
		'name' => '打赏页面',
		'desc' => '提示：输入您的打赏介绍页面的连接，若没开启点赞打赏功能该项无效',
		'id' => 'donate_links',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => '轮播图片',
		'type' => 'heading');
	$options[] = array(
		'name' => '是否启用轮播',
		'desc' => '注意：图片宽度建议大于750像素',
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
		'desc' => '提示：链接可以留空',
		'id' => 'kratos_banner_url1',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-2',
		'id' => 'kratos_banner2',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接2',
		'desc' => '提示：链接可以留空',
		'id' => 'kratos_banner_url2',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-3',
		'id' => 'kratos_banner3',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接3',
		'desc' => '提示：链接可以留空',
		'id' => 'kratos_banner_url3',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-4',
		'id' => 'kratos_banner4',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接4',
		'desc' => '提示：链接可以留空',
		'id' => 'kratos_banner_url4',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '轮播图片-5',
		'id' => 'kratos_banner5',
		'type' => 'upload');
	$options[] = array(
		'name' => '链接5',
		'desc' => '提示：链接可以留空',
		'id' => 'kratos_banner_url5',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => '底部组件',
		'type' => 'heading');
	$options[] = array(
		'name' => '站点订阅',
		'desc' => '是否启用站点订阅功能',
		'id' => 'site_rss',
		'std' => '1',
		'type' => 'select',
		'class' => 'mini',
		'options' => array(
			'1' => '是',
			'0' => '否')
	);
	$options[] = array(
		'name' => '邮件列表ID',
		'desc' => '提示：<a href="http://list.qq.com" target="_blank">点此</a>获取邮件列表ID',
		'id' => 'site_rss_id',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '新浪微博',
		'desc' => '提示：连接前要带有 http:// 或者 https:// ',
		'id' => 'social_weibo',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => '腾讯微博',
		'desc' => '提示：连接前要带有 http:// 或者 https:// ',
		'id' => 'social_tweibo',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => 'Twitter',
		'desc' => '提示：连接前要带有 http:// 或者 https:// ',
		'id' => 'social_twitter',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => 'FaceBook',
		'desc' => '提示：连接前要带有 http:// 或者 https:// ',
		'id' => 'social_facebook',
		'std' => '',
		'type' => 'text');
		$options[] = array(
		'name' => 'LinkedIn',
		'desc' => '提示：连接前要带有 http:// 或者 https:// ',
		'id' => 'social_linkedin',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => 'GitHub',
		'desc' => '提示：连接前要带有 http:// 或者 https:// ',
		'id' => 'social_github',
		'std' => '',
		'type' => 'text');

	return $options;
}