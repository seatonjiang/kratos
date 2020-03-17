/**
 * Custom scripts needed for the colorpicker, image button selectors,
 * and navigation tabs.
 */

jQuery(document).ready(function($) {

    $('input[id^="s_"]').click(function(){
		jQuery('#section-'+this.id+'_url').fadeToggle(400);
		jQuery('#section-'+this.id+'_links').fadeToggle(400);
	});

	for (i = 0; i < $('input[id^="s_"]'+':checked').length; i++) {
		let id =  $('input[id^="s_"]'+':checked')[i].id;
		$('#section-'+id+'_url').show();
		$('#section-'+id+'_links').show();
	}

	jQuery('#g_cos').click(function() {
		jQuery('#section-g_cos_bucketname').fadeToggle(400);
		jQuery('#section-g_cos_url').fadeToggle(400);
		jQuery('#section-g_cos_accesskey').fadeToggle(400);
		jQuery('#section-g_cos_secretkey').fadeToggle(400);
	});

	if (jQuery('#g_cos:checked').val() !== undefined) {
		jQuery('#section-g_cos_bucketname').show();
		jQuery('#section-g_cos_url').show();
		jQuery('#section-g_cos_accesskey').show();
		jQuery('#section-g_cos_secretkey').show();
	}

	jQuery('#g_cdn').click(function() {
		jQuery('#section-g_cdn_n3').fadeToggle(400);
	});

	if (jQuery('#g_cdn:checked').val() !== undefined) {
		jQuery('#section-g_cdn_n3').show();
	}

	jQuery('#g_cc_switch').click(function() {
		jQuery('#section-g_cc').fadeToggle(400);
	});

	if (jQuery('#g_cc_switch:checked').val() !== undefined) {
		jQuery('#section-g_cc').show();
	}

	jQuery('#g_donate').click(function() {
		jQuery('#section-g_donate_alipay').fadeToggle(400);
		jQuery('#section-g_donate_wechat').fadeToggle(400);
	});

	if (jQuery('#g_donate:checked').val() !== undefined) {
		jQuery('#section-g_donate_alipay').show();
		jQuery('#section-g_donate_wechat').show();
	}

	jQuery('#m_smtp').click(function() {
		jQuery('#section-m_host').fadeToggle(400);
		jQuery('#section-m_sec').fadeToggle(400);
		jQuery('#section-m_port').fadeToggle(400);
		jQuery('#section-m_username').fadeToggle(400);
		jQuery('#section-m_passwd').fadeToggle(400);
		jQuery('#section-m_sendmail').fadeToggle(400);
	});

	if (jQuery('#m_smtp:checked').val() !== undefined) {
		jQuery('#section-m_host').show();
		jQuery('#section-m_sec').show();
		jQuery('#section-m_port').show();
		jQuery('#section-m_username').show();
		jQuery('#section-m_passwd').show();
		jQuery('#section-m_sendmail').show();
	}

	jQuery('#g_thumbnail').click(function() {
		jQuery('#section-g_postthumbnail').fadeToggle(400);
	});

	if (jQuery('#g_thumbnail:checked').val() !== undefined) {
		jQuery('#section-g_postthumbnail').show();
	}

	jQuery('#top_select').change(function() {
		if (jQuery("#top_select").val() == 'color'){
			jQuery('#section-top_color').fadeIn(400);
			jQuery('#section-top_img').fadeOut(400);
			jQuery('#section-top_title').fadeOut(400);
			jQuery('#section-top_describe').fadeOut(400);
		}else{
			jQuery('#section-top_color').fadeOut(400);
			jQuery('#section-top_img').fadeIn(400);
			jQuery('#section-top_title').fadeIn(400);
			jQuery('#section-top_describe').fadeIn(400);
		}
	});

	if (jQuery('#top_select').val() == 'color') {
		jQuery('#section-top_color').show();
		jQuery('#section-top_img').hide();
		jQuery('#section-top_title').hide();
		jQuery('#section-top_describe').hide();
	}else{
		jQuery('#section-top_color').hide();
		jQuery('#section-top_img').show();
		jQuery('#section-top_title').show();
		jQuery('#section-top_describe').show();
	}

	// Loads the color pickers
	$('.of-color').wpColorPicker();

	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');
	});

	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	// Loads tabbed sections if they exist
	if ( $('.nav-tab-wrapper').length > 0 ) {
		options_framework_tabs();
	}

	function options_framework_tabs() {

		var $group = $('.group'),
			$navtabs = $('.nav-tab-wrapper a'),
			active_tab = '';

		// Hides all the .group sections to start
		$group.hide();

		// Find if a selected tab is saved in localStorage
		if ( typeof(localStorage) != 'undefined' ) {
			active_tab = localStorage.getItem('active_tab');
		}

		// If active tab is saved and exists, load it's .group
		if ( active_tab != '' && $(active_tab).length ) {
			$(active_tab).fadeIn();
			$(active_tab + '-tab').addClass('nav-tab-active');
		} else {
			$('.group:first').fadeIn();
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}

		// Bind tabs clicks
		$navtabs.click(function(e) {

			e.preventDefault();

			// Remove active class from all tabs
			$navtabs.removeClass('nav-tab-active');

			$(this).addClass('nav-tab-active').blur();

			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem('active_tab', $(this).attr('href') );
			}

			var selected = $(this).attr('href');

			$group.hide();
			$(selected).fadeIn();

		});
	}

});