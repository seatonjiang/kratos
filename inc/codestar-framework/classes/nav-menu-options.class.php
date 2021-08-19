<?php if (!defined('ABSPATH')) {
  die;
}
/**
 *
 * Nav Menu Options Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (!class_exists('CSF_Nav_Menu_Options')) {
  class CSF_Nav_Menu_Options extends CSF_Abstract
  {

    // constans
    public $unique   = '';
    public $abstract = 'menu';
    public $sections = array();
    public $args     = array(
      'data_type'    => 'serialize',
      'class'        => '',
      'defaults'     => array(),
    );

    // run menu construct
    public function __construct($key, $params)
    {

      $this->unique   = $key;
      $this->args     = apply_filters("csf_{$this->unique}_args", wp_parse_args($params['args'], $this->args), $this);
      $this->sections = apply_filters("csf_{$this->unique}_sections", $params['sections'], $this);

      add_action('wp_nav_menu_item_custom_fields', array($this, 'wp_nav_menu_item_custom_fields'), 10, 4);
      add_action('wp_update_nav_menu_item', array($this, 'wp_update_nav_menu_item'), 10, 3);

      add_filter('wp_edit_nav_menu_walker', array($this, 'wp_edit_nav_menu_walker'), 10, 2);
    }

    // instance
    public static function instance($key, $params)
    {
      return new self($key, $params);
    }

    public function wp_edit_nav_menu_walker($class, $menu_id)
    {

      global $wp_version;

      if (version_compare($wp_version, '5.4.0', '<')) {

        if (!class_exists('CSF_Walker_Nav_Menu_Edit')) {
          CSF::include_plugin_file('functions/walker.php');
        }

        return 'CSF_Walker_Nav_Menu_Edit';
      }

      return $class;
    }

    // get default value
    public function get_default($field)
    {

      $default = (isset($field['default'])) ? $field['default'] : '';
      $default = (isset($this->args['defaults'][$field['id']])) ? $this->args['defaults'][$field['id']] : $default;

      return $default;
    }

    // get meta value
    public function get_meta_value($menu_item_id, $field)
    {

      $value = null;

      if (!empty($menu_item_id) && !empty($field['id'])) {

        if ($this->args['data_type'] !== 'serialize') {
          $meta  = get_post_meta($menu_item_id, $field['id']);
          $value = (isset($meta[0])) ? $meta[0] : null;
        } else {
          $meta  = get_post_meta($menu_item_id, $this->unique, true);
          $value = (isset($meta[$field['id']])) ? $meta[$field['id']] : null;
        }
      }

      $default = (isset($field['id'])) ? $this->get_default($field) : '';
      $value   = (isset($value)) ? $value : $default;

      return $value;
    }

    //
    public function wp_nav_menu_item_custom_fields($menu_item_id, $item, $depth, $args)
    {

      $errors = (!empty($menu_item_id)) ? get_post_meta($menu_item_id, '_csf_errors_' . $this->unique, true) : array();
      $errors = (!empty($errors)) ? $errors : array();
      $class  = ($this->args['class']) ? ' ' . $this->args['class'] : '';

      if (!empty($errors)) {
        delete_post_meta($menu_item_id, '_csf_errors_' . $this->unique);
      }

      echo '<div class="csf csf-nav-menu-options' . esc_attr($class) . '">';

      foreach ($this->sections as $section) {

        $section_icon  = (!empty($section['icon'])) ? '<i class="csf-nav-menu-icon ' . esc_attr($section['icon']) . '"></i>' : '';
        $section_title = (!empty($section['title'])) ? $section['title'] : '';

        echo '<div class="csf-fields">';

        echo ($section_title || $section_icon) ? '<div class="csf-nav-menu-title"><h4>' . $section_icon . $section_title . '</h4></div>' : '';
        echo (!empty($section['description'])) ? '<div class="csf-field csf-section-description">' . $section['description'] . '</div>' : '';

        if (!empty($section['fields'])) {

          foreach ($section['fields'] as $field) {

            if (!empty($field['id']) && !empty($errors['fields'][$field['id']])) {
              $field['_error'] = $errors['fields'][$field['id']];
            }

            if (!empty($field['id'])) {
              $field['default'] = $this->get_default($field);
            }

            CSF::field($field, $this->get_meta_value($menu_item_id, $field), $this->unique . '[' . $menu_item_id . ']', 'menu');
          }
        }

        echo '</div>';
      }

      echo '</div>';
    }

    public function wp_update_nav_menu_item($menu_id, $menu_item_db_id, $menu_item_args)
    {

      $count    = 1;
      $data     = array();
      $errors   = array();
      $noncekey = 'update-nav-menu-nonce';
      $nonce    = (!empty($_POST[$noncekey])) ? sanitize_text_field(wp_unslash($_POST[$noncekey])) : '';

      if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !wp_verify_nonce($nonce, 'update-nav_menu')) {
        return $menu_item_db_id;
      }

      // XSS ok.
      // No worries, This "POST" requests is sanitizing in the below foreach.
      $request = (!empty($_POST[$this->unique][$menu_item_db_id])) ? $_POST[$this->unique][$menu_item_db_id] : array();

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
                    $data[$field_id] = $this->get_meta_value($menu_item_db_id, $field);
                  }
                }
              }
            }
          }

          $count++;
        }
      }

      $data = apply_filters("csf_{$this->unique}_save", $data, $menu_item_db_id, $this);

      do_action("csf_{$this->unique}_save_before", $data, $menu_item_db_id, $this);

      if (empty($data)) {

        if ($this->args['data_type'] !== 'serialize') {
          foreach ($data as $key => $value) {
            delete_post_meta($menu_item_db_id, $key);
          }
        } else {
          delete_post_meta($menu_item_db_id, $this->unique);
        }
      } else {

        if ($this->args['data_type'] !== 'serialize') {
          foreach ($data as $key => $value) {
            update_post_meta($menu_item_db_id, $key, $value);
          }
        } else {
          update_post_meta($menu_item_db_id, $this->unique, $data);
        }

        if (!empty($errors)) {
          update_post_meta($menu_item_db_id, '_csf_errors_' . $this->unique, $errors);
        }
      }

      do_action("csf_{$this->unique}_saved", $data, $menu_item_db_id, $this);

      do_action("csf_{$this->unique}_save_after", $data, $menu_item_db_id, $this);
    }
  }
}
