<?php
/**
 * WP Cloudflare Dashboard Cloudclient
 *
 * @since 0.1.0
 * @package WP Cloudflare Dashboard
 */

/**
 * WP Cloudflare Dashboard Cloudclient.
 *
 * @since 0.1.0
 */
class WPCD_Cloudclient {

	/**
	 * Parent plugin class
	 *
	 * @var   WP_Cloudflare_Dashboard
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Cloudflare Email Address
	 *
	 * @var   WP_Cloudflare_Dashboard
	 * @since 0.1.0
	 */
	protected $cloudflare_email_address = null;

	/**
	 * Cloudflare API Key
	 *
	 * @var   WP_Cloudflare_Dashboard
	 * @since 0.1.0
	 */
	protected $cloudflare_api_key = null;

	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 * @param  WP_Cloudflare_Dashboard $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->cloudflare_email_address = wpcd_get_option( 'cloudflare_email_address' );
		$this->cloudflare_api_key 		= wpcd_get_option( 'cloudflare_api_key' );

		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {

		add_action( 'wp_ajax_test_cloudflare_creds', array( $this, 'test_cloudflare_creds' ) );
	}

	/**
	 * Test provided cloudflare credentials
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function test_cloudflare_creds() {
		
		$client = new GuzzleHttp\Client();

		try{

			$res = $client->request('GET', 'https://api.cloudflare.com/client/v4/zones', [
				'headers' => [
		        	'X-Auth-Key' 	=> $this->cloudflare_api_key,
			        'X-Auth-Email'  => $this->cloudflare_email_address
			    ]
			]);

			echo $res->getStatusCode();
			wp_die();

		} catch ( Exception $e ) {

			echo $e;
			wp_die();
		}

	}
}
