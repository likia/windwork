<?php
define('ROOT_PATH', __DIR__);
require_once 'src/test/phpunit.php';
require_once 'src/core/adapter/sms/PicaSMS.php';

require_once 'PHPUnit/Framework/TestCase.php';

use \core\adapter\sms\PicaSMS;
use core\Factory;

/**
 * PicaSMS test case.
 */
class PicaSMSTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var PicaSMS
	 */
	private $PicaSMS;
	
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
		// TODO Auto-generated PicaSMSTest::tearDown()
		$this->PicaSMS = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests PicaSMS->send()
	 */
	public function testSend() {
		$cfg = include 'src/config/sms.php';
		$this->PicaSMS = new PicaSMS($cfg);
		$this->assertTrue($this->PicaSMS->send('15360561030', 'testSend'));
	}

	/**
	 * Tests PicaSMS->send()
	 */
	public function testSendSmsCode() {
		$cfg = include 'src/config/sms.php';
		$this->PicaSMS = new PicaSMS($cfg);
		$this->assertTrue($this->PicaSMS->sendSmsCode('15360561030', 'testSendSmsCode'));
	}
	
	/**
	 * Tests PicaSMS->send()
	 */
	public function testSendSmsCodeByFactory() {
		$this->assertTrue(Factory::sms()->sendSmsCode('15360561030', 'testSendSmsCodeByFactory'));
	}
}

