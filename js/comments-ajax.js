/**
 * WordPress jQuery-Ajax-Comments v1.0 by Bigfa.
 * URI: http://fatesinger.com/jquery-ajax-comments
 * Thanks Willin Kan
 * Require Jquery 1.7+
 */
jQuery(document).ready(function($) {
	var $commentform = $('#commentform'),
		txt1 = '<div id="loading"><i class="fa fa-spinner fa-spin"></i> 正在提交, 请稍候...</div>',
		txt2 = '<p id="error" class="text-danger">#</p>',
		txt3 = '">提交成功',
		num = 0,
		comm_array =[],
		$comments = $('#comments-title'),
		$cancel = $('#cancel-comment-reply-link'),
		cancel_text = $cancel.text(),
		$submit = $('#commentform #submit'); $submit.attr('disabled', false),
		$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
	$('#comment').after( txt1 + txt2 ); $('#loading').hide(); $('#error').hide();

	/** submit */
	$(document).on("submit", "#commentform",
	function() {
		editcode();
		$('#loading').slideDown();
		$submit.attr('disabled', true).fadeTo('slow', 0.5);
		/** Ajax */
		$.ajax( {
			url: SPECS.um_ajaxurl,
			data: $(this).serialize() + "&action=ajax_comment",
			type: $(this).attr('method'),

			error: function(request) {
				$('#loading').slideUp();
				$('#error').slideDown().html(request.responseText);
				setTimeout(function() {$submit.attr('disabled', false).fadeTo('slow', 1); $('#error').slideUp();}, 3000);
				},

			success: function(data) {
				//alert(data);
				if (data.match(/^错误：/)) {
					$('#error').slideDown().html(data);
					$('#loading').hide();
					setTimeout(function() {$submit.attr('disabled', false).fadeTo('slow', 1); $('#error').slideUp();}, 3000);
					return false;
				}
				$('#loading').hide();
				comm_array.push($('#comment').val());
				$('textarea').each(function() {this.value = ''});
				var t = addComment, cancel = t.I('cancel-comment-reply-link'), temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId), post = t.I('comment_post_ID').value, parent = t.I('comment_parent').value;

				// comments
				if ( $comments.length ) {
					n = parseInt($comments.text().match(/\d+/));
					$comments.text($comments.text().replace( n, n + 1 ));
				}

				// show comment
				new_htm = '" id="new_comm_' + num + '"></';
				new_htm = ( parent == '0' ) ? ('\n<ul style="clear:both;" class="list-unstyled commentlist' + new_htm + 'ul>') : ('\n<ul class="children' + new_htm + 'ul>');

				ok_htm = '\n<div class="alert alert-success" id="success_' + num + txt3;
				ok_htm += '</div><span></span>\n';

				$('#respond').before(new_htm);
				$('#new_comm_' + num).hide().append(data);
				$('#new_comm_' + num + ' li').append(ok_htm);
				$('#new_comm_' + num).fadeIn(4000);

				$body.animate( { scrollTop: $('#new_comm_' + num).offset().top - 200}, 900);
				countdown(); num++ ;
				cancel.style.display = 'none';
				cancel.onclick = null;
				t.I('comment_parent').value = '0';
				if ( temp && respond ) {
					temp.parentNode.insertBefore(respond, temp);
					temp.parentNode.removeChild(temp)
				}
			}
		}); // end Ajax
		return false;
	}); // end submit

	/** comment-reply.dev.js */
	addComment = {
		moveForm : function(commId, parentId, respondId, postId, num) {
			var t = this, div, comm = t.I(commId), respond = t.I(respondId), cancel = t.I('cancel-comment-reply-link'), parent = t.I('comment_parent'), post = t.I('comment_post_ID');
			t.respondId = respondId;
			postId = postId || false;

			if ( !t.I('wp-temp-form-div') ) {
				div = document.createElement('div');
				div.id = 'wp-temp-form-div';
				div.style.display = 'none';
				respond.parentNode.insertBefore(div, respond)
			}

			!comm ? (
				temp = t.I('wp-temp-form-div'),
				t.I('comment_parent').value = '0',
				temp.parentNode.insertBefore(respond, temp),
				temp.parentNode.removeChild(temp)
			) : comm.parentNode.insertBefore(respond, comm.nextSibling);

			$body.animate( { scrollTop: $('#respond').offset().top - 180 }, 400);

			if ( post && postId ) post.value = postId;
			parent.value = parentId;
			cancel.style.display = '';

			cancel.onclick = function() {
				var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);

				t.I('comment_parent').value = '0';
				if ( temp && respond ) {
					temp.parentNode.insertBefore(respond, temp);
					temp.parentNode.removeChild(temp);
				}
				this.style.display = 'none';
				this.onclick = null;
				return false;
			};

			try { t.I('comment').focus(); }
			catch(e) {}

			return false;
		},

		I : function(e) {
			return document.getElementById(e);
		}
	}; // end addComment

	var wait = 15, submit_val = $submit.val();
	function countdown() {
		if ( wait > 0 ) {
			$submit.val("提交成功("+wait+")"); wait--; setTimeout(countdown, 1000);
		} else {
			$submit.val(submit_val).attr('disabled', false).fadeTo('slow', 1);
			wait = 15;
	  }
	}
function editcode() {
	var a = "",
	b = $("#comment").val(),
	start = b.indexOf("<code>"),
	end = b.indexOf("</code>");
	if (start > -1 && end > -1 && start < end) {
		a = "";
		while (end != -1) {
			a += b.substring(0, start + 6) + b.substring(start + 6, end).replace(/<(?=[^>]*?>)/gi, "&lt;").replace(/>/gi, "&gt;");
			b = b.substring(end + 7, b.length);
			start = b.indexOf("<code>") == -1 ? -6: b.indexOf("<code>");
			end = b.indexOf("</code>");
			if (end == -1) {
				a += "</code>" + b;
				$("#comment").val(a)
			} else if (start == -6) {
				myFielde += "&lt;/code&gt;"
			} else {
				a += "</code>"
			}
		}
	}
	var b = a ? a: $("#comment").val(),
	a = "",
	start = b.indexOf("<pre>"),
	end = b.indexOf("</pre>");
	if (start > -1 && end > -1 && start < end) {
		a = a
	} else return;
	while (end != -1) {
		a += b.substring(0, start + 5) + b.substring(start + 5, end).replace(/<(?=[^>]*?>)/gi, "&lt;").replace(/>/gi, "&gt;");
		b = b.substring(end + 6, b.length);
		start = b.indexOf("<pre>") == -1 ? -5: b.indexOf("<pre>");
		end = b.indexOf("</pre>");
		if (end == -1) {
			a += "</pre>" + b;
			$("#comment").val(a)
		} else if (start == -5) {
			myFielde += "&lt;/pre&gt;"
		} else {
			a += "</pre>"
		}
	}
}
function grin(a) {
	var b;
	a = " " + a + " ";
	if (document.getElementById("comment") && document.getElementById("comment").type == "textarea") {
		b = document.getElementById("comment")
	} else {
		return false
	}
	if (document.selection) {
		b.focus();
		sel = document.selection.createRange();
		sel.text = a;
		b.focus()
	} else if (b.selectionStart || b.selectionStart == "0") {
		var c = b.selectionStart;
		var d = b.selectionEnd;
		var e = d;
		b.value = b.value.substring(0, c) + a + b.value.substring(d, b.value.length);
		e += a.length;
		b.focus();
		b.selectionStart = e;
		b.selectionEnd = e
	} else {
		b.value += a;
		b.focus()
	}
}

});