<script type="text/javascript" language="javascript">
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
<a href="javascript:grin(\':razz:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_razz.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':sad:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_sad.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':evil:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_evil.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':exclaim:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_exclaim.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':smile:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_smile.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':redface:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_redface.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':biggrin:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_biggrin.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':surprised:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_surprised.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':eek:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_eek.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':confused:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_confused.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':idea:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_idea.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':lol:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_lol.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':mad:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_mad.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':twisted:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_twisted.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':rolleyes:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_rolleyes.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':wink:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_wink.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':cool:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_cool.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':arrow:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_arrow.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':neutral:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_neutral.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':cry:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_cry.gif" alt="" class="size-smiley"/></a>
<a href="javascript:grin(\':mrgreen:\')"><img src="'.get_bloginfo("template_url").'/images/smilies/icon_mrgreen.gif" alt="" class="size-smiley"/></a>'
?>