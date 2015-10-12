<?php

use system\model\MenuModel;

require_once 'src\test\unittestinit.php';
require_once 'src\modules\system\model\menu.php';
require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Menu test case.
 */
class MenuTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var \module\system\model\Menu
	 */
	private $Menu;
	
	private $newIds = array();
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->Menu = new MenuModel(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		foreach ($this->newIds as $id) {
			$this->Menu->setObjId($id)->delete();
		}
		
		$this->Menu = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests crud
	 */
	public function testMenu() {
		// read
		$menus = $this->Menu->getTree();
		$count = count($menus);
		
		// create
		$items = array(
			array(
				'name' => 'test',
				'upid' => 0,
				'url'  => 'system.admin.admincp.index',
				'dislayorder' => '1',
		    ),
			array(
				'name' => 'test2',
				'upid' => 2,
				'url'  => 'system.admin.admincp.index2',
				'dislayorder' => '2',
		    )
		);
		
		foreach ($items as $item) {
			$this->Menu->fromArray($item)->create();
			$this->newIds[] = $this->Menu->getObjId();
		}
				
		// read
		$menus = $this->Menu->getTree();
		$this->assertEquals(count($items), count($menus) - $count);
		
		// update
		$obj = new \module\system\model\MenuModel();
		$obj->setObjId($this->newIds[0])->load();
		$this->assertNotEquals($items[1]['name'], $obj->name);
		$this->assertNotEquals($items[1]['upid'], $obj->upid);
		$this->assertNotEquals($items[1]['url'], $obj->url);
		$this->assertNotEquals($items[1]['dislayorder'], $obj->dislayorder);
		
		$obj->fromArray($items[1])->update()->load();
		$this->assertEquals($items[1]['name'], $obj->name);
		$this->assertEquals($items[1]['upid'], $obj->upid);
		$this->assertEquals($items[1]['url'], $obj->url);
		$this->assertEquals($items[1]['dislayorder'], $obj->dislayorder);

		// sort
		$data = array($this->newIds[0] => 8, $this->newIds[1] => 12);
		foreach ($data as $key => $val) {
			$this->Menu->setObjId($key)->load();
			if($this->Menu->displayOrder == $val) {
				continue;
			}
			
			$this->Menu->setDisplayOrder($val)->update();
			$this->assertEquals($val, $obj->setObjId($key)->load()->displayorder);
		}
		
		// delete
		foreach ($this->newIds as $id) {
			$obj->setObjId($id)->delete();
		}

		$menus = $this->Menu->getTree();
		$this->assertEquals($count, count($menus));
		
	}
	
	/**
	 * Tests Menu->removeByMod()
	 */
	public function testRemoveByMod() {
		// TODO Auto-generated MenuTest->testRemoveByMod()
		$this->markTestIncomplete ( "removeByMod test not implemented" );
		
		$this->Menu->removeByMod(/* parameters */);
	}
	
	
}

