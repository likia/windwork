<?php
require "test/phpunit.php";

require_once 'core/adapter/IFactoryAble.php';
require_once 'core/adapter/db/IDB.php';
require_once 'core/adapter/db/ADB.php';
require_once 'core/adapter/db/MySQL.php';
require_once 'PHPUnit/Framework/TestCase.php';


use \core\adapter\db\MySQL;

/**
 * MySQL test case.
 */
class MySQLTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var MySQL
	 */
	private $MySQL;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated MySQLTest::setUp()
		$cfg = include 'config/config.php';
		$this->MySQL = new MySQL($cfg);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated MySQLTest::tearDown()
		$this->MySQL = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests MySQL->beginTransaction()
	 */
	public function testBeginTransaction() {		
		try {
		    $this->MySQL->beginTransaction(/* parameters */);
			$result = true;
			try {
				$this->MySQL->beginTransaction(/* parameters */);
				$sql = "insert into test (txt) VALUE ('inner')";
				$this->MySQL->query($sql);
				$this->MySQL->commit();
			} catch (\core\Exception $e) {
				$this->MySQL->rollBack();
			}

			$sql = "insert into test (txt) VALUE ('outer')";
			$this->MySQL->query($sql);
			$this->MySQL->commit();
			
		} catch (\core\Exception $e) {			
			$this->MySQL->rollBack();
		}
		
		$this->assertTrue($result);
	}
	
	/**
	 * Tests MySQL->rollBack()
	 */
	public function testRollBack() {
		// TODO Auto-generated MySQLTest->testRollBack()
		$this->markTestIncomplete ( "rollBack test not implemented" );
		
		$this->MySQL->rollBack(/* parameters */);
	}
	
	/**
	 * Tests MySQL->commit()
	 */
	public function testCommit() {
		// TODO Auto-generated MySQLTest->testCommit()
		$this->markTestIncomplete ( "commit test not implemented" );
		
		$this->MySQL->commit(/* parameters */);
	}
	
	/**
	 * Tests MySQL->setAutoCommit()
	 */
	public function testSetAutoCommit() {
		// TODO Auto-generated MySQLTest->testSetAutoCommit()
		$this->markTestIncomplete ( "setAutoCommit test not implemented" );
		
		$this->MySQL->setAutoCommit(/* parameters */);
	}
	
	/**
	 * Tests MySQL->lastInsertId()
	 */
	public function testLastInsertId() {
		// TODO Auto-generated MySQLTest->testLastInsertId()
		$this->markTestIncomplete ( "lastInsertId test not implemented" );
		
		$this->MySQL->lastInsertId(/* parameters */);
	}
	
	/**
	 * Tests MySQL->query()
	 */
	public function testQuery() {
		// TODO Auto-generated MySQLTest->testQuery()
		$this->markTestIncomplete ( "query test not implemented" );
		
		$this->MySQL->query(/* parameters */);
	}
	
	/**
	 * Tests MySQL->exec()
	 */
	public function testExec() {
		// TODO Auto-generated MySQLTest->testExec()
		$this->markTestIncomplete ( "exec test not implemented" );
		
		$this->MySQL->exec(/* parameters */);
	}
	
	/**
	 * Tests MySQL->getAll()
	 */
	public function testGetAll() {
		// TODO Auto-generated MySQLTest->testGetAll()
		$this->markTestIncomplete ( "getAll test not implemented" );
		
		$this->MySQL->getAll(/* parameters */);
	}
	
	/**
	 * Tests MySQL->getRow()
	 */
	public function testGetRow() {
		// TODO Auto-generated MySQLTest->testGetRow()
		$this->markTestIncomplete ( "getRow test not implemented" );
		
		$this->MySQL->getRow(/* parameters */);
	}
	
	/**
	 * Tests MySQL->getOne()
	 */
	public function testGetOne() {
		// TODO Auto-generated MySQLTest->testGetOne()
		$this->markTestIncomplete ( "getOne test not implemented" );
		
		$this->MySQL->getOne(/* parameters */);
	}
	
	/**
	 * Tests MySQL->getLastErr()
	 */
	public function testGetLastErr() {
		// TODO Auto-generated MySQLTest->testGetLastErr()
		$this->markTestIncomplete ( "getLastErr test not implemented" );
		
		$this->MySQL->getLastErr(/* parameters */);
	}
	
	/**
	 * Tests MySQL->__destruct()
	 */
	public function test__destruct() {
		// TODO Auto-generated MySQLTest->test__destruct()
		$this->markTestIncomplete ( "__destruct test not implemented" );
		
		$this->MySQL->__destruct(/* parameters */);
	}
}

