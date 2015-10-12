<?php

require_once 'src\core\util\encoder.php';

require_once 'PHPUnit\Framework\TestCase.php';

use core\util\Encoder;

/**
 * Encoder test case.
 */
class EncoderTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var \core\util\Encoder
	 */
	private $Encoder;
	
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
	 * Tests Encoder::encode()
	 */
	public function testEncode() {
		$text = '我是一段测试文本，我的内容比较长，我有很多文本`1234567890-=+_)(*&^%$#@!~:" .';
		$encode = Encoder::encode($text);
		$decode = Encoder::decode($encode);
		
		$this->assertEquals($text, $decode);
	}
}

