<?php if (!defined('ABSPATH')) {
  die;
}
/**
 *
 * Metabox Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (!class_exists('CSF_Metabox')) {
  class CSF_Metabox extends CSF_Abstract
  {

    // constans
    public $unique         = '';
    public $abstract       = 'metabox';
    public $pre_fields     = array();
    public $sections       = array();
    public $post_type      = array();
    public $args           = array(
      'title'              => '',
      'post_type'          => 'post',
      'data_type'          => 'serialize',
      'context'            => 'advanced',
      'priority'           => 'default',
      'exclude_post_types' => array(),
      'page_templates'     => '',
      'post_formats'       => '',
      'show_reset'         => false,
      'show_restore'       => false,
      'enqueue_webfont'    => true,
      'async_webfont'      => false,
      'output_css'         => true,
      'nav'                => 'normal',
      'theme'              => 'dark',
      'class'              => '',
      'defaults'           => array(),
    );

    // run metabox construct
    public function __construct($key, $params = array())
    {

      $this->unique         = $key;
      $this->args           = apply_filters("csf_{$this->unique}_args", wp_parse_args($params['args'], $this->args), $this);
      $this->sections       = apply_filters("csf_{$this->unique}_sections", $params['sections'], $this);
      $this->post_type      = (is_array($this->args['post_type'])) ? $this->args['post_type'] : array_filter((array) $this->args['post_type']);
      $this->post_formats   = (is_array($this->args['post_formats'])) ? $this->args['post_formats'] : array_filter((array) $this->args['post_formats']);
      $this->page_templates = (is_array($this->args['page_templates'])) ? $this->args['page_templates'] : array_filter((array) $this->args['page_templates']);
      $this->pre_fields     = $this->pre_fields($this->sections);

      add_action('add_meta_boxes', array($this, 'add_meta_box'));
      add_action('save_post', array($this, 'save_meta_box'));
      add_action('edit_attachment', array($this, 'save_meta_box'));

      if (!empty($this->page_templates) || !empty($this->post_formats) || !empty($this->args['class'])) {
        foreach ($this->post_type as $post_type) {
          add_filter('postbox_classes_' . $post_type . '_' . $this->unique, array($this, 'add_metabox_classes'));
        }
      }

      // wp enqeueu for typography and output css
      parent::__construct();
    }

    // instance
    public static function instance($key, $params = array())
    {
      return new self($key, $params);
    }

    public function pre_fields($sections)
    {

      $result  = array();

      foreach ($sections as $key => $section) {
        if (!empty($section['fields'])) {
          foreach ($section['fields'] as $field) {
            $result[] = $field;
          }
        }
      }

      return $result;
    }

    public function add_metabox_classes($classes)
    {

      global $post;

      if (!empty($this->post_formats)) {

        $saved_post_format = (is_object($post)) ? get_post_format($post) : false;
        $saved_post_format = (!empty($saved_post_format)) ? $saved_post_format : 'default';

        $classes[] = 'csf-post-formats';

        // Sanitize post format for standard to default
        if (($key = array_search('standard', $this->post_formats)) !== false) {
          $this->post_formats[$key] = 'default';
        }

        foreach ($this->post_formats as $format) {
          $classes[] = 'csf-post-format-' . $format;
        }

        if (!in_array($saved_post_format, $this->post_formats)) {
          $classes[] = 'csf-metabox-hide';
        } else {
          $classes[] = 'csf-metabox-show';
        }
      }

      if (!empty($this->page_templates)) {

        $saved_template = (is_object($post) && !empty($post->page_template)) ? $post->page_template : 'default';

        $classes[] = 'csf-page-templates';

        foreach ($this->page_templates as $template) {
          $classes[] = 'csf-page-' . preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($template));
        }

        if (!in_array($saved_template, $this->page_templates)) {
          $classes[] = 'csf-metabox-hide';
        } else {
          $classes[] = 'csf-metabox-show';
        }
      }

      if (!empty($this->args['class'])) {
        $classes[] = $this->args['class'];
      }

      return $classes;
    }

    // add metabox
    public function add_meta_box($post_type)
    {

      if (!in_array($post_type, $this->args['exclude_post_types'])) {
        add_meta_box($this->unique, $this->args['title'], array($this, 'add_meta_box_content'), $this->post_type, $this->args['context'], $this->args['priority'], $this->args);
      }
    }

    // get default value
    public function get_default($field)
    {

      $default = (isset($field['default'])) ? $field['default'] : '';
      $default = (isset($this->args['defaults'][$field['id']])) ? $this->args['defaults'][$field['id']] : $default;

      return $default;
    }

    // get meta value
    public function get_meta_value($field)
    {

      global $post;

      $value = null;

      if (is_object($post) && !empty($field['id'])) {

        if ($this->args['data_type'] !== 'serialize') {
          $meta  = get_post_meta($post->ID, $field['id']);
          $value = (isset($meta[0])) ? $meta[0] : null;
        } else {
          $meta  = get_post_meta($post->ID, $this->unique, true);
          $value = (isset($meta[$field['id']])) ? $meta[$field['id']] : null;
        }
      }

      $default = (isset($field['id'])) ? $this->get_default($field) : '';
      $value   = (isset($value)) ? $value : $default;

      return $value;
    }

    // add metabox content
    public function add_meta_box_content($post, $callback)
    {

      global $post;

      $has_nav   = (count($this->sections) > 1 && $this->args['context'] !== 'side') ? true : false;
      $show_all  = (!$has_nav) ? ' csf-show-all' : '';
      $post_type = (is_object($post)) ? $post->post_type : '';
      $errors    = (is_object($post)) ? get_post_meta($post->ID, '_csf_errors_' . $this->unique, true) : array();
      $errors    = (!empty($errors)) ? $errors : array();
      $theme     = ($this->args['theme']) ? ' csf-theme-' . $this->args['theme'] : '';
      $nav_type  = ($this->args['nav'] === 'inline') ? 'inline' : 'normal';

      if (is_object($post) && !empty($errors)) {
        delete_post_meta($post->ID, '_csf_errors_' . $this->unique);
      }

      wp_nonce_field('csf_metabox_nonce', 'csf_metabox_nonce' . $this->unique);

      echo '<div class="csf csf-metabox' . esc_attr($theme) . '">';

      echo '<div class="csf-wrapper' . esc_attr($show_all) . '">';

      if ($has_nav) {

        echo '<div class="csf-nav csf-nav-' . esc_attr($nav_type) . ' csf-nav-metabox">';

        echo '<ul>';

        $tab_key = 0;

        foreach ($this->sections as $section) {

          if (!empty($section['post_type']) && !in_array($post_type, array_filter((array) $section['post_type']))) {
            continue;
          }

          $tab_error = (!empty($errors['sections'][$tab_key])) ? '<i class="csf-label-error csf-error">!</i>' : '';
          $tab_icon  = (!empty($section['icon'])) ? '<i class="csf-tab-icon ' . esc_attr($section['icon']) . '"></i>' : '';

          echo '<li><a href="#">' . $tab_icon . $section['title'] . $tab_error . '</a></li>';

          $tab_key++;
        }

        echo '</ul>';

        echo '</div>';
      }

      echo '<div class="csf-content">';

      echo '<div class="csf-sections">';

      $section_key = 0;

      foreach ($this->sections as $section) {

        if (!empty($section['post_type']) && !in_array($post_type, array_filter((array) $section['post_type']))) {
          continue;
        }

        $section_onload = (!$has_nav) ? ' csf-onload' : '';
        $section_class  = (!empty($section['class'])) ? ' ' . $section['class'] : '';
        $section_title  = (!empty($section['title'])) ? $section['title'] : '';
        $section_icon   = (!empty($section['icon'])) ? '<i class="csf-section-icon ' . esc_attr($section['icon']) . '"></i>' : '';

        echo '<div class="csf-section hidden' . esc_attr($section_onload . $section_class) . '">';

        echo ($section_title || $section_icon) ? '<div class="csf-section-title"><h3>' . $section_icon . $section_title . '</h3></div>' : '';
        echo (!empty($section['description'])) ? '<div class="csf-field csf-section-description">' . $section['description'] . '</div>' : '';

        if (!empty($section['fields'])) {

          foreach ($section['fields'] as $field) {

            if (!empty($field['id']) && !empty($errors['fields'][$field['id']])) {
              $field['_error'] = $errors['fields'][$field['id']];
            }

            if (!empty($field['id'])) {
              $field['default'] = $this->get_default($field);
            }

            CSF::field($field, $this->get_meta_value($field), $this->unique, 'metabox');
          }
        } else {

          echo '<div class="csf-no-option">' . esc_html__('No data available.', 'csf') . '</div>';
        }

        echo '</div>';

        $section_key++;
      }

      echo '</div>';

      if (!empty($this->args['show_restore']) || !empty($this->args['show_reset'])) {

        echo '<div class="csf-sections-reset">';
        echo '<label>';
        echo '<input type="checkbox" name="' . esc_attr($this->unique) . '[_reset]" />';
        echo '<span class="button csf-button-reset">' . esc_html__('Reset', 'csf') . '</span>';
        echo '<span class="button csf-button-cancel">' . sprintf('<small>( %s )</small> %s', esc_html__('update post', 'csf'), esc_html__('Cancel', 'csf')) . '</span>';
        echo '</label>';
        echo '</div>';
      }

      echo '</div>';

      echo ($has_nav && $nav_type === 'normal') ? '<div class="csf-nav-background"></div>' : '';

      echo '<div class="clear"></div>';

      echo '</div>';

      echo '</div>';
    }

    // save metabox
    public function save_meta_box($post_id)
    {

      $count    = 1;
      $data     = array();
      $errors   = array();
      $noncekey = 'csf_metabox_nonce' . $this->unique;
      $nonce    = (!empty($_POST[$noncekey])) ? sanitize_text_field(wp_unslash($_POST[$noncekey])) : '';

      if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !wp_verify_nonce($nonce, 'csf_metabox_nonce')) {
        return $post_id;
      }

      // XSS ok.
      // No worries, This "POST" requests is sanitizing in the below foreach.
      $request = (!empty($_POST[$this->unique])) ? $_POST[$this->unique] : array();

      if (!empty($request)) {

        foreach ($this->sections as $section) {

          if (!empty($section['fields'])) {

            foreach ($section['fields'] as $field) {

              if (!empty($field['id'])) {

                $field_id    = $field['id'];
                $field_value = isset($request[$field_id]) ? $request[$field_id] : '';

                // Sanitize "post" request of field.
                if (!isset($field['sanitize'])) {

                  if (is_array($field_value)) {
                    $data[$field_id] = wp_kses_post_deep($field_value);
                  } else {
                    $data[$field_id] = wp_kses_post($field_value);
                  }
                } else if (isset($field['sanitize']) && is_callable($field['sanitize'])) {

                  $data[$field_id] = call_user_func($field['sanitize'], $field_value);
                } else {

                  $data[$field_id] = $field_value;
                }

                // Validate "post" request of field.
                if (isset($field['validate']) && is_callable($field['validate'])) {

                  $has_validated = call_user_func($field['validate'], $field_value);

                  if (!empty($has_validated)) {

                    $errors['sections'][$count] = true;
                    $errors['fields'][$field_id] = $has_validated;
                    $data[$field_id] = $this->get_meta_value($field);
                  }
                }
              }
            }
          }

          $count++;
        }
      }

      $data = apply_filters("csf_{$this->unique}_save", $data, $post_id, $this);

      do_action("csf_{$this->unique}_save_before", $data, $post_id, $this);

      if (empty($data) || !empty($request['_reset'])) {

        if ($this->args['data_type'] !== 'serialize') {
          foreach ($data as $key => $value) {
            delete_post_meta($post_id, $key);
          }
        } else {
          delete_post_meta($post_id, $this->unique);
        }
      } else {

        if ($this->args['data_type'] !== 'serialize') {
          foreach ($data as $key => $value) {
            update_post_meta($post_id, $key, $value);
          }
        } else {
          update_post_meta($post_id, $this->unique, $data);
        }

        if (!empty($errors)) {
          update_post_meta($post_id, '_csf_errors_' . $this->unique, $errors);
        }
      }

      do_action("csf_{$this->unique}_saved", $data, $post_id, $this);

      do_action("csf_{$this->unique}_save_after", $data, $post_id, $this);
    }
  }
}
