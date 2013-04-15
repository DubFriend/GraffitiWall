<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once 'graffiti_wall.php';

class Graffiti_Wall_Test extends PHPUnit_Framework_TestCase {
	private $GWall;
	private $validFull;
	function setUp() {
		$fp = fopen("test_json.json", 'w');
		fwrite($fp,NULL);
		fclose($fp);
		$this->GWall = new Graffiti_Wall("test_json.json");
		$this->validFull = '[{"x":' . Graffiti_Wall::MAX_X .
		                   ',"y":' . Graffiti_Wall::MAX_Y .
		                   ',"s":' . Graffiti_Wall::MAX_RADIUS . ',"c":"FFFFFF"}]';
	}

	function tearDown() {
		$fp = fopen("test_json.json", 'w');
		fwrite($fp,NULL);
		fclose($fp);
	}

//------ is_json_valid ------------------------------------------------------------------------

	function test_is_json_valid_success_basic() {
		$this->assertTrue($this->GWall->is_json_valid('[{"x":123,"y":321}]'));
	}
	function test_is_json_valid_success_color() {
		$this->assertTrue($this->GWall->is_json_valid('[{"x":1,"y":0,"c":"FFFFFF"}]'));
	}
	function test_is_json_valid_success_size() {
		$this->assertTrue($this->GWall->is_json_valid('[{"x":1,"y":1,"s":5}]'));
	}
	function test_is_json_valid_success_full() {
		$this->assertTrue($this->GWall->is_json_valid($this->validFull));
	}

	function test_is_json_valid_fail_empty_array() {
		$this->assertFalse($this->GWall->is_json_valid("[]"));
	}

	function test_is_json_valid_fail_bad_key() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":1,"y":2,"foo":3}]'));
	}

	function test_is_json_valid_fail_missing_x() {
		$this->assertFalse($this->GWall->is_json_valid('[{"y":2}]'));
	}
	function test_is_json_valid_fail_missing_y() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":2}]'));
	}

	function test_is_json_valid_fail_y_too_small() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":1,"y":-1}]'));
	}
	function test_is_json_valid_fail_x_too_small() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":-1,"y":1}]'));
	}

	function test_is_json_valid_fail_y_too_big() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":1,"y":' . Graffiti_Wall::MAX_Y + 1 . '}]'));
	}
	function test_is_json_valid_fail_x_too_big() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":' . Graffiti_Wall::MAX_X + 1 . ',"y":1}]'));
	}

	function test_is_json_valid_fail_radius_too_small() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":1,"y":1,"s":0}]'));
	}
	function test_is_json_valid_fail_radius_too_big() {
		$this->assertFalse($this->GWall->is_json_valid(
			'[{"x":1,"y":1,"s":' . Graffiti_Wall::MAX_RADIUS + 1 . '}]'
		));
	}

	function test_is_json_valid_invalid_color_wrong_length() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":1,"y":2,"c":"FFFFF"}]'));
	}
	function test_is_json_valid_invalid_color_wrong_format() {
		$this->assertFalse($this->GWall->is_json_valid('[{"x":1,"y":2,"c":"012ABx"}]'));
	}

//----------- get_json_data ----------------------------------------------------------------------

	function test_get_json_data_no_data() {
		$this->assertEquals("", $this->GWall->get_json_data());
	}

//---------- save_picture ------------------------------------------------------------------------

	/** @depends test_get_json_data_no_data */
	function test_save_painting_first_save() {
		$this->GWall->save_painting($this->validFull);
		$this->assertEquals(
			$this->validFull,
			$this->GWall->get_json_data()
		);
	}
	/** @depends test_get_json_data_no_data */
	function test_save_painting_second_save() {
		$this->GWall->save_painting('[{"x":1,"y":2}]');
		$this->GWall->save_painting('[{"x":3,"y":4}]');
		$this->assertEquals(
			'[{"x":1,"y":2},{"x":3,"y":4}]',
			$this->GWall->get_json_data()
		);
	}
}
?>