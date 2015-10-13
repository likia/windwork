<?php

require_once 'src\test\phpunit.php';
require_once 'src\modules\article\model\articlecat.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Article test case.
 */
class ArticleTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var \module\article\model\Article
	 */
	private $Article;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();		
		$this->Article = new \module\article\model\ArticleModel();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->Article = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Article->create()
	 */
	public function testBiz() {
		$cat1 = array('title' => uniqid(), 'content' => uniqid(), 'parentid' => 0);
		$cat2 = array('title' => '', 'parentid' => 1);
		try {
			// validate err
			$catObj1 = new \module\article\model\ArticleModel();
			$obj = $catObj1->fromArray($cat2)->create();
			$this->assertFalse($obj);
			Test::trace($catObj1->getErrs());
			
			// 增加
		    $obj = $catObj1->fromArray($cat1)->create();
		    $this->assertTrue(false !== $obj);
		    $this->assertTrue($catObj1->isExist());
		    		    
		    // update
		    $catObj2 = new \module\article\model\ArticleModel();
		    $catObj2->setPkv($catObj1->getPkv())->load();
		    $title = uniqid();
		    $this->assertEquals($catObj1->title, $catObj2->title);
		    $catObj2->setTitle($title);
		    $catObj2->update();

		    
		    // 重新从数据库加载进行比较
		    $catObj3 = new \module\article\model\ArticleModel();
		    $catObj3->setPkv($catObj1->getPkv())->load();
		    
		    $this->assertNotEquals($catObj3->getTitle(), $catObj1->getTitle());
		    $this->assertEquals($catObj2->getTitle(), $catObj3->getTitle());
		    
		    // 删除		   
		    $catObj3->delete();
		    $this->assertFalse($catObj3->isExist());
		    
		} catch(Exception $e) {
			$this->assertFalse(1, $e->getMessage());
		}		
	}
	
}

