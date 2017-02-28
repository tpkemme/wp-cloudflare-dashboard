<?php

class WPCD_Assets_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'WPCD_Assets') );
	}

	function test_class_access() {
		$this->assertTrue( wpcd()->assets instanceof WPCD_Assets );
	}
}
