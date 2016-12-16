<?php
/**
 * The template for Comments on the emoji
 *
 * @package Vtrois
 * @version 1.0
 */
?><script type="text/javascript" language="javascript">
/* <![CDATA[ */
    function grin(tag) {
        var myField;
        tag = ' ' + tag + ' ';
        if (document.getElementById('comment') && document.getElementById('comment').type == 'textarea') {
            myField = document.getElementById('comment');
        } else {
            return false;
        }
        if (document.selection) {
            myField.focus();
            sel = document.selection.createRange();
            sel.text = tag;
            myField.focus();
        }
        else if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            var cursorPos = endPos;
            myField.value = myField.value.substring(0, startPos)
                          + tag
                          + myField.value.substring(endPos, myField.value.length);
            cursorPos += tag.length;
            myField.focus();
            myField.selectionStart = cursorPos;
            myField.selectionEnd = cursorPos;
        }
        else {
            myField.value += tag;
            myField.focus();
        }
    }
/* ]]> */
</script>
<?php $smilies = '
<a href="javascript:grin(\':razz:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_razz.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':sad:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_sad.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':evil:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_evil.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':exclaim:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_exclaim.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':smile:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_smile.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':redface:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_redface.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':biggrin:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_biggrin.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':surprised:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_surprised.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':eek:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_eek.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':confused:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_confused.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':idea:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_idea.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':lol:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_lol.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':mad:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_mad.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':twisted:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_twisted.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':rolleyes:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_rolleyes.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':wink:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_wink.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':cool:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_cool.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':arrow:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_arrow.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':neutral:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_neutral.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':cry:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_cry.png" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':mrgreen:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_mrgreen.png" alt="" class="size-smiley"/></a>'
?>