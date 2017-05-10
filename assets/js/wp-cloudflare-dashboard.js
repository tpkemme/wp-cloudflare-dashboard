/**
 * WP Cloudflare Dashboard
 * https://tylerkemme.com
 *
 * Licensed under the GPLv2+ license.
 */

window.WPCloudflareDashboard = window.WPCloudflareDashboard || {};

( function( window, document, $, plugin ) {
	var $c = {};

	plugin.init = function() {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function() {
		$c.window = $( window );
		$c.body = $( document.body );
	};

	plugin.bindEvents = function() {

		// Test Cloudflare Credentials
		$( 'input[name="test-cloudflare-creds"]' ).bind( 'click', function( event ) {
			var data = {
				'action': 'test_cloudflare_creds'
			};

			event.preventDefault();

			if ( $( '#green-success-connection' ).length ) {
				$( '#green-success-connection' ).remove();
			}

			if ( $( '#red-error-connection' ).length ) {
				$( '#red-error-connection' ).remove();
			}

			jQuery.post( '/wp-admin/admin-ajax.php', data, function( response ) {

				if ( '200' === response ) {
					$( '<span id="green-success-connection"><span style="font-size: 20px;">✓</span> Connection successful</span>' ).insertAfter( $( 'input[name="test-cloudflare-creds"]' ) );
				} else {
					$( '<span id="red-error-connection"><span style="font-size: 20px;">⨂</span> Connection unsuccessful.</span>' ).insertAfter( $( 'input[name="test-cloudflare-creds"]' ) );
				}
			});

		});

		if ( 0 < $( '.wpcd-analytics-page' ).length ) {

			// Initiate jquery tabs on analytics page
			$( '#analytics-tabs' ).tabs();

		}
	};

	$( plugin.init );


}( window, document, jQuery, window.WPCloudflareDashboard ) );

/* global google */
// Set chart options
var options = {'title':'How Much Pizza I Ate Last Night',
			   'width':400,
			   'height':300};
google.charts.load( 'current', { 'packages': [ 'corechart' ] });
google.charts.setOnLoadCallback( drawRequestsChart );
