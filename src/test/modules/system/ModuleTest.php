<?php

use \core\Factory;

require_once 'src\test\unittestinit.php';
require_once 'src\module\system\model\module.php';
require_once 'PHPUnit\Framework\TestCase.php';

class TModuleModel extends \module\system\model\ModuleModel {
	protected $table = 'test_mod';
}

/**
 * Module test case.
 */
class ModuleTest extends PHPUnit_Framework_TestCase {
	const TEST_TABLE = 'test_mod';
	
	/**
	 *
	 * @var TModuleModel
	 */
	private $Module;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		Factory::db()->exec("DROP TABLE IF EXISTS ".static::TEST_TABLE);
		$obj = new \module\system\model\ModuleModel();
		copyTable($obj->getTableObj()->getTable(), static::TEST_TABLE);
		
		$this->Module = new TModuleModel(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated ModuleTest::tearDown()
		$this->Module = null;

		Factory::db()->exec("DROP TABLE ".static::TEST_TABLE);
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Module->install()
	 */
	public function testInstall() {
		$mods = $this->Module->getInstalledMods();
		
		$inst = $this->Module->setPkv('ad')->install();		
		$mods = $this->Module->getInstalledMods();
		
		$this->assertTrue($inst);
		$this->assertNotEmpty($mods['ad']);

		$this->Module->setPkv('ad')->uninstall();

		$mods = $this->Module->getInstalledMods();
		$this->assertTrue(empty($mods['ad']));
	}
		
	/**
	 * Tests Module::isAccessable()
	 */
	public function testIsAcessable() {
		$code = 200;
		$_SESSION['uid'] = 1;
		$isAccessable = \module\system\model\ModuleModel::isAccessable('system', 'admin.admincp', 'index', $code);
		if (!$isAccessable) {
			throw new Exception("错误码： $code");
		}
		
		$this->assertTrue($isAccessable);
	}	
	
	
}

