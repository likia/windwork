<?php

require_once 'src\test\unittestinit.php';
require_once 'src\module\article\model\articlecat.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * ArticleCat test case.
 */
class ArticleCatTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var \module\article\model\ArticleCat
	 */
	private $ArticleCat;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();		
		$this->ArticleCat = new \module\article\model\ArticleCatModel();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->ArticleCat = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests ArticleCat->create()
	 */
	public function testBiz() {
		$cat1 = array('name' => uniqid(), 'parentid' => 0);
		$cat2 = array('name' => '', 'parentid' => 1);
		$pk = array();
		try {
			// validate err
			$catObj1 = new \module\article\model\ArticleCatModel();
			$obj = $catObj1->fromArray($cat2)->create();
			$this->assertFalse($obj);
			Test::trace($catObj1->getErrs());
			
			// 增加
		    $obj = $catObj1->fromArray($cat1)->create();
		    $this->assertTrue(false !== $obj);
		    $this->assertTrue($catObj1->isExist());
		    		    
		    // update
		    $catObj2 = new \module\article\model\ArticleCatModel();
		    $catObj2->setObjId($catObj1->getObjId())->load();
		    $name = uniqid();
		    $this->assertEquals($catObj1->name, $catObj2->name);
		    $catObj2->setName($name);
		    $catObj2->update();
		    
		    // 重新从数据库加载进行比较
		    $catObj3 = new \module\article\model\ArticleCatModel();
		    $catObj3->setObjId($catObj1->getObjId())->load();
		    $this->assertNotEquals($catObj3->getName(), $catObj1->getName());
		    $this->assertEquals($catObj2->getName(), $catObj3->getName());
		    
		    // 删除		   
		    $catObj3->delete();
		    $this->assertFalse($catObj3->isExist());
		    
		} catch(Exception $e) {
			$this->assertFalse(1, $e->getMessage());
		}		
	}
	
}

