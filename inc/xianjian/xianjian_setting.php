<?php
if (!defined('ABSPATH')) exit;
if ($_POST['xianjian-input'] == "true") {
	if (current_user_can('manage_options')) {
		$xianjian_config_key = "paradigm_render_config";

		$new_config_str = $_POST['xianjian-config'];
		$new_config_str = sanitize_text_field($new_config_str);
		if (is_string($new_config_str)) {
			$new_config_str = stripslashes($new_config_str);

			$new_config = json_decode($new_config_str, true);
			$original_config_str = get_option($xianjian_config_key);
			$original_config = null;
			if ($original_config_str == '') {
				$original_config = array();
			} else {
				$original_config = json_decode($original_config_str, true);
			}
			$scene_id = $new_config['sceneId'];
			if (in_array('delete', $new_config)) {
				$delete_arr = array($scene_id => "1" );
				$original_config = array_diff_key($original_config,	$delete_arr);
			} elseif (in_array('modify', $new_config)) {
				$original_config[$scene_id] = $new_config;
			}
			$total_config_str = json_encode($original_config);
			if (strlen($total_config_str) > 5) {
				update_option($xianjian_config_key,$total_config_str);
			}
		}
	}
}
?>
<iframe name="xianjian-hidden-iframe" id="xianjian-hidden-iframe" style="display:none;"></iframe>
<script type="text/javascript">
	 window.addEventListener('message',function(event){	
    if(event.origin == 'https://nbrecsys.4paradigm.com'){
		if(event.data){
		   buttonClicked(event.data);
		   }
    }
  },false);
	function buttonClicked(dic) {
		if (dic == "") {
			return;
		}
		var form = document.createElement('form');
		form.id = "xianjian-form";
		form.name = "setting";
		form.method = "post";
		form.action = "";
		form.target = "xianjian-hidden-iframe";

		var input = document.createElement('input');
		input.type = "hidden";
		input.name = "xianjian-input";
		input.value = "true";
		form.appendChild(input);

		var input_config = document.createElement('input');
		input_config.type = "hidden";
		input_config.name = "xianjian-config";
		input_config.value = dic;
		form.appendChild(input_config);

		document.body.appendChild(form);

		form.submit();

		document.body.removeChild(form);
	}
</script>
<iframe  src='https://nbrecsys.4paradigm.com/#/plugInBk/list?type=wordpress&siteId=<?php $site_id_key="paradigm_site_id";$site_id=get_option($site_id_key);if($site_id=="") {$characters = "0123456789abcdefghijklmnopqrstuvwxyz"; for($i=0;$i<16;$i++) {$site_id .= $characters[rand(0,strlen($characters)-1)];} update_option($site_id_key,$site_id); } echo $site_id; ?>&wpVersion=<?php global $wp_version;echo $wp_version ?>&plugChannel=<?php echo get_option("paradigm_site_channel"); ?>' style='width:100%;min-width:1200px;min-height: 800px;height:100%'></iframe>