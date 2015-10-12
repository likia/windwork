<?php

use \module\system\model\OptionModel;

require_once 'test/unittestinit.php';
require_once 'src\module\system\model\option.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Option test case.
 */
class OptionTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Option
	 */
	private $Option;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->Option = new OptionModel();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->Option = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
	}
		
	/**
	 * Tests Option::getOptions()
	 */
	public function testGetOptions() {		
		$opts = OptionModel::getOptions();
		$this->assertNotEmpty($opts);
	}
	
	/**
	 * Tests Option->getEditableOptionsByGroup()
	 */
	public function testGetEditableOptionsByGroup() {
		$opts = $this->Option->getEditableOptionsByGroup('system');
		$this->assertNotEmpty($opts);
	}
	
	/**
	 * Tests Option::alterValue()
	 */
	public function testAlterValue() {
		$id = 'site_name';
		$opt = $this->Option->setObjId($id)->load()->toArray();
		
		Test::trace($opt);
		
		$val = 'xxxxx';
		$this->Option->alterValue($id, $val);

		$optObj = new OptionModel();
		$optObj->setObjId($id)->load();
		
		$this->assertTrue($optObj->value == $val);
				

		$this->Option->alterValue($id, $opt['value']);
		$optObj->setObjId($id)->load();
		$this->assertTrue($optObj->value != $val);
	}
	
}

