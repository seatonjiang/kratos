<?php
switch (kratos_option('site_rss')) {
	case '1':
		if(kratos_option('background_image')){
			echo '<div id="kratos-subscribe" class="kratos-page-default"><div class="container"><h3><label for="email">获取我的最新消息？</label></h3><form action="http://list.qq.com/cgi-bin/qf_compose_send" target="_blank" method="post"><i class="kratos-icon fa fa-send"></i><input type="hidden" name="t" value="qf_booked_feedback"><input type="hidden" name="id" value="'.kratos_option('site_rss_id').'"><input type="email" class="form-control" placeholder="输入您的邮箱地址" id="email" name="to"><input type="submit" value="订 阅" class="btn btn-primary"></form></div></div>';}
	break;
	case '0':
	break;	
	}
?>