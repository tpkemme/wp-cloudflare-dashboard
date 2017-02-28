/**
 * WP Cloudflare Dashboard
 * https://tacticalwp.com
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

			jQuery.post( '/wp-admin/admin-ajax.php', data, function( response ) {
				if ( '200' === response ) {
					$( '<span style="color: #00ff00;">✓ Success</span>' ).appendTo( $( 'input[name="test-cloudflare-creds"]' ).closest( '.cmb-td' ) );
				} else {
					$( '<span style="color: red;">X Error</span>' ).appendTo( $( 'input[name="test-cloudflare-creds"]' ).closest( '.cmb-td' ) );
				}
			});

		});
	};

	$( plugin.init );
}( window, document, jQuery, window.WPCloudflareDashboard ) );
