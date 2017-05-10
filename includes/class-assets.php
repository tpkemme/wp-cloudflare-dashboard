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

		// Load admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcd_admin_scripts' ) );

		// Load admin styles
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcd_admin_styles' ) );

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
		if( $hook === 'cloudflare-dashboard_page_wp_cloudflare_dashboard_options' ){

			// Enqueue plugin CSS
			wp_enqueue_style( 'wpcd_admin_css', plugins_url( 'assets/css/wp-cloudflare-dashboard.min.css', dirname(__FILE__) ) );
		}
		if( $hook === 'toplevel_page_wp_cloudflare_dashboard_analytics' ) {

			global $wp_scripts;

			// Enqueue C3 styles
			wp_enqueue_style( 'c3_css', plugins_url( 'assets/bower/c3/c3.min.css', dirname(__FILE__) ) );

			// tell WordPress to load the JQuery UI theme
			wp_enqueue_style('wpcd-admin-ui-css',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/ui-lightness/jquery-ui.css',
                false,
                $this->plugin->version,
                false);

			// Enqueue plugin CSS
			wp_enqueue_style( 'wpcd_admin_css', plugins_url( 'assets/css/wp-cloudflare-dashboard.min.css', dirname(__FILE__) ) );

		}
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
		if( $hook === 'cloudflare-dashboard_page_wp_cloudflare_dashboard_options' ){
			wp_enqueue_script( 'wpcd_admin_js', plugins_url( 'assets/scripts/wp-cloudflare-dashboard.min.js', dirname(__FILE__) ) );
        }
        if( $hook === 'toplevel_page_wp_cloudflare_dashboard_analytics' ) {
			wp_enqueue_script( 'jquery-ui-core' );			// enqueue jQuery UI Core
		    wp_enqueue_script( 'jquery-ui-tabs' );			// enqueue jQuery UI Tabs
			wp_enqueue_script( 'd3_js', plugins_url( 'assets/bower/d3/d3.min.js', dirname(__FILE__) ) );
			wp_enqueue_script( 'c3_js', plugins_url( 'assets/bower/c3/c3.min.js', dirname(__FILE__) ) );
			wp_enqueue_script( 'wpcd_admin_js', plugins_url( 'assets/scripts/wp-cloudflare-dashboard.min.js', dirname(__FILE__) ) );
		}
	}
}
