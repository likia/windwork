<?php

require_once 'src\core\util\xml.php';

require_once 'PHPUnit\Framework\TestCase.php';

use core\util\XML;

/**
 * XML test case.
 */
class XMLTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var XML
	 */
	private $XML;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests XML::make()
	 */
	public function testMake() {
		$arr = array('a' => '的ssd三的速度', 'b' => 'dswedwse', 'arr' => array('xx' => 1 ,2, 3), 'c' => 234567);	
		$obj = XML::make($arr);
		$xml = $obj->asXML();
		print_r($xml);
	}
}

