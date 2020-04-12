<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Options_Framework_Interface
{

    /**
     * Generates the tabs that are used in the options menu
     */
    public static function optionsframework_tabs()
    {
        $counter = 0;
        $options = &Options_Framework::_optionsframework_options();
        $menu = '';

        foreach ($options as $value) {
            // Heading for Navigation
            if ($value['type'] == "heading") {
                $counter++;
                $class = '';
                $class = !empty($value['id']) ? $value['id'] : $value['name'];
                $class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class)) . '-tab';
                $menu .= '<a id="options-group-' . $counter . '-tab" class="nav-tab ' . $class . '" title="' . esc_attr($value['name']) . '" href="' . esc_attr('#options-group-' . $counter) . '">' . esc_html($value['name']) . '</a>';
            }
        }

        return $menu;
    }

    /**
     * Generates the options fields that are used in the form.
     */
    public static function optionsframework_fields()
    {

        global $allowedtags;

        $options_framework = new Options_Framework;
        $option_name = $options_framework->get_option_name();
        $settings = get_option($option_name);
        $options = &Options_Framework::_optionsframework_options();

        $counter = 0;
        $menu = '';

        foreach ($options as $value) {

            $val = '';
            $select_value = '';
            $output = '';

            // Wrap all options
            if (($value['type'] != "heading") && ($value['type'] != "info") && ($value['type'] != "about")) {

                // Keep all ids lowercase with no spaces
                $value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']));

                $id = 'section-' . $value['id'];

                $class = 'section';
                if (isset($value['type'])) {
                    $class .= ' section-' . $value['type'];
                }
                if (isset($value['class'])) {
                    $class .= ' ' . $value['class'];
                }

                $output .= '<div id="' . esc_attr($id) . '" class="' . esc_attr($class) . '">' . "\n";
                if (isset($value['name'])) {
                    $output .= '<h4 class="heading">' . esc_html($value['name']) . '</h4>' . "\n";
                }
                if ($value['type'] != 'editor') {
                    $output .= '<div class="option">' . "\n" . '<div class="controls">' . "\n";
                } else {
                    $output .= '<div class="option">' . "\n" . '<div>' . "\n";
                }
            }

            // Set default value to $val
            if (isset($value['std'])) {
                $val = $value['std'];
            }

            // If the option is already saved, override $val
            if (($value['type'] != 'heading') && ($value['type'] != 'info') && ($value['type'] != "about")) {
                if (isset($settings[($value['id'])])) {
                    $val = $settings[($value['id'])];
                    // Striping slashes of non-array options
                    if (!is_array($val)) {
                        $val = stripslashes($val);
                    }
                }
            }

            // If there is a description save it for labels
            $explain_value = '';
            if (isset($value['desc'])) {
                $explain_value = $value['desc'];
            }

            // Set the placeholder if one exists
            $placeholder = '';
            if (isset($value['placeholder'])) {
                $placeholder = ' placeholder="' . esc_attr($value['placeholder']) . '"';
            }

            if (has_filter('optionsframework_' . $value['type'])) {
                $output .= apply_filters('optionsframework_' . $value['type'], $option_name, $value, $val);
            }

            switch ($value['type']) {

                // Basic text input
                case 'text':
                    $output .= '<input id="' . esc_attr($value['id']) . '" class="of-input" name="' . esc_attr($option_name . '[' . $value['id'] . ']') . '" type="text" value="' . esc_attr($val) . '"' . $placeholder . ' />';
                    break;

                // Password input
                case 'password':
                    $output .= '<input id="' . esc_attr($value['id']) . '" class="of-input" name="' . esc_attr($option_name . '[' . $value['id'] . ']') . '" type="password" value="' . esc_attr($val) . '" />';
                    break;

                // Textarea
                case 'textarea':
                    $rows = '8';

                    if (isset($value['settings']['rows'])) {
                        $custom_rows = $value['settings']['rows'];
                        if (is_numeric($custom_rows)) {
                            $rows = $custom_rows;
                        }
                    }

                    $val = stripslashes($val);
                    $output .= '<textarea id="' . esc_attr($value['id']) . '" class="of-input" name="' . esc_attr($option_name . '[' . $value['id'] . ']') . '" rows="' . $rows . '"' . $placeholder . '>' . esc_textarea($val) . '</textarea>';
                    break;

                // Select Box
                case 'select':
                    $output .= '<select class="of-input" name="' . esc_attr($option_name . '[' . $value['id'] . ']') . '" id="' . esc_attr($value['id']) . '">';

                    foreach ($value['options'] as $key => $option) {
                        $output .= '<option' . selected($val, $key, false) . ' value="' . esc_attr($key) . '">' . esc_html($option) . '</option>';
                    }
                    $output .= '</select>';
                    break;

                // Radio Box
                case "radio":
                    $name = $option_name . '[' . $value['id'] . ']';
                    foreach ($value['options'] as $key => $option) {
                        $id = $option_name . '-' . $value['id'] . '-' . $key;
                        $output .= '<input class="of-input of-radio" type="radio" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" value="' . esc_attr($key) . '" ' . checked($val, $key, false) . ' /><label for="' . esc_attr($id) . '">' . esc_html($option) . '</label>';
                    }
                    break;

                // Image Selectors
                case "images":
                    $name = $option_name . '[' . $value['id'] . ']';
                    foreach ($value['options'] as $key => $option) {
                        $selected = '';
                        if ($val != '' && ($val == $key)) {
                            $selected = ' of-radio-img-selected';
                        }
                        $output .= '<input type="radio" id="' . esc_attr($value['id'] . '_' . $key) . '" class="of-radio-img-radio" value="' . esc_attr($key) . '" name="' . esc_attr($name) . '" ' . checked($val, $key, false) . ' />';
                        $output .= '<div class="of-radio-img-label">' . esc_html($key) . '</div>';
                        $output .= '<img src="' . esc_url($option) . '" alt="' . $option . '" class="of-radio-img-img' . $selected . '" onclick="document.getElementById(\'' . esc_attr($value['id'] . '_' . $key) . '\').checked=true;" />';
                    }
                    break;

                // Checkbox
                case "checkbox":
                    $output .= '<input id="' . esc_attr($value['id']) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr($option_name . '[' . $value['id'] . ']') . '" ' . checked($val, 1, false) . ' />';
                    $output .= '<label class="explain" for="' . esc_attr($value['id']) . '">' . wp_kses($explain_value, $allowedtags) . '</label>';
                    break;

                // Color picker
                case "color":
                    $default_color = '';
                    if (isset($value['std'])) {
                        if ($val != $value['std']) {
                            $default_color = ' data-default-color="' . $value['std'] . '" ';
                        }

                    }
                    $output .= '<input name="' . esc_attr($option_name . '[' . $value['id'] . ']') . '" id="' . esc_attr($value['id']) . '" class="of-color"  type="text" value="' . esc_attr($val) . '"' . $default_color . ' />';

                    break;

                // Uploader
                case "upload":
                    $output .= Options_Framework_Media_Uploader::optionsframework_uploader($value['id'], $val, null);

                    break;

                case "info":
                    $id = '';
                    $class = 'section';
                    if (isset($value['id'])) {
                        $id = 'id="' . esc_attr($value['id']) . '" ';
                    }
                    if (isset($value['type'])) {
                        $class .= ' section-' . $value['type'];
                    }
                    if (isset($value['class'])) {
                        $class .= ' ' . $value['class'];
                    }

                    $output .= '<div ' . $id . 'class="' . esc_attr($class) . '">' . "\n";
                    if (isset($value['name'])) {
                        $output .= '<h4 class="heading">' . esc_html($value['name']) . '</h4>' . "\n";
                    }
                    if (isset($value['desc'])) {
                        $output .= $value['desc'] . "\n";
                    }
                    $output .= '</div>' . "\n";
                    break;

                case "about":
                    global $wp_version;
                    $version = $wp_version;
                    $output .= '<div class="about-content">
					<img src="' . ASSET_PATH . '/inc/options-framework/images/about.png">
					<h4>' . __('基础信息', 'kratos') . '</h4>
					<ul>
						<li>' . __('PHP 版本：', 'kratos') . PHP_VERSION . '</li>
						<li>' . __('Kratos 版本：', 'kratos') . THEME_VERSION . '</li>
						<li>' . __('WordPress 版本：', 'kratos') . $version . '</li>
						<li>' . __('User Agent 信息：', 'kratos') . $_SERVER['HTTP_USER_AGENT'] . '</li>
					</ul>
					<p class="notices">' . __('提示：在提交主题相关问题反馈时，请将上面「基础信息」中的内容复制到环境信息中。', 'kratos') . '</p>
					<h4>' . __('资料文档', 'kratos') . '</h4>
					<ul>
						<li>' . __('说明文档：', 'kratos') . '<a href="https://www.vtrois.com/" target="_blank">https://www.vtrois.com/</a></li>
						<li>' . __('代码托管：', 'kratos') . '<a href="https://github.com/vtrois/kratos" target="_blank">https://github.com/vtrois/kratos</a></li>
						<li>' . __('问题反馈：', 'kratos') . '<a href="https://github.com/vtrois/kratos/issues" target="_blank">https://github.com/vtrois/kratos/issues</a></li>
						<li>' . __('更新日志：', 'kratos') . '<a href="https://github.com/vtrois/kratos/releases" target="_blank">https://github.com/vtrois/kratos/releases</a></li>
					</ul>
					<h4>' . __('讨论交流', 'kratos') . '</h4>
					<p>' . __('欢迎使用 Kratos 主题开始文章创作，诚邀您加入主题交流 QQ 群：<a href="https://shang.qq.com/wpa/qunwpa?idkey=18a1de727037e3e8b9b49bfc7a410139e5db736106ef07292f07a62ff5eef9a2" target="_blank">734508</a>', 'kratos') . '</p>
					<h4>' . __('版权声明', 'kratos') . '</h4>
					<p>' . __('主题源码使用 <a href="https://github.com/Vtrois/Kratos/blob/master/LICENSE" target="_blank">MIT 协议</a> 进行许可，说明文档使用 <a href="https://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank">CC BY-NC-ND 4.0</a> 进行许可。', 'kratos') . '</p>
					<h4>' . __('打赏支持', 'kratos') . '</h4>
					<img src="' . ASSET_PATH . '/inc/options-framework/images/donate.png">
                    <p class="tips">' . __('项目的发展需要您的支持和鼓励，打赏时请确认作者姓名为<b>姜学栋</b>', 'kratos') . '</p>
                    <h4>' . __('赞助商', 'kratos') . '</h4>
                    <a href="https://www.maoyuncloud.com/" target="_blank"><img class="maocloud" src="' . ASSET_PATH . '/inc/options-framework/images/maocloud.png"></a>
				    </div>';
                    break;

                case "sendmail":
                    $output .= '<input type="submit" name="sendmail" class="button-secondary" value="' . __('测试邮件', 'kratos') . '">';
                    break;

                // Heading for Navigation
                case "heading":
                    $counter++;
                    if ($counter >= 2) {
                        $output .= '</div>' . "\n";
                    }
                    $class = '';
                    $class = !empty($value['id']) ? $value['id'] : $value['name'];
                    $class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class));
                    $output .= '<div id="options-group-' . $counter . '" class="group ' . $class . '">';
                    $output .= '<h3>' . esc_html($value['name']) . '</h3>' . "\n";
                    break;
            }

            if (($value['type'] != "heading") && ($value['type'] != "info") && ($value['type'] != "about")) {
                $output .= '</div>';
                if (($value['type'] != "checkbox") && ($value['type'] != "editor")) {
                    $output .= '<div class="explain">' . wp_kses($explain_value, $allowedtags) . '</div>' . "\n";
                }
                $output .= '</div></div>' . "\n";
            }

            echo $output;
        }

        // Outputs closing div if there tabs
        if (Options_Framework_Interface::optionsframework_tabs() != '') {
            echo '</div>';
        }
    }

}
