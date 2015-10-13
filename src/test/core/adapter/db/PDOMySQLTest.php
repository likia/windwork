<?php
require "test/phpunit.php";

require_once 'core/adapter/IFactoryAble.php';
require_once 'core/adapter/db/IDB.php';
require_once 'core/adapter/db/ADB.php';
require_once 'core/adapter/db/PDOMySQL.php';
require_once 'PHPUnit/Framework/TestCase.php';


use \core\adapter\db\PDOMySQL;

/**
 * PDOMySQL test case.
 */
class PDOMySQLTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var PDOMySQL
	 */
	private $PDOMySQL;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated PDOMySQLTest::setUp()
		$cfg = include 'config/config.php';
		$this->PDOMySQL = new PDOMySQL($cfg);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated PDOMySQLTest::tearDown()
		$this->PDOMySQL = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests PDOMySQL->beginTransaction()
	 */
	public function testBeginTransaction() {
		try {
		    $this->PDOMySQL->beginTransaction(/* parameters */);
			
			try {
				$this->PDOMySQL->beginTransaction(/* parameters */);
				$sql = "insert into test (txt) VALUE ('inner1')";
				$this->PDOMySQL->query($sql);
				$sql = "insert into test (txt) VALUE ('inner2')";
				$this->PDOMySQL->query($sql);
				$lastId = $this->PDOMySQL->lastInsertId();
				
				$sql = "insert into test (txt) VALUE ('inner3')";
				$this->PDOMySQL->query($sql);
				$this->PDOMySQL->commit();
			} catch (\core\Exception $e) {
				$this->PDOMySQL->rollBack();
			}

			$sql = "insert into test (txt) VALUE ('outer')";
			$this->PDOMySQL->query($sql);
			$this->PDOMySQL->commit();
		} catch (\core\Exception $e) {
			$this->PDOMySQL->rollBack();
		}
	}
	
	/**
	 * Tests PDOMySQL->rollBack()
	 */
	public function testRollBack() {
		// TODO Auto-generated PDOMySQLTest->testRollBack()
		$this->markTestIncomplete ( "rollBack test not implemented" );
		
		$this->PDOMySQL->rollBack(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->commit()
	 */
	public function testCommit() {
		// TODO Auto-generated PDOMySQLTest->testCommit()
		$this->markTestIncomplete ( "commit test not implemented" );
		
		$this->PDOMySQL->commit(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->setAutoCommit()
	 */
	public function testSetAutoCommit() {
		// TODO Auto-generated PDOMySQLTest->testSetAutoCommit()
		$this->markTestIncomplete ( "setAutoCommit test not implemented" );
		
		$this->PDOMySQL->setAutoCommit(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->lastInsertId()
	 */
	public function testLastInsertId() {
		// TODO Auto-generated PDOMySQLTest->testLastInsertId()
		$this->markTestIncomplete ( "lastInsertId test not implemented" );
		
		$this->PDOMySQL->lastInsertId(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->query()
	 */
	public function testQuery() {
		// TODO Auto-generated PDOMySQLTest->testQuery()
		$this->markTestIncomplete ( "query test not implemented" );
		
		$this->PDOMySQL->query(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->exec()
	 */
	public function testExec() {
		// TODO Auto-generated PDOMySQLTest->testExec()
		$this->markTestIncomplete ( "exec test not implemented" );
		
		$this->PDOMySQL->exec(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->getAll()
	 */
	public function testGetAll() {
		// TODO Auto-generated PDOMySQLTest->testGetAll()
		$this->markTestIncomplete ( "getAll test not implemented" );
		
		$this->PDOMySQL->getAll(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->getRow()
	 */
	public function testGetRow() {
		// TODO Auto-generated PDOMySQLTest->testGetRow()
		$this->markTestIncomplete ( "getRow test not implemented" );
		
		$this->PDOMySQL->getRow(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->getOne()
	 */
	public function testGetOne() {
		// TODO Auto-generated PDOMySQLTest->testGetOne()
		$this->markTestIncomplete ( "getOne test not implemented" );
		
		$this->PDOMySQL->getOne(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->getLastErr()
	 */
	public function testGetLastErr() {
		// TODO Auto-generated PDOMySQLTest->testGetLastErr()
		$this->markTestIncomplete ( "getLastErr test not implemented" );
		
		$this->PDOMySQL->getLastErr(/* parameters */);
	}
	
	/**
	 * Tests PDOMySQL->__destruct()
	 */
	public function test__destruct() {
		// TODO Auto-generated PDOMySQLTest->test__destruct()
		$this->markTestIncomplete ( "__destruct test not implemented" );
		
		$this->PDOMySQL->__destruct(/* parameters */);
	}
}

