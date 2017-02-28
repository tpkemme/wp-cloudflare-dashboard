<?php
/**
 * WP Cloudflare Dashboard Assets
 *
 * @since 0.1.0
 * @package WP Cloudflare Dashboard
 */

/**
 * WP Cloudflare Dashboard Assets.
 *
 * @since 0.1.0
 */
class WPCD_Assets {
	/**
	 * Parent plugin class
	 *
	 * @var   WP_Cloudflare_Dashboard
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 * @param  WP_Cloudflare_Dashboard $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {

		// Load admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcd_admin_styles' ) );

		// Load admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcd_admin_scripts' ) );
	}

	/**
	 * Enqueues admin styles
	 *
	 * @since  0.1.0
	 * @param  hook name $hook
	 * @return void
	 */
	public function wpcd_admin_styles( $hook ) {

		// Only load on cloudflare dashboard page
		if( $hook != 'toplevel_page_wp_cloudflare_dashboard_options' ) {
			return;
		}
		wp_enqueue_style( 'wpcd_admin_css', plugins_url( 'assets/css/wp-cloudflare-dashboard.min.css', dirname(__FILE__) ) );
	}

	/**
	 * Enqueues admin scripts
	 *
	 * @since  0.1.0
	 * @param  hook name $hook
	 * @return void
	 */
	public function wpcd_admin_scripts( $hook ) {

		// Only load on cloudflare dashboard page
		if( $hook != 'toplevel_page_wp_cloudflare_dashboard_options' ) {
			return;
		}
		wp_enqueue_script( 'wpcd_admin_js', plugins_url( 'assets/js/wp-cloudflare-dashboard.min.js', dirname(__FILE__) ) );
	}
}
