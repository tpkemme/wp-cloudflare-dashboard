<?php
/**
 * WP Cloudflare Dashboard Cloudclient
 *
 * This class handles all api calls to Cloudflare
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

        if( empty( $this->cloudflare_api_key ) || empty( $this->cloudflare_email_address ) ){
            return;
        }

        $response = wp_remote_get( 'https://api.cloudflare.com/client/v4/zones', array(
			'headers' => array(
	        	'X-Auth-Key' 	=> $this->cloudflare_api_key,
		        'X-Auth-Email'  => $this->cloudflare_email_address
		    )
		));

        if( is_wp_error($response) ){
            echo $response->get_error_message();
        }
        else if( wp_remote_retrieve_response_code($response) === 200 ){
            echo 200;
        }
        else{
            echo wp_remote_retrieve_response_code() . ': ' . wp_remote_retrieve_response_message();
        }
		wp_die();
	}

	/**
	 * Get Zones
	 *
	 * @since  0.1.0
	 * @return $zones array of zones for current user ['id', 'name','status']
	 */
	public function wpcd_get_zones() {

        if( empty( $this->cloudflare_api_key ) || empty( $this->cloudflare_email_address ) ){
            return;
        }

    	$response = wp_remote_get( 'https://api.cloudflare.com/client/v4/zones', array(
    		'headers' => array(
    			'X-Auth-Key' 	=> $this->cloudflare_api_key,
    			'X-Auth-Email'  => $this->cloudflare_email_address
    		)
    	));
        if( is_wp_error( $response ) ){
            return $response->get_error_message();
        }
		else if( json_decode( $response['body'] )->success === false ){
			return json_decode( $response['body'] )->errors[0]->message;

		}
        else{
        	// Zone data from cloudflare
        	$zoneJson = json_decode( $response['body'] )->result;

        	// Array of zones to return
        	$zones = array();

        	foreach( $zoneJson as $zoneJ ){

        		$status = '';
        		switch( $zoneJ->status ){
        			case 'active':
        			case 'read-only':
        				$status = 'status-green';
        				break;

        			case 'pending':
        			case 'initializing':
        				$status = 'status-yellow';
        				break;

        			default:
        				$status = 'status-red';
        				break;
        		}

        		array_push( $zones, array(
        			'id' 		=> $zoneJ->id,
        			'name'		=> $zoneJ->name,
        			'status'	=> $status
        		));
        	}

            return $zones;
        }

	}

	/**
	 * Get Requests Data
	 *
	 * @since  0.1.0
	 * @param  $zoneId id of cloudflare zone
	 * @param  $period period of time to retrieve data
	 * @return $zones/WP_Error array of zones for current user ['x', 'Cached','Uncached']
	 */
	public function wpcd_get_requests( $zoneId, $period ) {

        if( empty( $this->cloudflare_api_key ) || empty( $this->cloudflare_email_address ) ){
            return;
        }

		$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'X-Auth-Key' 	=> $this->cloudflare_api_key,
				'X-Auth-Email'  => $this->cloudflare_email_address
			)
		));

        if( is_wp_error( $response ) ){
            return $response->get_error_message();
        }
        else{
			// Zone data from cloudflare
			$timeJson = json_decode( $response['body'] )->result->timeseries;

			// Array of cached requests
			$crequests = array();

			// Array of uncached requests
			$ucrequests = array();

			// Array of times
			$times = array();

			foreach( $timeJson as $tJ ){
				array_push( $times, $tJ->since);
				array_push( $crequests, $tJ->requests->cached );
				array_push( $ucrequests, $tJ->requests->uncached );
			}
			array_unshift( $times, 'x' );
			array_unshift( $crequests, 'Cached' );
			array_unshift( $ucrequests, 'Uncached' );

			return array(
				'times' => $times,
				'crequests' => $crequests,
				'ucrequests' => $ucrequests
			);

		}

	}

	/**
	 * Get Bandwidth Data
	 *
	 * @since  0.1.0
	 * @param  $zoneId id of cloudflare zone
	 * @param  $period    period of time to retrieve the data from
	 * @return $zones/WP_Error array of zones for current user ['x', 'Cached','Uncached']
	 */
	public function wpcd_get_bandwidth( $zoneId, $period ) {

        if( empty( $this->cloudflare_api_key ) || empty( $this->cloudflare_email_address ) ){
            return;
        }

		$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'X-Auth-Key' 	=> $this->cloudflare_api_key,
				'X-Auth-Email'  => $this->cloudflare_email_address
			)
		));

        if( is_wp_error( $response ) ){
            return $response->get_error_message();
        }
        else{
			// Zone data from cloudflare
			$timeJson = json_decode( $response['body'] )->result->timeseries;

			// Array of cached requests
			$crequests = array();

			// Array of uncached requests
			$ucrequests = array();

			// Array of times
			$times = array();

			foreach( $timeJson as $tJ ){
				array_push( $times, $tJ->since);
				array_push( $crequests, $tJ->bandwidth->cached );
				array_push( $ucrequests, $tJ->bandwidth->uncached );
			}
			array_unshift( $times, 'x' );
			array_unshift( $crequests, 'Cached' );
			array_unshift( $ucrequests, 'Uncached' );

			return array(
				'times' => $times,
				'crequests' => $crequests,
				'ucrequests' => $ucrequests
			);

		}

	}

	/**
	 * Get Visitor Data
	 *
	 * @since  0.1.0
	 * @param  $zoneId id of cloudflare zone
	 * @param  $period    period of time to retrieve the data from
	 * @return $zones array of zones for current user ['x', 'Unique Visitors']
	 */
	public function wpcd_get_visitors( $zoneId, $period ) {

        if( empty( $this->cloudflare_api_key ) || empty( $this->cloudflare_email_address ) ){
            return;
        }

		$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'X-Auth-Key' 	=> $this->cloudflare_api_key,
				'X-Auth-Email'  => $this->cloudflare_email_address
			)
		));

		if( is_wp_error( $response ) ){
            return $response->get_error_message();
        }
        else{
			// Zone data from cloudflare
			$timeJson = json_decode( $response['body'] )->result->timeseries;

			// Array of cached requests
			$uniques = array();

			// Array of times
			$times = array();

			foreach( $timeJson as $tJ ){
				array_push( $times, $tJ->since);
				array_push( $uniques, $tJ->uniques->all );
			}
			array_unshift( $times, 'x' );
			array_unshift( $uniques, 'Unique Visitors' );

			return array(
				'times' => $times,
				'uniques' => $uniques
			);

		}
	}

	/**
	 * Get Threat Data
	 *
	 * @since  0.1.0
	 * @param  $zoneId id of cloudflare zone
	 * @param  $period    period of time to retrieve the data from
	 * @return $zones array of threats for current user ['x', 'Threats']
	 */
	public function wpcd_get_threats( $zoneId, $period ) {

        if( empty( $this->cloudflare_api_key ) || empty( $this->cloudflare_email_address ) ){
            return;
        }

		$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'X-Auth-Key' 	=> $this->cloudflare_api_key,
				'X-Auth-Email'  => $this->cloudflare_email_address
			)
		));

		if( is_wp_error( $response ) ){
            return $response->get_error_message();
        }
        else{
			// Zone data from cloudflare
			$timeJson = json_decode( $response['body'] )->result->timeseries;

			// Array of cached requests
			$threats = array();

			// Array of times
			$times = array();

			foreach( $timeJson as $tJ ){
				array_push( $times, $tJ->since);
				array_push( $threats, $tJ->threats->all );
			}
			array_unshift( $times, 'x' );
			array_unshift( $threats, 'Threats' );

			return array(
				'times' => $times,
				'threats' => $threats
			);

		}

	}

	/**
	 * Get SSL Data
	 *
	 * @since  0.1.0
	 * @param  $zoneId id of cloudflare zone
	 * @param  $period    period of time to retrieve the data from
	 * @return $zones array of ssl requests for current user ['x', 'Encrypted', 'Unencrypted']
	 */
	public function wpcd_get_ssl( $zoneId, $period ) {

        if( empty( $this->cloudflare_api_key ) || empty( $this->cloudflare_email_address ) ){
            return;
        }

		$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'X-Auth-Key' 	=> $this->cloudflare_api_key,
				'X-Auth-Email'  => $this->cloudflare_email_address
			)
		));

		if( is_wp_error( $response ) ){
			return $response->get_error_message();
		}
		else{
			// Zone data from cloudflare
			$timeJson = json_decode( $response['body'] )->result->timeseries;

			// Array of encrypted requests
			$encrypted = array();

			// Array of unencrypted requests
			$unencrypted = array();

			// Array of times
			$times = array();

			foreach( $timeJson as $tJ ){
				array_push( $times, $tJ->since);
				array_push( $encrypted, $tJ->requests->ssl->encrypted );
				array_push( $unencrypted, $tJ->requests->ssl->unencrypted );
			}
			array_unshift( $times, 'x' );
			array_unshift( $encrypted, 'Encrypted' );
			array_unshift( $unencrypted, 'Unencrypted' );

			return array(
				'times' => $times,
				'encrypted' => $encrypted,
				'unencrypted' => $unencrypted
			);

		}
	}

}
