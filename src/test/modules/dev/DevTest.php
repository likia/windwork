<?php
require_once 'test/unittestinit.php';
require_once 'PHPUnit/Framework/TestCase.php';

use dev\model\DevModel;
use system\model\UIModel;

/**
 * DevModel test case.
 */
class DevTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var DevModel
	 */
	private $DevModel;
	
	const MOD_DIR = 'buildertest';
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->DevModel = new DevModel(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->DevModel = null;

		$modDir = SRC_PATH . 'module/'.self::MOD_DIR;
		\core\File::removeDirs($modDir, true);
		\core\File::removeDirs(UIModel::getCurrentTplDir().self::MOD_DIR, true);
		\core\File::removeDirs(UIModel::getCurrentTplDir().'admincp/'.self::MOD_DIR, true);
		
		parent::tearDown ();
	}
	
	public function testDev() {
		// 创建模块
		$modCfg = array(
			'dir'        => self::MOD_DIR,
			'name'       => '测试',
			'version'    => '1.0',
			'author'     => 'windwork.org',
			'email'      => 'cmm@windwork.org',
			'siteurl'    => 'http://www.windwork.org',
			'copyright'  => 'Copyright (c) 2008-2014 Windwork Team.',
			'desc'       => '这是一个测试模块',
			'package'    => 'option',
		);
		
		$this->assertTrue($this->DevModel->createMod($modCfg));

		// 添加控制器
		$mod = array(
			'mod' => self::MOD_DIR,
			'ctl' => 'Demo',
		);
		
		$ctlFile = $this->DevModel->addCtl($mod);
		$this->assertTrue((bool)$ctlFile);
		$this->assertTrue(is_file($ctlFile));

		// 添加控制器
		$mod = array(
			'mod' => self::MOD_DIR,
			'ctl' => 'Demo2',
			'name' => '测试控制器',
			'desc' => '这是一个测试控制器',
			'copyright' => 'Copyright (c) 2008-2013 Windwork Team.',
		);
		
		$ctlFile = $this->DevModel->addCtl($mod);
		$this->assertTrue((bool)$ctlFile);
		$this->assertTrue(is_file($ctlFile));
		
		// 添加模型
		$modelData = array(
			'mod' => self::MOD_DIR,
			'model' => 'DemoModel',
		);
		
		$this->assertTrue($this->DevModel->addModel($modelData));

		// 添加模型
		$modelData = array(
			'mod' => self::MOD_DIR,
			'model' => 'Demo2Model',
			'table' => 'Demo2Table',
			'db_table' => 'wk_demo',
		);
		
		$this->assertTrue($this->DevModel->addModel($modelData));
	}
}

