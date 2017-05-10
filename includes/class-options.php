<?php
/**
 * WP Cloudflare Dashboard Options
 *
 * @since 0.0.0
 * @package WP Cloudflare Dashboard
 */

 // Require CMB2
 require dirname( __DIR__ ) . '/vendor/cmb2/init.php';

/**
 * WP Cloudflare Dashboard Options class.
 *
 * @since 0.0.0
 */
class WPCD_Options {
	/**
	 * Parent plugin class
	 *
	 * @var    WP_Cloudflare_Dashboard
	 * @since  0.0.0
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug
	 *
	 * @var    string
	 * @since  0.0.0
	 */
	protected $key = 'wp_cloudflare_dashboard_options';

	/**
	 * Options page metabox id
	 *
	 * @var    string
	 * @since  0.0.0
	 */
	protected $options_metabox_id = 'wp_cloudflare_dashboard_options_metabox';

	/**
	 * Options Page title
	 *
	 * @var    string
	 * @since  0.0.0
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Analytics Page hook
	 * @var string
	 */
	protected $analytics_page = '';

	/**
	 * Constructor
	 *
	 * @since  0.0.0
	 * @param  WP_Cloudflare_Dashboard $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		$this->title = __( 'Cloudflare Dashboard', 'wp-cloudflare-dashboard' );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function add_options_page() {
		$this->analytics_page = add_menu_page(
			__( 'Cloudflare Dashboard Analytics', 'wp-cloudflare-dashboard' ),
			__( 'Cloudflare Dashboard', 'wp-cloudflare-dashboard' ),
			'manage_options',
			'wp_cloudflare_dashboard_analytics',
			array( $this->plugin->analytics, 'analytics_page_display' ),
			'dashicons-cloud'
		);
		$this->options_page = add_submenu_page(
			'wp_cloudflare_dashboard_analytics',
			__( 'Cloudflare Dashboard Settings', 'wp-cloudflare-dashboard' ),
			__( 'Settings', 'wp-cloudflare-dashboard' ),
			'manage_options',
			$this->key,
			array( $this, 'options_page_display' )
		);


		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Options page markup. Mostly handled by CMB2
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function options_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<p class="cmb2-metabox-description">To get your API Key, login to your <a target="_blank" href="https://www.cloudflare.com/a/account/my-account">Cloudflare Account</a> and use your Global API Key.</p><br/>
			<?php cmb2_metabox_form( $this->options_metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add custom fields to the options page.
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function add_options_page_metabox() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->options_metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $this->options_metabox_id,
			'hookup'     => false,
			'cmb_styles' => true,
			'show_on'    => array(
				// These are important, don't remove.
				'key'   => 'options-page',
				'value' => array( $this->key ),
			),
		) );

		// Cloudflare Email Address
		$cmb->add_field( array(
			'name'    => __( 'Cloudflare Email Address', 'wpcd' ),
			'desc'    => __( 'Email address associated with your Cloudflare account.', 'wpcd' ),
			'id'      => 'cloudflare_email_address',
			'type'    => 'text_email',
			'default' => __( '', 'wpcd' ),
		) );

		// Cloudflare API Key
		$cmb->add_field( array(
			'name'    => __( 'Cloudflare API Key', 'wpcd' ),
			'desc'    => __( 'Global API Key associated with your Cloudflare account.', 'wpcd' ),
			'id'      => 'cloudflare_api_key',
			'type'    => 'text',
			'default' => __( '', 'wpcd' ),
			'after'   => '<br><input type="button" name="test-cloudflare-creds" value="Test Connection" class="button-secondary"><p class="cmb2-metabox-description">Make sure you save your credentials before testing</p>'
		) );

	}


	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'wpcd' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		throw new Exception( 'Invalid property: ' . $field );
	}
}

/**
 * Helper function to get/return the WPCD_Options object
 * @since  0.1.0
 * @return WPCD_Options object
 */
function wpcd_options() {
	$wpcd = WP_Cloudflare_Dashboard::get_instance();
	return $wpcd->options;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function wpcd_get_option( $key = '', $default = null ) {

	$opt_key = wpcd_options()->key;

	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( $opt_key, $key, $default );
	}

	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( $opt_key, $key, $default );
	$val = $default;

	if( strcmp( gettype( $opts ), 'string') == 0 ){
		$opts = array( $opts );
	}
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}

	return $val;
}

// Get it started
wpcd_options();
