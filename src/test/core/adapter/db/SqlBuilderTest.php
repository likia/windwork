<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'src/core/Exception.php';
require_once 'src/core/Config.php';
require_once 'src/core/adapter/db/Exception.php';
require_once 'src/core/adapter/db/SqlBuilder.php';

use \core\adapter\db\SqlBuilder;

/**
 * SqlBuilder test case.
 */
class SqlBuilderTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var SqlBuilder
	 */
	private $SqlBuilder;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
	}
	
	/**
	 * Tests SqlBuilder::tablePrefix()
	 */
	public function testTablePrefix() {
		\core\Config::set('db_table_prefix', 'my_');
		$sql = "SELECT * FROM wk_tbname t1 LEFT JOIN wk_tb2 t2 ON t1.xx=t2.xx WHERE t1.wk_xx = 'wk_yy'";
		$ret = SqlBuilder::tablePrefix($sql);
		$exp = "SELECT * FROM my_tbname t1 LEFT JOIN my_tb2 t2 ON t1.xx=t2.xx WHERE t1.wk_xx = 'wk_yy'";
		$this->assertEquals($exp, $ret);
	}
	
	/**
	 * Tests SqlBuilder::quote()
	 */
	public function testQuote() {
		$ret = SqlBuilder::quote("abc'd\"e");
		$exp = "'abc\'d\\\"e'";
		$this->assertEquals($exp, $ret);
	}
	
	/**
	 * Tests SqlBuilder::quoteField()
	 */
	public function testQuoteField() {
		$ret = SqlBuilder::quoteField('db.tb.f2 fx');
		$exp = "`db`.`tb`.`f2` `fx`";
		$this->assertEquals($exp, $ret);
	}
	
	/**
	 * Tests SqlBuilder::quoteFields()
	 */
	public function testQuoteFields() {
		$ret = SqlBuilder::quoteFields('tb.a, db.tb.f2, tb.f3 fx');
		$exp = "`tb`.`a`,`db`.`tb`.`f2`,`tb`.`f3` `fx`";
		$this->assertEquals($exp, $ret);
	}
	
	/**
	 * Tests SqlBuilder::limit()
	 */
	public function testLimit() {
		$limit = SqlBuilder::limit(1);
		$this->assertEquals(' LIMIT 1', $limit);

		$limit = SqlBuilder::limit(0, 5);
		$this->assertEquals(' LIMIT 5', $limit);
		
		$limit = SqlBuilder::limit(1, 2);
		$this->assertEquals(' LIMIT 1, 2', $limit);
	}
	
	/**
	 * Tests SqlBuilder::order()
	 */
	public function testOrder() {
		$str = 'a asc, b desc';
		$ret = SqlBuilder::order($str);
		$this->assertEquals('`a` asc, `b` desc', $ret);

		$str = 't.x, tb.a asc, tb.b desc';
		$ret = SqlBuilder::order($str);
		$this->assertEquals('`t`.`x`, `tb`.`a` asc, `tb`.`b` desc', $ret);
	}
	
	/**
	 * Tests SqlBuilder::where()
	 */
	public function testWhere() {
		$ret = SqlBuilder::where('tb.a', 111);
		$exp = "`tb`.`a`='111'";
		$this->assertEquals($exp, trim($ret));
	}
	
	/**
	 * Tests SqlBuilder::whereArr()
	 */
	public function testWhereArr() {
		$whereArr = array(
			array('name', 'cm'),
			array('pass', '123456'),
		);
		$ret = SqlBuilder::whereArr($whereArr);
		$exp = "(`name`='cm' AND `pass`='123456')";
		$this->assertEquals($exp, trim($ret));
	}
	
	/**
	 * Tests SqlBuilder::format()
	 */
	public function testFormat() {
		// %t:表名； %a：字段名；  %n:数字值；%i：整形；%f：浮点型； %s：字符串值; %x:保留不处理
		$sql = "SELECT * FROM %t WHERE %a = %n and b = %i and c = %f and d = %s %x";
		$ret = SqlBuilder::format($sql, array('my_tb', 'name', '123456789012345x', 88, 1.2, 'test yes', 'AND xx IN(1, 2, 3)'));
		$exp = 'SELECT * FROM `my_tb` WHERE `name` = 123456789012345 and b = 88 and c = 1.2 and d = \'test yes\' AND xx IN(1, 2, 3)';
		$this->assertEquals($exp, $ret);
	}
}

