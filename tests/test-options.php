<?php

class WPCD_Options_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'WPCD_Options') );
	}

	function test_class_access() {
		$this->assertTrue( wpcd()->options instanceof WPCD_Options );
	}
}
