<?php

class WPCD_Cloudclient_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'WPCD_Cloudclient') );
	}

	function test_class_access() {
		$this->assertTrue( wpcd()->cloudclient instanceof WPCD_Cloudclient );
	}
}
