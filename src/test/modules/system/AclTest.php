<?php

use \core\Factory;

require_once 'src\test\unittestinit.php';
require_once 'src\module\system\model\acl.php';
require_once 'PHPUnit\Framework\TestCase.php';

class AcltableTest extends \module\system\table\AclTable {
	protected $table = 'test_acl';
}

class TAclModel extends \module\system\model\AclModel {
	protected $table = 'test_acl';
}

/**
 * Acl test case.
 */
class AclTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var TAclModel
	 */
	private $Acl;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$aclObj = new \module\user\model\AclModel();
		$tableName = $aclObj->getTableObj()->getTable();
		copyTable($tableName, 'test_acl');
		
		$this->Acl = new TAclModel(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated AclTest::tearDown()
		$this->Acl = null;

		Factory::db()->exec("DROP TABLE test_acl");
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Acl->add()
	 */
	public function testAdd() {

		
		//$this->Acl->add(/* parameters */);
	}
	
	/**
	 * Tests Acl->create()
	 */
	public function testCreate() {
		$array = array(
			'roid' => '1',
			'mod'  => 'system',
			'ctl'  => 'default',
			'act'  => 'don',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create(/* parameters */);
		if(false === $cr) {
			throw new Exception(var_export($this->Acl->getErrs(), 1));			
		}
		
		$this->Acl->load();

		//$this->assertEquals($array['roid'], $this->Acl->roid);
		//$this->assertEquals($array['mod'], $this->Acl->mod);
		//$this->assertEquals($array['ctl'], $this->Acl->ctl);
		//$this->assertEquals($array['act'], $this->Acl->act);
	}
	
	/**
	 * Tests Acl->update()
	 */
	public function testUpdate() {
		// TODO Auto-generated AclTest->testUpdate()
		$this->markTestIncomplete ( "update test not implemented" );
		
		$this->Acl->update(/* parameters */);
	}
	
	/**
	 * Tests Acl->getRoleAclsByMod()
	 */
	public function testGetRoleAclsByMod() {
		$array = array(
			'roid' => '1',
			'mod'  => 'system',
			'ctl'  => 'default',
			'act'  => 'don',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create();
		
		$r = $this->Acl->getRoleAclsByMod('system');
		$this->assertNotEmpty($r);
	}
	
	/**
	 * Tests Acl::removeByMod()
	 */
	public function testRemoveByMod() {
		$array = array(
			'roid' => '1',
			'mod'  => 'system',
			'ctl'  => 'default',
			'act'  => 'don',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create();
		
		$r = $this->Acl->getRoleAclsByMod('system');
		$this->assertNotEmpty($r);
		$this->Acl->removeByMod('system');

		$r = $this->Acl->getRoleAclsByMod('system');
		$this->assertEmpty($r);
	}
	
	/**
	 * Tests Acl->updateModAcl()
	 */
	public function testUpdateModAcl() {
		// TODO Auto-generated AclTest->testUpdateModAcl()
		$this->markTestIncomplete ( "updateModAcl test not implemented" );
		
		$this->Acl->updateModAcl(/* parameters */);
	}
	
	/**
	 * Tests Acl->isAccessable()
	 */
	public function testIsAccessable() {
		// TODO Auto-generated AclTest->testIsAccessable()
		$this->markTestIncomplete ( "isAccessable test not implemented" );
		
		$this->Acl->isAccessable(/* parameters */);
	}
	
	/**
	 * Tests Acl->getModRolesAcls()
	 */
	public function testGetModRolesAcls() {		
		$array = array(
			'roid' => '1',
			'mod'  => 'system',
			'ctl'  => 'default',
			'act'  => 'don',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create();

		$r = $this->Acl->getModRolesAcls($array['mod']);
		$this->assertTrue($r && 1 == count($r[$array['ctl']][$array['act']]));

		$array = array(
			'roid' => '2',
			'mod'  => 'system',
			'ctl'  => 'default',
			'act'  => 'don',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create();

		$r = $this->Acl->getModRolesAcls($array['mod']);
		$this->assertTrue($r && 2 == count($r[$array['ctl']][$array['act']]));
	}
	
	/**
	 * Tests Acl->getRolesAcls()
	 */
	public function testGetRolesAcls() {
			
		$array = array(
			'roid' => '1',
			'mod'  => 'm',
			'ctl'  => 'c',
			'act'  => 'a',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create();

		$r = $this->Acl->getRolesAcls(1);
		$this->assertTrue($r && 1 == count($r[$array['mod']][$array['ctl']]));

		$array = array(
			'roid' => '1',
			'mod'  => 'm',
			'ctl'  => 'c',
			'act'  => 'b',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create();		
		$r  = $this->Acl->getRolesAcls(1);
		$this->assertTrue($r && 2 == count($r[$array['mod']][$array['ctl']]));
		
		$array = array(
			'roid' => '1',
			'mod'  => 'm2',
			'ctl'  => 'c',
			'act'  => 'b',
		);
		
		$this->Acl->fromArray($array);
		$cr = $this->Acl->create();		
		$r  = $this->Acl->getRolesAcls(1);
		$this->assertTrue($r && 2 == count($r));
		
	}
	
}

