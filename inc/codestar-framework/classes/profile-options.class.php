<?php if (!defined('ABSPATH')) {
  die;
}
/**
 *
 * Profile Option Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (!class_exists('CSF_Profile_Options')) {
  class CSF_Profile_Options extends CSF_Abstract
  {

    // constans
    public $unique   = '';
    public $abstract = 'profile';
    public $sections = array();
    public $args     = array(
      'data_type'    => 'serialize',
      'class'        => '',
      'defaults'     => array(),
    );

    // run profile construct
    public function __construct($key, $params)
    {

      $this->unique   = $key;
      $this->args     = apply_filters("csf_{$this->unique}_args", wp_parse_args($params['args'], $this->args), $this);
      $this->sections = apply_filters("csf_{$this->unique}_sections", $params['sections'], $this);

      add_action('admin_init', array($this, 'add_profile_options'));
    }

    // instance
    public static function instance($key, $params)
    {
      return new self($key, $params);
    }

    // add profile add/edit fields
    public function add_profile_options()
    {

      add_action('show_user_profile', array($this, 'render_profile_form_fields'));
      add_action('edit_user_profile', array($this, 'render_profile_form_fields'));

      add_action('personal_options_update', array($this, 'save_profile'));
      add_action('edit_user_profile_update', array($this, 'save_profile'));
    }

    // get default value
    public function get_default($field)
    {

      $default = (isset($field['default'])) ? $field['default'] : '';
      $default = (isset($this->args['defaults'][$field['id']])) ? $this->args['defaults'][$field['id']] : $default;

      return $default;
    }

    // get meta value
    public function get_meta_value($user_id, $field)
    {

      $value = null;

      if (!empty($user_id) && !empty($field['id'])) {

        if ($this->args['data_type'] !== 'serialize') {
          $meta  = get_user_meta($user_id, $field['id']);
          $value = (isset($meta[0])) ? $meta[0] : null;
        } else {
          $meta  = get_user_meta($user_id, $this->unique, true);
          $value = (isset($meta[$field['id']])) ? $meta[$field['id']] : null;
        }
      }

      $default = (isset($field['id'])) ? $this->get_default($field) : '';
      $value   = (isset($value)) ? $value : $default;

      return $value;
    }

    // render profile add/edit form fields
    public function render_profile_form_fields($profileuser)
    {

      $is_profile = (is_object($profileuser) && isset($profileuser->ID)) ? true : false;
      $profile_id = ($is_profile) ? $profileuser->ID : 0;
      $errors     = (!empty($profile_id)) ? get_user_meta($profile_id, '_csf_errors_' . $this->unique, true) : array();
      $errors     = (!empty($errors)) ? $errors : array();
      $class      = ($this->args['class']) ? '' . $this->args['class'] : '';

      if (!empty($errors)) {
        delete_user_meta($profile_id, '_csf_errors_' . $this->unique);
      }

      echo '<div class="csf csf-profile-options csf-onload' . esc_attr($class) . '">';

      wp_nonce_field('csf_profile_nonce', 'csf_profile_nonce' . $this->unique);

      foreach ($this->sections as $section) {

        $section_icon  = (!empty($section['icon'])) ? '<i class="csf-section-icon ' . esc_attr($section['icon']) . '"></i>' : '';
        $section_title = (!empty($section['title'])) ? $section['title'] : '';

        echo ($section_title || $section_icon) ? '<h2>' . $section_icon . $section_title . '</h2>' : '';
        echo (!empty($section['description'])) ? '<div class="csf-field csf-section-description">' . $section['description'] . '</div>' : '';

        if (!empty($section['fields'])) {

          foreach ($section['fields'] as $field) {

            if (!empty($field['id']) && !empty($errors['fields'][$field['id']])) {
              $field['_error'] = $errors['fields'][$field['id']];
            }

            if (!empty($field['id'])) {
              $field['default'] = $this->get_default($field);
            }

            CSF::field($field, $this->get_meta_value($profile_id, $field), $this->unique, 'profile');
          }
        }
      }

      echo '</div>';
    }

    // save profile form fields
    public function save_profile($user_id)
    {

      $count    = 1;
      $data     = array();
      $errors   = array();
      $noncekey = 'csf_profile_nonce' . $this->unique;
      $nonce    = (!empty($_POST[$noncekey])) ? sanitize_text_field(wp_unslash($_POST[$noncekey])) : '';

      if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !wp_verify_nonce($nonce, 'csf_profile_nonce')) {
        return $user_id;
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
                    $data[$field_id] = $this->get_meta_value($user_id, $field);
                  }
                }
              }
            }
          }

          $count++;
        }
      }

      $data = apply_filters("csf_{$this->unique}_save", $data, $user_id, $this);

      do_action("csf_{$this->unique}_save_before", $data, $user_id, $this);

      if (empty($data)) {

        if ($this->args['data_type'] !== 'serialize') {
          foreach ($data as $key => $value) {
            delete_user_meta($user_id, $key);
          }
        } else {
          delete_user_meta($user_id, $this->unique);
        }
      } else {

        if ($this->args['data_type'] !== 'serialize') {
          foreach ($data as $key => $value) {
            update_user_meta($user_id, $key, $value);
          }
        } else {
          update_user_meta($user_id, $this->unique, $data);
        }

        if (!empty($errors)) {
          update_user_meta($user_id, '_csf_errors_' . $this->unique, $errors);
        }
      }

      do_action("csf_{$this->unique}_saved", $data, $user_id, $this);

      do_action("csf_{$this->unique}_save_after", $data, $user_id, $this);
    }
  }
}
