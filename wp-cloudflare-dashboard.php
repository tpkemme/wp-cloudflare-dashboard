<?php
/**
 * Plugin Name: WP Cloudflare Dashboard
 * Plugin URI:  https://tylerkemme.com
 * Description: A Cloudflare Analytics Dashboard for Wordpress.
 * Version:     0.3.3
 * Author:      Tyler Kemme
 * Author URI:  https://tylerkemme.com
 * Donate link: https://tylerkemme.com
 * License:     MIT
 * Text Domain: wp-cloudflare-dashboard
 * Domain Path: /languages
 *
 * @link https://tylerkemme.com
 *
 * @package WP Cloudflare Dashboard
 * @version 0.3.3
 */

/**
 * Copyright (c) 2017 Tyler Kemme (email : tylerkemme@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp
 */

// User composer autoload.
require 'vendor/autoload.php';

/**
 * Main initiation class
 *
 * @since  0.0.0
 */
final class WP_Cloudflare_Dashboard {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.0.0
	 */
	const VERSION = '0.3.3';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  0.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  0.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  0.0.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages
	 *
	 * @var array
	 * @since  0.0.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin
	 *
	 * @var WP_Cloudflare_Dashboard
	 * @since  0.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of WPCD_Options
	 *
	 * @since0.0.0
	 * @var WPCD_Options
	 */
	protected $options;

	/**
	 * Instance of WPCD_Cloudclient
	 *
	 * @since0.1.0
	 * @var WPCD_Cloudclient
	 */
	protected $cloudclient;

	/**
	 * Instance of WPCD_Assets
	 *
	 * @since0.1.0
	 * @var WPCD_Assets
	 */
	protected $assets;

	/**
	 * Instance of WPCD_Analytics
	 *
	 * @since0.2.0
	 * @var WPCD_Analytics
	 */
	protected $analytics;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.0.0
	 * @return WP_Cloudflare_Dashboard A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  0.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->options = new WPCD_Options( $this );
		$this->cloudclient = new WPCD_Cloudclient( $this );
		$this->assets = new WPCD_Assets( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function hooks() {
		// Priority needs to be:
		// < 10 for CPT_Core,
		// < 5 for Taxonomy_Core,
		// 0 Widgets because widgets_init runs at init priority 1.
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function _deactivate() {}

	/**
	 * Init hooks
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function init() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'wp-cloudflare-dashboard', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.0.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function deactivate_me() {
		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  0.0.0
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').
		// We have met all requirements.
		// Add detailed messages to $this->activation_errors array
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// Compile default message.
		$default_message = sprintf(
			__( 'WP Cloudflare Dashboard is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wp-cloudflare-dashboard' ),
			admin_url( 'plugins.php' )
		);

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( ! empty( $this->activation_errors ) && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo $default_message; ?></p>
			<?php echo $details; ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.0.0
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'options':
			case 'cloudclient':
			case 'assets':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}
}

/**
 * Grab the WP_Cloudflare_Dashboard object and return it.
 * Wrapper for WP_Cloudflare_Dashboard::get_instance()
 *
 * @since  0.0.0
 * @return WP_Cloudflare_Dashboard  Singleton instance of plugin class.
 */
function wpcd() {
	return WP_Cloudflare_Dashboard::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wpcd(), 'hooks' ) );

register_activation_hook( __FILE__, array( wpcd(), '_activate' ) );
register_deactivation_hook( __FILE__, array( wpcd(), '_deactivate' ) );
