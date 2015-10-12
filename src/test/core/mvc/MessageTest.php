<?php

require_once 'src\core\mvc\message.php';

require_once 'PHPUnit\Framework\TestCase.php';

use core\mvc\Message;

/**
 * Message test case.
 */
class MessageTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Message
	 */
	private $Message;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated MessageTest::setUp()
		
		$this->Message = new Message(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated MessageTest::tearDown()
		$this->Message = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Message::hasMessage()
	 */
	public function testHasMessage() {
		// TODO Auto-generated MessageTest::testHasMessage()
		$this->markTestIncomplete ( "hasMessage test not implemented" );
		
		Message::hasMessage(/* parameters */);
	}
	
	/**
	 * Tests Message::getMessages()
	 */
	public function testGetMessages() {
		// TODO Auto-generated MessageTest::testGetMessages()
		$this->markTestIncomplete ( "getMessages test not implemented" );
		
		Message::getMessages(/* parameters */);
	}
	
	/**
	 * Tests Message::setErr()
	 */
	public function testSetErr() {
		// TODO Auto-generated MessageTest::testSetErr()
		$this->markTestIncomplete ( "setErr test not implemented" );
		
		Message::setErr(/* parameters */);
	}
	
	/**
	 * Tests Message::setWarn()
	 */
	public function testSetWarn() {
		// TODO Auto-generated MessageTest::testSetWarn()
		$this->markTestIncomplete ( "setWarn test not implemented" );
		
		Message::setWarn(/* parameters */);
	}
	
	/**
	 * Tests Message::setOK()
	 */
	public function testSetOK() {
		// TODO Auto-generated MessageTest::testSetOK()
		$this->markTestIncomplete ( "setOK test not implemented" );
		
		Message::setOK(/* parameters */);
	}
	
	/**
	 * Tests Message::hasWarn()
	 */
	public function testHasWarn() {
		// TODO Auto-generated MessageTest::testHasWarn()
		$this->markTestIncomplete ( "hasWarn test not implemented" );
		
		Message::hasWarn(/* parameters */);
	}
	
	/**
	 * Tests Message::hasOK()
	 */
	public function testHasOK() {
		// TODO Auto-generated MessageTest::testHasOK()
		$this->markTestIncomplete ( "hasOK test not implemented" );
		
		Message::hasOK(/* parameters */);
	}
	
	/**
	 * Tests Message::hasErr()
	 */
	public function testHasErr() {
		Message::hasErr(/* parameters */);
	}
	
	/**
	 * Tests Message::getWarns()
	 */
	public function testGetWarns() {
		$msg = Message::getWarns();
		$this->assertEmpty($msg);
		

		$this->assertEmpty(Message::hasWarn());
		
		Message::setWarn('有些东西需要搞定!');
		$msg = Message::getWarns();
		$this->assertNotEmpty($msg);

		$this->assertNotEmpty(Message::hasWarn());
	}
	
	/**
	 * Tests Message::getOKs()
	 */
	public function testGetOKs() {		
		$msg = Message::getOKs();
		$this->assertEmpty($msg);
		

		$this->assertEmpty(Message::hasOK());
		
		Message::setOK('搞定!');
		$msg = Message::getOKs();
		$this->assertNotEmpty($msg);
		
		$this->assertNotEmpty(Message::hasOK());
	}
	
	/**
	 * Tests Message::getErrs()
	 */
	public function testGetErrs() {
		$errs = Message::getErrs();
		$this->assertEmpty($errs);
		
		$this->assertEmpty(Message::hasErr());
		
		Message::setErr('有问题测试!');
		$errs = Message::getErrs();
		$this->assertNotEmpty($errs);
		
		$this->assertNotEmpty(Message::hasErr());
	}
}

