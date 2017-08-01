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

			// get the jquery ui object
			$queryui = $wp_scripts->query('jquery-ui-core');

			// load the jquery ui theme
			$url = "http://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/ui-lightness/jquery-ui.css";
			wp_enqueue_style('jquery-ui-smoothness', $url, false, null);


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

	    wp_enqueue_script( 'charts_js',  plugins_url('assets/bower/chart.js/dist/Chart.js', dirname(__FILE__) ) );

	    wp_enqueue_script(
				'wpcd_admin_js',
				plugins_url( 'assets/scripts/wp-cloudflare-dashboard.min.js', dirname(__FILE__) ),
				array( 'charts_js'),
			   	false,
				true
			);
    }
	}
}
