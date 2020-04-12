<?php
/**
 * 文章工具栏
 * @author Seaton Jiang <seaton@vtrois.com>
 * @license MIT License
 * @version 2020.04.12
 */
?>
<div class="toolbar clearfix">
    <div class="meta float-md-left">
		<img src="<?php echo kratos_option('a_gravatar', ASSET_PATH . '/assets/img/gravatar.png'); ?>">
		<p class="name"><?php echo kratos_option('a_nickname','Kratos'); ?></p>
		<p class="motto mb-0"><?php echo kratos_option('a_about', __('保持饥渴的专注，追求最佳的品质', 'kratos')); ?></p>
	</div>
	<div class="share float-md-right text-center">
        <?php if(kratos_option('g_donate',false)){ ?>
		    <a href="javascript:;" id="donate" class="btn btn-donate mr-3" role="button"><i class="kicon i-donate"></i> <?php _e('打赏','kratos'); ?></a>
        <?php } ?>
		    <a href="javascript:;" id="thumbs" data-action="love" data-id="<?php the_ID(); ?>" role="button" class="btn btn-thumbs <?php if(isset($_COOKIE['love_'.$post->ID])) echo 'done'; ?>" ><i class="kicon i-like"></i><span class="ml-1"><?php _e('点赞','kratos'); ?></span></a>
	</div>
</div>