<?php
/**
 * The template for displaying the header
 * 
 * @author Vtrois <seaton@vtrois.com>
 * @license GPL-3.0
 */
switch (kratos_option('background_mode', 'image')) {
	case 'image':
		echo '<div class="kratos-start kratos-hero-2"><div class="kratos-overlay"></div><div class="kratos-cover kratos-cover_2 text-center" style="background-image: url(' . kratos_option('background_image', get_template_directory_uri() . '/images/background.jpg') . ');"><div class="desc desc2 animate-box"><h2>' . single_cat_title('', false) . '</h2><span>' . category_description() . '</span></div></div></div>';
	break;
	case 'color':
		echo '<div class="kratos-start kratos-hero-2 kratos-post-header"><div class="kratos-overlay kratos-post" style="background:' . kratos_option('background_color', '#222831') . '"></div></div>';
	break;
	default:
		echo '<div class="kratos-start kratos-hero-2 kratos-post-header"><div class="kratos-overlay kratos-post" style="background:#222831"></div></div>';
	break;
}
?>