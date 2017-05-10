<?php
/**
 * WP Cloudflare Dashboard Options
 *
 * @since 0.1.0
 * @package WP Cloudflare Dashboard
 */

 // Require CMB2
 require dirname( __DIR__ ) . '/vendor/cmb2/init.php';

/**
 * WP Cloudflare Dashboard Options class.
 *
 * @since 0.1.0
 */
class WPCD_Analytics {
	/**
	 * Parent plugin class
	 *
	 * @var    WP_Cloudflare_Dashboard
	 * @since  0.1.0
	 */
	protected $plugin = null;

	/**
	 * Analytics key, and analytics page slug
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $key = 'wp_cloudflare_dashboard_analytics';

	/**
	 * Analytics page metabox id
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $metabox_id = 'wp_cloudflare_dashboard_analytics_metabox';


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
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
	}


	/**
	 * Analytics page markup
	 * @since  0.1.0
	 * @return void
	 */
	public function analytics_page_display()
{
		$cloudclient = $this->plugin->cloudclient;
		$charts = $this->plugin->charts;
		$zones = $cloudclient->wpcd_get_zones();

		if( empty( wpcd_get_option( 'cloudflare_email_address' ) ) || empty( wpcd_get_option( 'cloudflare_api_key' ) ) ): ?>
			<div class="wrap wpcd-analytics-page <?php echo esc_attr( $this->key ); ?>">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<br/>
				<div class="notice notice-error"><p>Please set your Cloudflare API Key and Email Address <a href="/wp-admin/admin.php?page=wp_cloudflare_dashboard_options">here</a>.</p></div>
			</div>
		<?php elseif( $zones === 'Invalid request headers'): ?>
			<div class="wrap wpcd-analytics-page <?php echo esc_attr( $this->key ); ?>">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<br/>
				<div class="notice notice-error"><p>There was a problem with your Cloudflare credentials.  Please verify them <a href="/wp-admin/admin.php?page=wp_cloudflare_dashboard_options">here</a>.</p></div>
			</div>
		<?php else: ?>
			<div class="wrap wpcd-analytics-page <?php echo esc_attr( $this->key ); ?>">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<br/>
				<div class="wpcd-analytics-form">
					<form id="filter-select-form" class="select-form" method="POST" >
						<fieldset>
							<label for="zoneSelect">Select a Website:</label>
							<select name="zoneSelect" id="zoneSelect">
								<?php $count = 1; foreach( $zones as $zone ): ?>
									<?php if( isset($_POST['zoneSelect'] ) && $_POST['zoneSelect'] == $zone['id'] ): ?>
										<option selected="selected" value="<?php echo $zone['id']; ?>" data-class="<?php echo $zone['status']; ?>"><?php echo $zone['name']; ?></option>
									<?php else: ?>
										<option value="<?php echo $zone['id']; ?>" data-class="<?php echo $zone['status']; ?>"><?php echo $zone['name']; ?></option>
									<?php endif; ?>
									<?php $count++; ?>
								<?php endforeach; ?>
							</select>
						</fieldset>
						<fieldset>
							<label for="timeSelect">Select a Time Period:</label>
							<select name="timeSelect" id="timeSelect">
								<?php if( isset( $_POST['timeSelect'] ) && $_POST['timeSelect'] == '1440' ): ?>
									<option selected="selected" value="1440" data-class="day">Last 24 Hours</option>
								<?php else: ?>
									<option value="1440" data-class="day">Last 24 Hours</option>
								<?php endif; ?>
								<?php if( isset( $_POST['timeSelect'] ) && $_POST['timeSelect'] == '10080' ): ?>
									<option selected="selected" value="10080" data-class="week">Last Week</option>
								<?php else: ?>
									<option value="10080" data-class="week">Last Week</option>
								<?php endif; ?>
								<?php if( isset( $_POST['timeSelect'] ) && $_POST['timeSelect'] == '43200' ): ?>
									<option selected="selected" value="43200" data-class="month">Last Month</option>
								<?php else: ?>
									<option value="43200" data-class="month">Last Month</option>
								<?php endif; ?>
								<?php if( isset( $_POST['timeSelect'] ) && $_POST['timeSelect'] == '525600' ): ?>
									<option selected="selected" value="525600" data-class="year">Last year</option>
								<?php else: ?>
									<option value="525600" data-class="year">Last Year</option>
								<?php endif; ?>
							</select>
						</fieldset>
						<input type="submit" value="Filter Data" id="filter-button" class="button-secondary" />
					</form>
					<div id="analytics-tabs">
						<ul>
						    <li><a href="#requests"><span>Requests</span></a></li>
						    <li><a href="#bandwidth"><span>Bandwidth</span></a></li>
						    <li><a href="#visitors"><span>Unique Visitors</span></a></li>
						    <li><a href="#threats"><span>Threats</span></a></li>
						    <li><a href="#ssl"><span>SSL</span></a></li>
						</ul>
				 		<?php
							$requests = $cloudclient->wpcd_get_requests(
								isset( $_POST['zoneSelect'] ) ? $_POST['zoneSelect'] : $zones[0]['id'],
								isset( $_POST['timeSelect'] ) ? $_POST['timeSelect'] : '1440'
							);
							$charts->display_requests( $requests );
						?>
						<?php
							$requests = $cloudclient->wpcd_get_bandwidth(
								isset( $_POST['zoneSelect'] ) ? $_POST['zoneSelect'] : $zones[0]['id'],
								isset( $_POST['timeSelect'] ) ? $_POST['timeSelect'] : '1440'
								);
							$charts->display_bandwidth( $requests );
						?>

						<?php
							$visitors = $cloudclient->wpcd_get_visitors(
								isset( $_POST['zoneSelect'] ) ? $_POST['zoneSelect'] : $zones[0]['id'],
								isset( $_POST['timeSelect'] ) ? $_POST['timeSelect'] : '1440'
								);
							$charts->display_visitors( $visitors );
						?>
						<?php
							$threats = $cloudclient->wpcd_get_threats(
								isset( $_POST['zoneSelect'] ) ? $_POST['zoneSelect'] : $zones[0]['id'],
								isset( $_POST['timeSelect'] ) ? $_POST['timeSelect'] : '1440'
							);
							$charts->display_threats( $threats );
						?>
						<?php
							$ssl = $cloudclient->wpcd_get_ssl(
								isset( $_POST['zoneSelect'] ) ? $_POST['zoneSelect'] : $zones[0]['id'],
								isset( $_POST['timeSelect'] ) ? $_POST['timeSelect'] : '1440'
							);
							$charts->display_ssl( $ssl );
						?>
					</div>
				</div>
			</div>
		<?php endif;
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'analytics_page' ), true ) ) {
			return $this->{$field};
		}
		throw new Exception( 'Invalid property: ' . $field );
	}
}
