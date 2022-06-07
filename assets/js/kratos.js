/*!
 * Kratos
 * Seaton Jiang <hi@seatonjiang.com>
 */
; (function () {
    'use strict'

    var KRATOS_VERSION = '4.1.4'

    var navbarConfig = function () {
        $('#navbutton').on('click', function () {
            $('.navbar-toggler').toggleClass('nav-close')
        })
    }

    var tooltipConfig = function () {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    }

    var gotopConfig = function () {
        $(window).on('load', function () {
            var $win = $(window)
            var setShowOrHide = function () {
                if ($win.scrollTop() > 200) {
                    $('.gotop').addClass('active')
                } else {
                    $('.gotop').removeClass('active')
                }
            }
            setShowOrHide()
            $win.scroll(setShowOrHide)
        })
        $('.gotop').on('click', function (event) {
            event.preventDefault()
            $('html, body').animate(
                {
                    scrollTop: $('html').offset().top
                },
                500
            )
            return false
        })
    }

    var searchConfig = function () {
        $('.search').on('click', function (e) {
            $('.search-form').animate(
                {
                    width: '200px'
                },
                200
            ),
                $('.search-form input').css('display', 'block')
            $(document).one('click', function () {
                $('.search-form').animate(
                    {
                        width: '0'
                    },
                    100
                ),
                    $('.search-form input').hide()
            })
            e.stopPropagation()
        })
        $('.search-form').on('click', function (e) {
            e.stopPropagation()
        })
    }

    var wechatConfig = function () {
        $('.wechat').mouseout(function () {
            $('.wechat-pic')[0].style.display = 'none'
        })
        $('.wechat').mouseover(function () {
            $('.wechat-pic')[0].style.display = 'block'
        })
    }

    var smiliesConfig = function () {
        $('#addsmile').on('click', function (e) {
            $('.smile').toggleClass('open')
            $(document).one('click', function () {
                $('.smile').toggleClass('open')
            })
            e.stopPropagation()
            return false
        })
    }

    var postlikeConfig = function () {
        $.fn.postLike = function () {
            if ($(this).hasClass('done')) {
                layer.msg(kratos.repeat, function () { })
                return false
            } else {
                $(this).addClass('done')
                layer.msg(kratos.thanks)
                var id = $(this).data('id'),
                    action = $(this).data('action')
                var ajax_data = {
                    action: 'love',
                    um_id: id,
                    um_action: action
                }
                $.post(kratos.site + '/wp-admin/admin-ajax.php', ajax_data, function (data) { })
                return false
            }
        }
        $(document).on('click', '.btn-thumbs', function () {
            $(this).postLike()
        })
    }

    var donateConfig = function () {
        $('#donate').on('click', function () {
            layer.open({
                type: 1,
                area: ['300px', '370px'],
                title: kratos.donate,
                resize: false,
                scrollbar: false,
                content:
                    '<div class="donate-box"><div class="meta-pay text-center my-2"><strong>' +
                    kratos.scan +
                    '</strong></div><div class="qr-pay text-center"><img class="pay-img" id="alipay_qr" src="' +
                    kratos.alipay +
                    '"><img class="pay-img d-none" id="wechat_qr" src="' +
                    kratos.wechat +
                    '"></div><div class="choose-pay text-center mt-2"><input id="alipay" type="radio" name="pay-method" checked><label for="alipay" class="pay-button"><img src="' +
                    kratos.directory +
                    '/assets/img/payment/alipay.png"></label><input id="wechatpay" type="radio" name="pay-method"><label for="wechatpay" class="pay-button"><img src="' +
                    kratos.directory +
                    '/assets/img/payment/wechat.png"></label></div></div>'
            })
            $(".choose-pay input[type='radio']").click(function () {
                var id = $(this).attr('id')
                if (id == 'alipay') {
                    $('.qr-pay #alipay_qr').removeClass('d-none')
                    $('.qr-pay #wechat_qr').addClass('d-none')
                }
                if (id == 'wechatpay') {
                    $('.qr-pay #alipay_qr').addClass('d-none')
                    $('.qr-pay #wechat_qr').removeClass('d-none')
                }
            })
        })
    }

    var accordionConfig = function () {
        $(document).on('click', '.acheader', function (event) {
            var $this = $(this)
            $this.closest('.accordion').find('.contents').slideToggle(300)
            if ($this.closest('.accordion').hasClass('active')) {
                $this.closest('.accordion').removeClass('active')
            } else {
                $this.closest('.accordion').addClass('active')
            }
            event.preventDefault()
        })
    }

    var consoleConfig = function () {
        console.log('\n Kratos v' + KRATOS_VERSION + '\n\n https://github.com/seatonjiang/kratos \n\n')
    }

    var lightGalleryConfig = function () {
        lightGallery(document.getElementById('lightgallery'), {
            selector: 'a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".gif"], a[href$=".bmp"], a[href$=".webp"]'
        })
    }

    window.initKratos = function () {
        accordionConfig()
        navbarConfig()
        tooltipConfig()
        gotopConfig()
        searchConfig()
        wechatConfig()
        smiliesConfig()
        postlikeConfig()
        donateConfig()
        consoleConfig()
        lightGalleryConfig()
    };
    $(window.initKratos);
})()

function grin(tag) {
    var myField
    tag = ' ' + tag + ' '
    if (document.getElementById('comment') && document.getElementById('comment').type == 'textarea') {
        myField = document.getElementById('comment')
    } else {
        return false
    }
    if (document.selection) {
        myField.focus()
        sel = document.selection.createRange()
        sel.text = tag
        myField.focus()
    } else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart
        var endPos = myField.selectionEnd
        var cursorPos = endPos
        myField.value = myField.value.substring(0, startPos) + tag + myField.value.substring(endPos, myField.value.length)
        cursorPos += tag.length
        myField.focus()
        myField.selectionStart = cursorPos
        myField.selectionEnd = cursorPos
    } else {
        myField.value += tag
        myField.focus()
    }
}
