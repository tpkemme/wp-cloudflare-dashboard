<?php
/**
 * WP Cloudflare Dashboard Options
 *
 * @since 0.0.0
 * @package WP Cloudflare Dashboard
 */



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
	protected $metabox_id = 'wp_cloudflare_dashboard_options_metabox';

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
		$this->options_page = add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' ),
			'dashicons-cloud'
		);

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since  0.0.0
	 * @return void
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
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

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
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
			'desc'    => __( 'API Key associated with your Cloudflare account.', 'wpcd' ),
			'id'      => 'cloudflare_api_key',
			'type'    => 'text',
			'default' => __( '', 'wpcd' ),
		) );

	}
}
