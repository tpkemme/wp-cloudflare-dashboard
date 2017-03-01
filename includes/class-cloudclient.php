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

	/**
	 * Get Zones
	 *
	 * @since  0.1.0
	 * @return $zones array of zones for current user ['id', 'name','status']
	 */
	public function wpcd_get_zones() {

		$client = new GuzzleHttp\Client();

		try{

			$res = $client->request('GET', 'https://api.cloudflare.com/client/v4/zones', [
				'headers' => [
					'X-Auth-Key' 	=> $this->cloudflare_api_key,
					'X-Auth-Email'  => $this->cloudflare_email_address
				]
			]);

			// Zone data from cloudflare
			$zoneJson = json_decode($res->getBody())->result;

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

		} catch ( Exception $e ) {

			return $e;
		}

	}

	/**
	 * Get Requests Data
	 *
	 * @since  0.1.0
	 * @param  $zoneId id of cloudflare zone
	 * @param  $period period of time to retrieve data
	 * @return $zones array of zones for current user ['x', 'Cached','Uncached']
	 */
	public function wpcd_get_requests( $zoneId, $period ) {

		$client = new GuzzleHttp\Client();

		try{
			$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
			$res = $client->request('GET', $url, [
				'headers' => [
					'X-Auth-Key' 	=> $this->cloudflare_api_key,
					'X-Auth-Email'  => $this->cloudflare_email_address
				]
			]);

			// Zone data from cloudflare
			$timeJson = json_decode($res->getBody())->result->timeseries;

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

		} catch ( Exception $e ) {
			return $e;
		}

	}

	/**
	 * Display Analytics for Requests
	 *
	 * @since  0.1.0
	 * @param  $requests  an array of retrieved requests for a specific zone
	 * @return void
	 */
	public static function display_requests( $requests ) {

		?>
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-requests"></div>
			</div>
			<script type="text/javascript">
				var chart = c3.generate({
					bindto: '#wpcd-requests',
					data: {
						x: 'x',
						xFormat: '%Y-%m-%dT%H:%M:%SZ',
						columns: [
							[<?php foreach( $requests['times'] as $time ): ?>
								 '<?php echo $time ?>',
							<?php endforeach; ?>],
							[<?php foreach( $requests['crequests'] as $crequest ): ?>
								 '<?php echo $crequest ?>',
							<?php endforeach; ?>],
							[<?php foreach( $requests['ucrequests'] as $ucrequest ): ?>
								 '<?php echo $ucrequest ?>',
							<?php endforeach; ?>]
						],
						types: {
							'Uncached': 'area-spline',
							'Cached': 'area-spline'
						}
					},
					axis: {
						x: {
							type: 'timeseries',
							tick: {
								format: '%_m/%-e, %_I%p'
							}
						}
					}
				});
			</script>
		<?php
	}

	/**
	 * Get Bandwidth Data
	 *
	 * @since  0.1.0
	 * @param  $zoneId id of cloudflare zone
	 * @param  $period    period of time to retrieve the data from
	 * @return $zones array of zones for current user ['x', 'Cached','Uncached']
	 */
	public function wpcd_get_bandwidth( $zoneId, $period ) {

		$client = new GuzzleHttp\Client();

		try{
			$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
			$res = $client->request('GET', $url, [
				'headers' => [
					'X-Auth-Key' 	=> $this->cloudflare_api_key,
					'X-Auth-Email'  => $this->cloudflare_email_address
				]
			]);

			// Zone data from cloudflare
			$timeJson = json_decode($res->getBody())->result->timeseries;

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

		} catch ( Exception $e ) {
			return $e;
		}

	}

	/**
	 * Display Analytics for Bandwidth
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_bandwidth( $requests ) {

		?>
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-bandwidth"></div>
			</div>
			<script type="text/javascript">
				var chart = c3.generate({
					bindto: '#wpcd-bandwidth',
					data: {
						x: 'x',
						xFormat: '%Y-%m-%dT%H:%M:%SZ',
						columns: [
							[<?php foreach( $requests['times'] as $time ): ?>
								 '<?php echo $time ?>',
							<?php endforeach; ?>],
							[<?php foreach( $requests['crequests'] as $crequest ): ?>
								 '<?php echo $crequest ?>',
							<?php endforeach; ?>],
							[<?php foreach( $requests['ucrequests'] as $ucrequest ): ?>
								 '<?php echo $ucrequest ?>',
							<?php endforeach; ?>]
						],
						types: {
							'Uncached': 'area-spline',
							'Cached': 'area-spline'
						}
					},
					axis: {
						x: {
							type: 'timeseries',
							tick: {
								format: '%_m/%-e, %_I%p'
							}
						}
					}
				});
			</script>
		<?php
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

		$client = new GuzzleHttp\Client();

		try{
			$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
			$res = $client->request('GET', $url, [
				'headers' => [
					'X-Auth-Key' 	=> $this->cloudflare_api_key,
					'X-Auth-Email'  => $this->cloudflare_email_address
				]
			]);

			// Zone data from cloudflare
			$timeJson = json_decode($res->getBody())->result->timeseries;

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

		} catch ( Exception $e ) {
			return $e;
		}

	}

	/**
	 * Display Analytics for Unique Visitors
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_visitors( $visitors ) {
		?>
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-visitors"></div>
			</div>
			<script type="text/javascript">
				var chart = c3.generate({
					bindto: '#wpcd-visitors',
					data: {
						x: 'x',
						xFormat: '%Y-%m-%dT%H:%M:%SZ',
						columns: [
							[<?php foreach( $visitors['times'] as $time ): ?>
								 '<?php echo $time ?>',
							<?php endforeach; ?>],
							[<?php foreach( $visitors['uniques'] as $unique ): ?>
								 '<?php echo $unique ?>',
							<?php endforeach; ?>]
						],
						types: {
							'Unique Visitors': 'area-spline'
						}
					},
					axis: {
						x: {
							type: 'timeseries',
							tick: {
								format: '%_m/%-e, %_I%p'
							}
						}
					}
				});
			</script>
		<?php
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

		$client = new GuzzleHttp\Client();

		try{
			$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
			$res = $client->request('GET', $url, [
				'headers' => [
					'X-Auth-Key' 	=> $this->cloudflare_api_key,
					'X-Auth-Email'  => $this->cloudflare_email_address
				]
			]);

			// Zone data from cloudflare
			$timeJson = json_decode($res->getBody())->result->timeseries;

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

		} catch ( Exception $e ) {
			return $e;
		}

	}

	/**
	 * Display Analytics for Threats
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_threats( $threats ) {
		?>
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-threats"></div>
			</div>
			<script type="text/javascript">
				var chart = c3.generate({
					bindto: '#wpcd-threats',
					data: {
						x: 'x',
						xFormat: '%Y-%m-%dT%H:%M:%SZ',
						columns: [
							[<?php foreach( $threats['times'] as $time ): ?>
								 '<?php echo $time ?>',
							<?php endforeach; ?>],
							[<?php foreach( $threats['threats'] as $threat ): ?>
								 '<?php echo $threat ?>',
							<?php endforeach; ?>]
						],
						types: {
							'Threats': 'area-spline'
						}
					},
					axis: {
						x: {
							type: 'timeseries',
							tick: {
								format: '%_m/%-e, %_I%p'
							}
						}
					}
				});
			</script>
		<?php
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

		$client = new GuzzleHttp\Client();

		try{
			$url = 'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/analytics/dashboard?since=-' . $period;
			$res = $client->request('GET', $url, [
				'headers' => [
					'X-Auth-Key' 	=> $this->cloudflare_api_key,
					'X-Auth-Email'  => $this->cloudflare_email_address
				]
			]);

			// Zone data from cloudflare
			$timeJson = json_decode($res->getBody())->result->timeseries;

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

		} catch ( Exception $e ) {
			return $e;
		}

	}

	/**
	 * Display Analytics for SSL
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_ssl( $requests ) {
		?>
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-ssl"></div>
			</div>
			<script type="text/javascript">
				var chart = c3.generate({
					bindto: '#wpcd-ssl',
					data: {
						x: 'x',
						xFormat: '%Y-%m-%dT%H:%M:%SZ',
						columns: [
							[<?php foreach( $requests['times'] as $time ): ?>
								 '<?php echo $time ?>',
							<?php endforeach; ?>],
							[<?php foreach( $requests['encrypted'] as $request ): ?>
								 '<?php echo $request ?>',
							<?php endforeach; ?>],
							[<?php foreach( $requests['unencrypted'] as $request ): ?>
								 '<?php echo $request ?>',
							<?php endforeach; ?>]
						],
						types: {
							'Encrypted': 'area-spline',
							'Unencrypted': 'area-spline'
						}
					},
					axis: {
						x: {
							type: 'timeseries',
							tick: {
								format: '%_m/%-e, %_I%p'
							}
						}
					}
				});
			</script>
		<?php
	}
}
