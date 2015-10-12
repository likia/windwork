<?php
require_once 'core/Hook.php';
require_once 'core/IHook.php';

require_once 'PHPUnit/Framework/TestCase.php';

use core\Hook;

class MyHook implements \core\IHook {
	public function execute($params = array()) {
		
	}
}

/**
 * Hook test case.
 */
class HookTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Hook
	 */
	private $Hook;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated HookTest::setUp()
		
		$this->Hook = new Hook(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated HookTest::tearDown()
		$this->Hook = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests Hook::call()
	 */
	public function testCall() {
		Hook::registerHook('pa', new MyHook());
		Hook::registerHook('pa', array(new MyHook(), array(1, 2, 3)));
		Hook::registerHook('pb', '\MyHook');
		Hook::registerHook('pb', array('\MyHook', array(1, 2, 3)));
		
		$this->assertEquals(2, count(Hook::getRegistry('pa')));
		$hooks = Hook::$hooks;
		
		$this->assertFalse(Hook::call('pa'));

		Hook::$enabled = 1;
		
		$this->assertTrue(Hook::call('pa'));
		$this->assertTrue(Hook::call('pb'));
	}
}

