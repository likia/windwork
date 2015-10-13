<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'test/phpunit.php';


use user\model\RoleModel;

/**
 * RoleModel test case.
 */
class RoleTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var RoleModel
	 */
	private $RoleModel;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated RoleTest::setUp()
		
		$this->RoleModel = new RoleModel(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated RoleTest::tearDown()
		$this->RoleModel = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	public function testRole() {
		// 添加角色
		$roleObj = new RoleModel();
		$roleObj->fromArray(array(
			'name' => 'test',
			'type' => 'member',
			'desc' => '测试角色',
			'disabled' => false,
			'displayorder' => 99,
		));
		//$roleObj->create();
		
		// 读取角色
		
		// 修改角色
		
		// 删除角色
	}
	
}

