<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Options_Framework {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.7.0
	 * @type string
	 */
	const VERSION = '1.9.1';

	/**
	 * Gets option name
	 *
	 * @since 1.9.0
	 */
	function get_option_name() {

		$name = '';

		// Gets option name as defined in the theme
		if ( function_exists( 'optionsframework_option_name' ) ) {
			$name = optionsframework_option_name();
		}

		// Fallback
		if ( '' == $name ) {
			$name = get_option( 'stylesheet' );
			$name = preg_replace( "/\W/", "_", strtolower( $name ) );
		}

		return apply_filters( 'options_framework_option_name', $name );

	}

	/**
	 * Wrapper for optionsframework_options()
	 *
	 * Allows for manipulating or setting options via 'of_options' filter
	 * For example:
	 *
	 * <code>
	 * add_filter( 'of_options', function( $options ) {
	 *     $options[] = array(
	 *         'name' => 'Input Text Mini',
	 *         'desc' => 'A mini text input field.',
	 *         'id' => 'example_text_mini',
	 *         'std' => 'Default',
	 *         'class' => 'mini',
	 *         'type' => 'text'
	 *     );
	 *
	 *     return $options;
	 * });
	 * </code>
	 *
	 * Also allows for setting options via a return statement in the
	 * options.php file.  For example (in options.php):
	 *
	 * <code>
	 * return array(...);
	 * </code>
	 *
	 * @return array (by reference)
	 */
	static function &_optionsframework_options() {
		static $options = null;

		if ( !$options ) {
	        // Load options from options.php file (if it exists)
	        $location = apply_filters( 'options_framework_location', array( 'theme-options.php' ) );
			$options = kratos_options();
		}

		return $options;
	}

}