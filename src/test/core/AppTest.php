<?php

require_once 'src\test\phpunit.php';


use core\App;

/**
 * App test case.
 */
class AppTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var App
	 */
	private $App;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated AppTest::setUp()
		
		//$this->App = new App(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated AppTest::tearDown()
		$this->App = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests App::getInstance()
	 */
	public function testGetInstance() {
		// TODO Auto-generated AppTest::testGetInstance()
		$this->markTestIncomplete ( "getInstance test not implemented" );
		
		App::getInstance(/* parameters */);
	}
	
	/**
	 * Tests App->dispatch()
	 */
	public function testDispatch() {
		// TODO Auto-generated AppTest->testDispatch()
		$this->markTestIncomplete ( "dispatch test not implemented" );
		
		$this->App->dispatch(/* parameters */);
	}
	
	/**
	 * Tests App->getCtlObj()
	 */
	public function testGetCtlObj() {
		// TODO Auto-generated AppTest->testGetCtlObj()
		$this->markTestIncomplete ( "getCtlObj test not implemented" );
		
		$this->App->getCtlObj(/* parameters */);
	}
	
	
	/**
	 * Tests App::setDispatch()
	 */
	public function testSetDispatch() {
		// TODO Auto-generated AppTest::testSetDispatch()
		$this->markTestIncomplete ( "setDispatch test not implemented" );
		
	}
	
}

