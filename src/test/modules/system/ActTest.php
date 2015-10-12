<?php

use \core\Factory;

require_once 'src\test\unittestinit.php';
require_once 'src\module\system\model\act.php';
require_once 'PHPUnit\Framework\TestCase.php';

class ActtableTest extends \module\system\table\ActTable {
	/*
	CREATE TABLE IF NOT EXISTS `test_act` (
	  `name` varchar(64) NOT NULL DEFAULT '',
	  `mod` varchar(64) NOT NULL DEFAULT '',
	  `ctl` varchar(64) NOT NULL DEFAULT '',
	  `act` varchar(64) NOT NULL DEFAULT '',
	  PRIMARY KEY (`mod`,`ctl`,`act`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='已安装模块功能表'
	 */
	protected $table = 'test_act';
}

class TActModel extends \module\system\model\ActModel {
	protected $table = 'test_act';
}

/**
 * Act test case.
 */
class ActTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var TActModel
	 */
	private $Act;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		Factory::db()->exec("DROP TABLE IF EXISTS test_act");
		$obj = new \module\system\model\ActModel();
		copyTable($obj->getTableObj()->getTable(), 'test_act');
		
		$this->Act = new TActModel(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated ActTest::tearDown()
		$this->Act = null;
		Factory::db()->exec("DROP TABLE test_act");
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Act->create()
	 */
	public function testCreate() {
		$act = array(
			'name' => '测试',
			'mod'  => 'system',
			'ctl'  => 'test',
			'act'  => 'demo',
		);
		
		$this->Act->fromArray($act);
		$this->Act->create(/* parameters */);
		
		$r = $this->Act->getActsByMod('system');
		$this->assertNotEmpty($r);
	}
	
	/**
	 * Tests Act->add()
	 */
	public function testAdd() {
		$this->Act->add('测试', 'tmod', 'tctl', 'tact');

		$r = $this->Act->getActsByMod('tmod');
		$this->assertNotEmpty($r);
	}
	
	/**
	 * Tests Act::removeByMod()
	 */
	public function testRemoveByMod() {
		$this->Act->add('测试', 'tmod', 'tctl', 'tact');

		$r = $this->Act->getActsByMod('tmod');
		$this->assertNotEmpty($r);
		
		$this->Act->removeByMod('tmod');

		$r = $this->Act->getActsByMod('tmod');
		$this->assertEmpty($r);
	}
	
	/**
	 * Tests Act->getActsByMod()
	 */
	public function testGetActsByMod() {
		
	}
}

