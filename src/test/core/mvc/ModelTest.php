<?php

use core\Factory;
use core\mvc\Model;

require_once 'src\test\phpunit.php';
require_once 'src\core\mvc\model.php';


/**
 * Model test case.
 */
class ModelTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var TestModel
	 */
	private $Model;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		//创建测试表
		Factory::db()->exec("
          CREATE TABLE IF NOT EXISTS `model_test` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
             `name` varchar(32) NOT NULL DEFAULT '',
             `pass` varchar(32) NOT NULL DEFAULT '',
             `other` varchar(64) NOT NULL DEFAULT '',
             PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$this->Model = new TestModel(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->Model = null;
		Factory::db()->exec("DROP TABLE IF EXISTS model_test");
		
		parent::tearDown ();
	}
		
	/**
	 * Tests Model->load()
	 */
	public function testRead() {
		$this->testCreate();
		
		$m = new TestModel();
		$m->setPkv($this->Model->getPkv())->load();

		
		$this->assertEquals($this->Model->getUserPass(), $m->getUserPass());
		
		return $m;
	}
	
	/**
	 * Tests Model->update()
	 */
	public function testUpdate() {
		// 创建
		$this->testCreate();
		
		$m = $this->Model;	
		$s1 = $m->getUserPass();
		
		// 修改并保存
		$s2 = uniqid();
		$other = uniqid();
		$m->setUserPass($s2)->setOther($other);
		$m->update();	
		
		// 从持久层取出
		$r = new TestModel();
		$r->setPkv($m->getPkv())->load();
		$s3 = $r->getUserPass();
		
		$this->assertNotEquals($s1, $s2);
		$this->assertEquals($s2, $s3);
	}
	
	/**
	 * Tests Model->delete()
	 */
	public function testDelete() {
		$id = $this->testCreate();
		$obj = $this->Model;
		
		$obj->setPkv($id);
		
		$this->assertNotEmpty($obj->load());
		
		$obj->delete();
		
		$this->assertEmpty($obj->load());
	}
	
	/**
	 * Tests Model->create()
	 */
	public function testCreate() {
		$m = new TestModel();
		
		$data = array('name' => 'cm', 'pass' => uniqid());
		$m->fromArray($data);
		
		$m->create();
		
		$this->assertNotEmpty($m->getPkv());
		
		$this->Model = $m;
		
		return $m->getPkv();
	}
	
	/**
	 * Tests Model->count()
	 */
	public function testGetTotals() {
		// 1
		$this->testCreate();		
		$r = $this->Model->count(/* parameters */);

		$this->assertEquals(1, $r);

		// 2
		$this->testCreate();
		$r = $this->Model->count(/* parameters */);
		
		$this->assertEquals(2, $r);

		// 3
		$data = $this->testCreate();
		$whereArr = array('and', array('id', $data));
		$r = $this->Model->count($whereArr);
		
		$this->assertEquals(3, $r);
		
	}
	
	/**
	 * Tests Model->select()
	 */
	public function testSelect() {
		$this->testCreate();		
		$this->testCreate();		
		$this->testCreate();
		
		$cdt = array();
		$r = $this->Model->select($cdt);
		
		
		$this->assertEquals(3, count($r));

		$id = $this->testCreate();
		$whereArr = array('and', array('id', $id));
		$cdt = array(
			'where' => $whereArr,
			'order' => 'id desc',
		);
		
		$r = $this->Model->select($cdt, 0, 100);
		$this->assertEquals(1, count($r));		
	}
	
	public function testFromArray() {
		$array = array('name' => 'xxx', 'pass' => 'yyyy');
		$this->Model->fromArray($array);
		
		$this->assertEquals($array['pass'], $this->Model->getUserPass());
		
	}
}



Factory::logger()->setLogDir(SRC_PATH.'data/log/');


class TestModel extends Model {
	protected $table = 'model_test';
	protected $pk = 'id';
	
	protected $fieldMap = array(
		'id'   => 'id',
		'name' => 'userName',
		'pass' => 'userPass'
	);

	protected $userName = '';
	protected $userPass = '';

	public function setUserName($name) {
		$this->userName = $name;
		return $this;
	}
	public function setUserPass($pw) {
		$this->userPass = $pw;
		return $this;
	}

	public function getUserPass() {
		return $this->userPass;
	}


}

