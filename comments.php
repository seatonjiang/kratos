<?php
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'amadeus' ); ?></h2>
			<div class="nav-links">
				<div class="nav-previous"><?php previous_comments_link( __( 'Older Comments', 'amadeus' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'amadeus' ) ); ?></div>
			</div>
		</nav>
		<?php endif; ?>
		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style' => 'ol',
					'short_ping' => true,
					'avatar_size'=> 60,
				) );
			?>
		</ol>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'amadeus' ); ?></h2>
			<div class="nav-links">
				<div class="nav-previous"><?php previous_comments_link( __( 'Older Comments', 'amadeus' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'amadeus' ) ); ?></div>
			</div>
		</nav>
		<?php endif; ?>
	<?php endif; ?>
	<?php
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'amadeus' ); ?></p>
	<?php endif; ?>
	<?php include(TEMPLATEPATH . '/smiley.php');?>
	<?php 
		$fields =  array(
   			 'author' => '<div class="comment-form-author form-group has-feedback"><div class="input-group"><div class="input-group-addon"><i class="fa fa-user"></i></div><input class="form-control" placeholder="昵称" id="author" name="author" type="text" value="" ' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /><span class="form-control-feedback required">*</span></div></div>',
   			 'email'  => '<div class="comment-form-email form-group has-feedback"><div class="input-group"><div class="input-group-addon"><i class="fa fa-envelope-o"></i></div><input class="form-control" placeholder="邮箱" id="email" name="email" type="text" value="" ' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /><span class="form-control-feedback required">*</span></div></div>',
   			 'url'  => '<div class="comment-form-url form-group has-feedback"><div class="input-group"><div class="input-group-addon"><i class="fa fa-link"></i></div><input class="form-control" placeholder="网站" id="url" name="url" type="text" value="" ' . esc_attr(  $commenter['comment_author_url'] ) . '" size="30"' . $aria_req . ' /></div></div>',
		);
		$args = array(
			'title_reply_before' => '<h4 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h4>',
			'fields' =>  $fields,
			'class_submit' => 'btn btn-primary',
			'comment_field' =>  '<div class="comment form-group has-feedback"><div class="input-group"><p>'.$smilies.'</p><textarea class="form-control" id="comment" placeholder=" " name="comment" rows="5" aria-required="true" required  onkeydown="if(event.ctrlKey){if(event.keyCode==13){document.getElementById(\'submit\').click();return false}};"></textarea></div></div>',
		);
		comment_form($args);
	?>

</div>