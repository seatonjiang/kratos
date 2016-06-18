(function() {

	'use strict';

	// iPad and iPod detection	
	var isiPad = function() {
		return (navigator.platform.indexOf("iPad") != -1);
	};
	var isiPhone = function() {
		return (
			(navigator.platform.indexOf("iPhone") != -1) ||
			(navigator.platform.indexOf("iPod") != -1)
		);
	};

	// Main Menu Superfish
	var mainMenu = function() {
		$('#kratos-primary-menu').superfish({
			delay: 0,
			animation: {
				opacity: 'show'
			},
			speed: 'fast',
			cssArrows: true,
			disableHI: true
		});
	};

	// Parallax
	var parallax = function() {
		$(window).stellar();
	};

	// Offcanvas and cloning of the main menu
	var offcanvas = function() {
		var $clone = $('#kratos-menu-wrap').clone();
		$clone.attr({
			'id': 'offcanvas-menu'
		});
		$clone.find('> ul').attr({
			'class': '',
			'id': ''
		});
		$('#kratos-page').prepend($clone);

		// click the burger
		$('.js-kratos-nav-toggle').on('click', function() {
			if ($('body').hasClass('kratos-offcanvas')) {
				$('body').removeClass('kratos-offcanvas');
			} else {
				$('body').addClass('kratos-offcanvas');
			}
		});
		$('#offcanvas-menu').css('height', $(window).height());
		$(window).resize(function() {
			var w = $(window);
			$('#offcanvas-menu').css('height', w.height());
			if (w.width() > 769) {
				if ($('body').hasClass('kratos-offcanvas')) {
					$('body').removeClass('kratos-offcanvas');
				}
			}
		});
	}

	// Click outside of the Mobile Menu
	var mobileMenuOutsideClick = function() {
		$(document).click(function(e) {
			var container = $("#offcanvas-menu, .js-kratos-nav-toggle");
			if (!container.is(e.target) && container.has(e.target).length === 0) {
				if ($('body').hasClass('kratos-offcanvas')) {
					$('body').removeClass('kratos-offcanvas');
				}
			}
		});
	};

	// Animations
	var contentWayPoint = function() {
		var i = 0;
		$('.animate-box').waypoint(function(direction) {
			if (direction === 'down' && !$(this.element).hasClass('animated')) {
				i++;
				$(this.element).addClass('item-animate');
				setTimeout(function() {
					$('body .animate-box.item-animate').each(function(k) {
						var el = $(this);
						setTimeout(function() {
							el.addClass('fadeInUp animated');
							el.removeClass('item-animate');
						}, k * 200, 'easeInOutExpo');
					});
				}, 100);
			}
		}, {
			offset: '85%'
		});
	};

	//Show love
	$.fn.postLike = function() {
		if ($(this).hasClass('done')) {
			alert('您已赞过该文章');
			return false;
		} else {
			$(this).addClass('done');
			var id = $(this).data("id"),
			action = $(this).data('action'),
			rateHolder = $(this).children('.count');
			var ajax_data = {
				action: "kratos_love",
				um_id: id,
				um_action: action
			};
			$.post("/wp-admin/admin-ajax.php", ajax_data,
			function(data) {
				$(rateHolder).html(data);
			});
			return false;
		}
	};
	$(document).on("click", ".KratosLove",
		function() {
			$(this).postLike();
	});

	//Go Top
	var offset = 300,
		offset_opacity = 1200,
		scroll_top_duration = 700,
		$back_to_top = $('.cd-top');
	$(window).scroll(function(){
		( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
		if( $(this).scrollTop() > offset_opacity ) { 
			$back_to_top.addClass('cd-fade-out');
		}
	});

	$back_to_top.on('click', function(event){
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0 ,
		 	}, scroll_top_duration
		);
	});
	
	// Document on load.
	$(function() {
		mainMenu();
		parallax();
		offcanvas();
		mobileMenuOutsideClick();
		contentWayPoint();
	});

}());