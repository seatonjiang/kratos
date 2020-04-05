<?php
/**
 * WP Bootstrap Navwalker
 *
 * @package WP-Bootstrap-Navwalker
 *
 * @wordpress-plugin
 * Plugin Name: WP Bootstrap Navwalker
 * Plugin URI:  https://github.com/wp-bootstrap/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 4 navigation style in a custom theme using the WordPress built in menu manager.
 * Author: Edward McIntyre - @twittem, WP Bootstrap, William Patton - @pattonwebz
 * Version: 4.3.0
 * Author URI: https://github.com/wp-bootstrap
 * GitHub Plugin URI: https://github.com/wp-bootstrap/wp-bootstrap-navwalker
 * GitHub Branch: master
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

function register_navmenus()
{
    register_nav_menus(array('header_menu' => __('顶部菜单', 'kratos')));
}
add_action('after_setup_theme', 'register_navmenus');

// 删除原导航标签的多余属性
function attributes_filter($var)
{
    return is_array($var) ? array_intersect($var, array()) : '';
}
add_filter('nav_menu_css_class', 'attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'attributes_filter', 100, 1);
add_filter('page_css_class', 'attributes_filter', 100, 1);

if (!class_exists('WP_Bootstrap_Navwalker')) {
    /**
     * WP_Bootstrap_Navwalker class.
     *
     * @extends Walker_Nav_Menu
     */
    class WP_Bootstrap_Navwalker extends Walker_Nav_Menu
    {

        /**
         * Starts the list before the elements are added.
         *
         * @since WP 3.0.0
         *
         * @see Walker_Nav_Menu::start_lvl()
         *
         * @param string   $output Used to append additional content (passed by reference).
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function start_lvl(&$output, $depth = 0, $args = array())
        {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat($t, $depth);
            // Default class to add to the file.
            $classes = array('dropdown-menu');
            /**
             * Filters the CSS class(es) applied to a menu list element.
             *
             * @since WP 4.8.0
             *
             * @param array    $classes The CSS classes that are applied to the menu `<ul>` element.
             * @param stdClass $args    An object of `wp_nav_menu()` arguments.
             * @param int      $depth   Depth of menu item. Used for padding.
             */
            $class_names = join(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            /*
             * The `.dropdown-menu` container needs to have a labelledby
             * attribute which points to it's trigger link.
             *
             * Form a string for the labelledby attribute from the the latest
             * link with an id that was added to the $output.
             */
            $labelledby = '';
            // Find all links with an id in the output.
            preg_match_all('/(<a.*?id=\"|\')(.*?)\"|\'.*?>/im', $output, $matches);
            // With pointer at end of array check if we got an ID match.
            if (end($matches[2])) {
                // Build a string to use as aria-labelledby.
                $labelledby = 'aria-labelledby="' . esc_attr(end($matches[2])) . '"';
            }
            $output .= "{$n}{$indent}<ul$class_names $labelledby role=\"menu\">{$n}";
        }

        /**
         * Starts the element output.
         *
         * @since WP 3.0.0
         * @since WP 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
         *
         * @see Walker_Nav_Menu::start_el()
         *
         * @param string   $output Used to append additional content (passed by reference).
         * @param WP_Post  $item   Menu item data object.
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         * @param int      $id     Current item ID.
         */
        public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
        {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = ($depth) ? str_repeat($t, $depth) : '';

            $classes = empty($item->classes) ? array() : (array) $item->classes;

            /*
             * Initialize some holder variables to store specially handled item
             * wrappers and icons.
             */
            $linkmod_classes = array();
            $icon_classes = array();

            /*
             * Get an updated $classes array without linkmod or icon classes.
             *
             * NOTE: linkmod and icon class arrays are passed by reference and
             * are maybe modified before being used later in this function.
             */
            $classes = self::separate_linkmods_and_icons_from_classes($classes, $linkmod_classes, $icon_classes, $depth);

            // Join any icon classes plucked from $classes into a string.
            $icon_class_string = join(' ', $icon_classes);

            /**
             * Filters the arguments for a single nav menu item.
             *
             *  WP 4.4.0
             *
             * @param stdClass $args  An object of wp_nav_menu() arguments.
             * @param WP_Post  $item  Menu item data object.
             * @param int      $depth Depth of menu item. Used for padding.
             */
            $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

            // Add .dropdown or .active classes where they are needed.
            if (isset($args->has_children) && $args->has_children) {
                $classes[] = 'dropdown';
            }
            if (in_array('current-menu-item', $classes, true) || in_array('current-menu-parent', $classes, true)) {
                $classes[] = 'active';
            }

            // Add some additional default classes to the item.
            $classes[] = 'menu-item-' . $item->ID;
            $classes[] = 'nav-item';

            // Allow filtering the classes.
            $classes = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth);

            // Form a string of classes in format: class="class_names".
            $class_names = join(' ', $classes);
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            /**
             * Filters the ID applied to a menu item's list item element.
             *
             * @since WP 3.0.1
             * @since WP 4.1.0 The `$depth` parameter was added.
             *
             * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
             * @param WP_Post  $item    The current menu item.
             * @param stdClass $args    An object of wp_nav_menu() arguments.
             * @param int      $depth   Depth of menu item. Used for padding.
             */
            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
            $id = $id ? ' id="' . esc_attr($id) . '"' : '';

            if ($args->has_children) {
                $output .= $indent . '<li' . $id . ' class="nav-item dropdown" ' . '>';
            } else {
                $output .= $indent . '<li' . $id . ' class="nav-item" ' . '>';
            }

            // Initialize array for holding the $atts for the link item.
            $atts = array();

            /*
             * Set title from item to the $atts array - if title is empty then
             * default to item title.
             */
            if (empty($item->attr_title)) {
                $atts['title'] = !empty($item->title) ? strip_tags($item->title) : '';
            } else {
                $atts['title'] = $item->attr_title;
            }

            $atts['target'] = !empty($item->target) ? $item->target : '';
            $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
            // If the item has children, add atts to the <a>.
            if (isset($args->has_children) && $args->has_children && 0 === $depth && $args->depth > 1) {
                $atts['href'] = '#';
                $atts['data-toggle'] = 'dropdown';
                $atts['aria-haspopup'] = 'true';
                $atts['aria-expanded'] = 'false';
                $atts['class'] = 'dropdown-toggle nav-link';
                $atts['id'] = 'menu-item-dropdown-' . $item->ID;
            } else {
                $atts['href'] = !empty($item->url) ? $item->url : '#';
                // For items in dropdowns use .dropdown-item instead of .nav-link.
                if ($depth > 0) {
                    $atts['class'] = 'dropdown-item';
                } else {
                    $atts['class'] = 'nav-link';
                }
            }

            $atts['aria-current'] = $item->current ? 'page' : '';

            // Update atts of this item based on any custom linkmod classes.
            $atts = self::update_atts_for_linkmod_type($atts, $linkmod_classes);
            // Allow filtering of the $atts array before using it.
            $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

            // Build a string of html containing all the atts for the item.
            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            // Set a typeflag to easily test if this is a linkmod or not.
            $linkmod_type = self::get_linkmod_type($linkmod_classes);

            // START appending the internal item contents to the output.
            $item_output = isset($args->before) ? $args->before : '';

            /*
             * This is the start of the internal nav item. Depending on what
             * kind of linkmod we have we may need different wrapper elements.
             */
            if ('' !== $linkmod_type) {
                // Is linkmod, output the required element opener.
                $item_output .= self::linkmod_element_open($linkmod_type, $attributes);
            } else {
                // With no link mod type set this must be a standard <a> tag.
                $item_output .= '<a' . $attributes . '>';
            }

            /*
             * Initiate empty icon var, then if we have a string containing any
             * icon classes form the icon markup with an <i> element. This is
             * output inside of the item before the $title (the link text).
             */
            $icon_html = '';
            if (!empty($icon_class_string)) {
                // Append an <i> with the icon classes to what is output before links.
                $icon_html = '<i class="' . esc_attr($icon_class_string) . '" aria-hidden="true"></i> ';
            }

            /** This filter is documented in wp-includes/post-template.php */
            $title = apply_filters('the_title', $item->title, $item->ID);

            /**
             * Filters a menu item's title.
             *
             * @since WP 4.4.0
             *
             * @param string   $title The menu item's title.
             * @param WP_Post  $item  The current menu item.
             * @param stdClass $args  An object of wp_nav_menu() arguments.
             * @param int      $depth Depth of menu item. Used for padding.
             */
            $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

            // If the .sr-only class was set apply to the nav items text only.
            if (in_array('sr-only', $linkmod_classes, true)) {
                $title = self::wrap_for_screen_reader($title);
                $keys_to_unset = array_keys($linkmod_classes, 'sr-only', true);
                foreach ($keys_to_unset as $k) {
                    unset($linkmod_classes[$k]);
                }
            }

            // Put the item contents into $output.
            $item_output .= isset($args->link_before) ? $args->link_before . $icon_html . $title . $args->link_after : '';

            /*
             * This is the end of the internal nav item. We need to close the
             * correct element depending on the type of link or link mod.
             */
            if ('' !== $linkmod_type) {
                // Is linkmod, output the required closing element.
                $item_output .= self::linkmod_element_close($linkmod_type);
            } else {
                // With no link mod type set this must be a standard <a> tag.
                $item_output .= '</a>';
            }

            $item_output .= isset($args->after) ? $args->after : '';

            // END appending the internal item contents to the output.
            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);

        }

        /**
         * Traverse elements to create list from elements.
         *
         * Display one element if the element doesn't have any children otherwise,
         * display the element and its children. Will only traverse up to the max
         * depth and no ignore elements under that depth. It is possible to set the
         * max depth to include all depths, see walk() method.
         *
         * This method should not be called directly, use the walk() method instead.
         *
         * @since WP 2.5.0
         *
         * @see Walker::start_lvl()
         *
         * @param object $element           Data object.
         * @param array  $children_elements List of elements to continue traversing (passed by reference).
         * @param int    $max_depth         Max depth to traverse.
         * @param int    $depth             Depth of current element.
         * @param array  $args              An array of arguments.
         * @param string $output            Used to append additional content (passed by reference).
         */
        public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
        {
            if (!$element) {
                return;}
            $id_field = $this->db_fields['id'];
            // Display this element.
            if (is_object($args[0])) {
                $args[0]->has_children = !empty($children_elements[$element->$id_field]);}
            parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
        }

        /**
         * Menu Fallback.
         *
         * If this function is assigned to the wp_nav_menu's fallback_cb variable
         * and a menu has not been assigned to the theme location in the WordPress
         * menu manager the function with display nothing to a non-logged in user,
         * and will add a link to the WordPress menu manager if logged in as an admin.
         *
         * @param array $args passed from the wp_nav_menu function.
         */
        public static function fallback($args)
        {
            if (current_user_can('edit_theme_options')) {

                // Get Arguments.
                $container = $args['container'];
                $container_id = $args['container_id'];
                $container_class = $args['container_class'];
                $menu_class = $args['menu_class'];
                $menu_id = $args['menu_id'];

                // Initialize var to store fallback html.
                $fallback_output = '';

                if ($container) {
                    $fallback_output .= '<' . esc_attr($container);
                    if ($container_id) {
                        $fallback_output .= ' id="' . esc_attr($container_id) . '"';
                    }
                    if ($container_class) {
                        $fallback_output .= ' class="' . esc_attr($container_class) . '"';
                    }
                    $fallback_output .= '>';
                }
                $fallback_output .= '<ul';
                if ($menu_id) {
                    $fallback_output .= ' id="' . esc_attr($menu_id) . '"';}
                if ($menu_class) {
                    $fallback_output .= ' class="' . esc_attr($menu_class) . '"';}
                $fallback_output .= '>';
                $fallback_output .= '<li class="nav-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '" class="nav-link">' . esc_attr__('添加导航', 'kratos') . '</a></li>';
                $fallback_output .= '</ul>';
                if ($container) {
                    $fallback_output .= '</' . esc_attr($container) . '>';
                }

                // If $args has 'echo' key and it's true echo, otherwise return.
                if (array_key_exists('echo', $args) && $args['echo']) {
                    echo $fallback_output; // WPCS: XSS OK.
                } else {
                    return $fallback_output;
                }
            }
        }

        /**
         * Find any custom linkmod or icon classes and store in their holder
         * arrays then remove them from the main classes array.
         *
         * Supported linkmods: .disabled, .dropdown-header, .dropdown-divider, .sr-only
         * Supported iconsets: Font Awesome 4/5, Glypicons
         *
         * NOTE: This accepts the linkmod and icon arrays by reference.
         *
         * @since 4.0.0
         *
         * @param array   $classes         an array of classes currently assigned to the item.
         * @param array   $linkmod_classes an array to hold linkmod classes.
         * @param array   $icon_classes    an array to hold icon classes.
         * @param integer $depth           an integer holding current depth level.
         *
         * @return array  $classes         a maybe modified array of classnames.
         */
        private function separate_linkmods_and_icons_from_classes($classes, &$linkmod_classes, &$icon_classes, $depth)
        {
            // Loop through $classes array to find linkmod or icon classes.
            foreach ($classes as $key => $class) {
                /*
                 * If any special classes are found, store the class in it's
                 * holder array and and unset the item from $classes.
                 */
                if (preg_match('/^disabled|^sr-only/i', $class)) {
                    // Test for .disabled or .sr-only classes.
                    $linkmod_classes[] = $class;
                    unset($classes[$key]);
                } elseif (preg_match('/^dropdown-header|^dropdown-divider|^dropdown-item-text/i', $class) && $depth > 0) {
                    /*
                     * Test for .dropdown-header or .dropdown-divider and a
                     * depth greater than 0 - IE inside a dropdown.
                     */
                    $linkmod_classes[] = $class;
                    unset($classes[$key]);
                } elseif (preg_match('/^fa-(\S*)?|^fa(s|r|l|b)?(\s?)?$/i', $class)) {
                    // Font Awesome.
                    $icon_classes[] = $class;
                    unset($classes[$key]);
                } elseif (preg_match('/^glyphicon-(\S*)?|^glyphicon(\s?)$/i', $class)) {
                    // Glyphicons.
                    $icon_classes[] = $class;
                    unset($classes[$key]);
                }
            }

            return $classes;
        }

        /**
         * Return a string containing a linkmod type and update $atts array
         * accordingly depending on the decided.
         *
         * @since 4.0.0
         *
         * @param array $linkmod_classes array of any link modifier classes.
         *
         * @return string                empty for default, a linkmod type string otherwise.
         */
        private function get_linkmod_type($linkmod_classes = array())
        {
            $linkmod_type = '';
            // Loop through array of linkmod classes to handle their $atts.
            if (!empty($linkmod_classes)) {
                foreach ($linkmod_classes as $link_class) {
                    if (!empty($link_class)) {

                        // Check for special class types and set a flag for them.
                        if ('dropdown-header' === $link_class) {
                            $linkmod_type = 'dropdown-header';
                        } elseif ('dropdown-divider' === $link_class) {
                            $linkmod_type = 'dropdown-divider';
                        } elseif ('dropdown-item-text' === $link_class) {
                            $linkmod_type = 'dropdown-item-text';
                        }
                    }
                }
            }
            return $linkmod_type;
        }

        /**
         * Update the attributes of a nav item depending on the limkmod classes.
         *
         * @since 4.0.0
         *
         * @param array $atts            array of atts for the current link in nav item.
         * @param array $linkmod_classes an array of classes that modify link or nav item behaviors or displays.
         *
         * @return array                 maybe updated array of attributes for item.
         */
        private function update_atts_for_linkmod_type($atts = array(), $linkmod_classes = array())
        {
            if (!empty($linkmod_classes)) {
                foreach ($linkmod_classes as $link_class) {
                    if (!empty($link_class)) {
                        /*
                         * Update $atts with a space and the extra classname
                         * so long as it's not a sr-only class.
                         */
                        if ('sr-only' !== $link_class) {
                            $atts['class'] .= ' ' . esc_attr($link_class);
                        }
                        // Check for special class types we need additional handling for.
                        if ('disabled' === $link_class) {
                            // Convert link to '#' and unset open targets.
                            $atts['href'] = '#';
                            unset($atts['target']);
                        } elseif ('dropdown-header' === $link_class || 'dropdown-divider' === $link_class || 'dropdown-item-text' === $link_class) {
                            // Store a type flag and unset href and target.
                            unset($atts['href']);
                            unset($atts['target']);
                        }
                    }
                }
            }
            return $atts;
        }

        /**
         * Wraps the passed text in a screen reader only class.
         *
         * @since 4.0.0
         *
         * @param string $text the string of text to be wrapped in a screen reader class.
         * @return string      the string wrapped in a span with the class.
         */
        private function wrap_for_screen_reader($text = '')
        {
            if ($text) {
                $text = '<span class="sr-only">' . $text . '</span>';
            }
            return $text;
        }

        /**
         * Returns the correct opening element and attributes for a linkmod.
         *
         * @since 4.0.0
         *
         * @param string $linkmod_type a sting containing a linkmod type flag.
         * @param string $attributes   a string of attributes to add to the element.
         *
         * @return string              a string with the openign tag for the element with attribibutes added.
         */
        private function linkmod_element_open($linkmod_type, $attributes = '')
        {
            $output = '';
            if ('dropdown-item-text' === $linkmod_type) {
                $output .= '<span class="dropdown-item-text"' . $attributes . '>';
            } elseif ('dropdown-header' === $linkmod_type) {
                /*
                 * For a header use a span with the .h6 class instead of a real
                 * header tag so that it doesn't confuse screen readers.
                 */
                $output .= '<span class="dropdown-header h6"' . $attributes . '>';
            } elseif ('dropdown-divider' === $linkmod_type) {
                // This is a divider.
                $output .= '<div class="dropdown-divider"' . $attributes . '>';
            }
            return $output;
        }

        /**
         * Return the correct closing tag for the linkmod element.
         *
         * @since 4.0.0
         *
         * @param string $linkmod_type a string containing a special linkmod type.
         *
         * @return string              a string with the closing tag for this linkmod type.
         */
        private function linkmod_element_close($linkmod_type)
        {
            $output = '';
            if ('dropdown-header' === $linkmod_type || 'dropdown-item-text' === $linkmod_type) {
                /*
                 * For a header use a span with the .h6 class instead of a real
                 * header tag so that it doesn't confuse screen readers.
                 */
                $output .= '</span>';
            } elseif ('dropdown-divider' === $linkmod_type) {
                // This is a divider.
                $output .= '</div>';
            }
            return $output;
        }
    }
}
