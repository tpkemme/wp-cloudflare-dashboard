<?php
/**
 * WP Cloudflare Dashboard
 *
 * This class uses data retrieved from WPCD_Cloudclient to generate charts using the
 * C3 Javascript Library
 *
 * @since 0.1.0
 * @package WP Cloudflare Dashboard
 */

/**
 * WP Cloudflare Dashboard Charts.
 *
 * @since 0.1.0
 */
class WPCD_Charts {

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
		<div id = "requests">
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-requests"></div>
			</div>
			<script type="text/javascript">
				function drawRequestsChart() {
					var data = google.visualization.arrayToDataTable( <?php echo json_encode( $requests ); ?> ),
					options = {
						title: 'Requests',
						hAxis: {title: 'x',  titleTextStyle: {color: '#333'}},
						vAxis: {minValue: 0},
						'width':600,
						'height':400
					},
					chart = new google.visualization.AreaChart( document.getElementById( 'wpcd-requests' ) );

					chart.draw( data, options );

				}
				</script>
			</div>
		</div>
		<?php
	}

	/**
	 * Display Analytics for Bandwidth
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_bandwidth( $requests ) {
		?>
		<div id = "bandwidth">
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-bandwidth"></div>
			</div>
			<!-- <script type="text/javascript">
			function drawChart() {
				var data = google.visualization.arrayToDataTable( <?php echo json_encode( $requests ); ?> ),
				options = {
					title: 'Requests',
					hAxis: {title: 'x',  titleTextStyle: {color: '#333'}},
					vAxis: {minValue: 0},
					'width':600,
					'height':400
				},
				chart = new google.visualization.AreaChart( document.getElementById( 'wpcd-bandwidth' ) );

				chart.draw( data, options );

			}
			</script> -->
		</div>
		<?php
	}

	/**
	 * Display Analytics for Unique Visitors
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_visitors( $visitors ) {
		?>
		<div id = "visitors">
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-visitors"></div>
			</div>
			<!-- <script type="text/javascript">
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
			</script> -->
		</div>
		<?php
	}

	/**
	 * Display Analytics for Threats
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_threats( $threats ) {
		?>
		<div id = "threats">
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-threats"></div>
			</div>
			<!-- <script type="text/javascript">
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
			</script> -->
		</div>
		<?php
	}

	/**
	 * Display Analytics for SSL
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public static function display_ssl( $requests ) {
		?>
		<div id = "ssl">
			<div class="cmb2-analytics">
				<div class="cmb2-analytics-data" id="wpcd-ssl"></div>
			</div>
			<!-- <script type="text/javascript">
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
			</script> -->
		</div>
		<?php
	}
}
