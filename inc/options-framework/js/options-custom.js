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
	});

	if (jQuery('#m_smtp:checked').val() !== undefined) {
		jQuery('#section-m_host').show();
		jQuery('#section-m_sec').show();
		jQuery('#section-m_port').show();
		jQuery('#section-m_username').show();
		jQuery('#section-m_passwd').show();
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