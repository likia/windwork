<?php

require_once 'src\test\phpunit.php';
require_once 'src\core\wrapper\db.php';

require_once 'PHPUnit\Framework\TestCase.php';

use \core\wrapper\DB;
/**
 * DB test case.
 */
class DBTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var DB
	 */
	private $DB;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated DBTest::setUp()
		
		$this->DB = new DB(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated DBTest::tearDown()
		$this->DB = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests DB::register()
	 */
	public function testRegister() {
		// TODO Auto-generated DBTest::testRegister()
		$this->markTestIncomplete ( "register test not implemented" );
		
		DB::register(/* parameters */);
	}
	
	/**
	 * Tests DB->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated DBTest->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		$this->DB->__construct(/* parameters */);
	}
	
	/**
	 * Tests DB->dir_closedir()
	 */
	public function testDir_closedir() {
		// TODO Auto-generated DBTest->testDir_closedir()
		$this->markTestIncomplete ( "dir_closedir test not implemented" );
		
		$this->DB->dir_closedir(/* parameters */);
	}
	
	/**
	 * Tests DB->dir_opendir()
	 */
	public function testDir_opendir() {
		// TODO Auto-generated DBTest->testDir_opendir()
		$this->markTestIncomplete ( "dir_opendir test not implemented" );
		
		$this->DB->dir_opendir(/* parameters */);
	}
	
	/**
	 * Tests DB->dir_readdir()
	 */
	public function testDir_readdir() {
		// TODO Auto-generated DBTest->testDir_readdir()
		$this->markTestIncomplete ( "dir_readdir test not implemented" );
		
		$this->DB->dir_readdir(/* parameters */);
	}
	
	/**
	 * Tests DB->dir_rewinddir()
	 */
	public function testDir_rewinddir() {
		// TODO Auto-generated DBTest->testDir_rewinddir()
		$this->markTestIncomplete ( "dir_rewinddir test not implemented" );
		
		$this->DB->dir_rewinddir(/* parameters */);
	}
	
	/**
	 * Tests DB->mkdir()
	 */
	public function testMkdir() {
		// TODO Auto-generated DBTest->testMkdir()
		$this->markTestIncomplete ( "mkdir test not implemented" );
		
		$this->DB->mkdir(/* parameters */);
	}
	
	/**
	 * Tests DB->rename()
	 */
	public function testRename() {
		// TODO Auto-generated DBTest->testRename()
		$this->markTestIncomplete ( "rename test not implemented" );
		
		$this->DB->rename(/* parameters */);
	}
	
	/**
	 * Tests DB->rmdir()
	 */
	public function testRmdir() {
		// TODO Auto-generated DBTest->testRmdir()
		$this->markTestIncomplete ( "rmdir test not implemented" );
		
		$this->DB->rmdir(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_cast()
	 */
	public function testStream_cast() {
		// TODO Auto-generated DBTest->testStream_cast()
		$this->markTestIncomplete ( "stream_cast test not implemented" );
		
		$this->DB->stream_cast(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_close()
	 */
	public function testStream_close() {
		// TODO Auto-generated DBTest->testStream_close()
		$this->markTestIncomplete ( "stream_close test not implemented" );
		
		$this->DB->stream_close(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_eof()
	 */
	public function testStream_eof() {
		// TODO Auto-generated DBTest->testStream_eof()
		$this->markTestIncomplete ( "stream_eof test not implemented" );
		
		$this->DB->stream_eof(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_flush()
	 */
	public function testStream_flush() {
		// TODO Auto-generated DBTest->testStream_flush()
		$this->markTestIncomplete ( "stream_flush test not implemented" );
		
		$this->DB->stream_flush(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_lock()
	 */
	public function testStream_lock() {
		// TODO Auto-generated DBTest->testStream_lock()
		$this->markTestIncomplete ( "stream_lock test not implemented" );
		
		$this->DB->stream_lock(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_open()
	 */
	public function testStream_open() {
		// TODO Auto-generated DBTest->testStream_open()
		$this->markTestIncomplete ( "stream_open test not implemented" );
		
		$this->DB->stream_open(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_read()
	 */
	public function testStream_read() {
		// TODO Auto-generated DBTest->testStream_read()
		$this->markTestIncomplete ( "stream_read test not implemented" );
		
		$this->DB->stream_read(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_seek()
	 */
	public function testStream_seek() {
		// TODO Auto-generated DBTest->testStream_seek()
		$this->markTestIncomplete ( "stream_seek test not implemented" );
		
		$this->DB->stream_seek(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_set_option()
	 */
	public function testStream_set_option() {
		// TODO Auto-generated DBTest->testStream_set_option()
		$this->markTestIncomplete ( "stream_set_option test not implemented" );
		
		$this->DB->stream_set_option(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_stat()
	 */
	public function testStream_stat() {
		// TODO Auto-generated DBTest->testStream_stat()
		$this->markTestIncomplete ( "stream_stat test not implemented" );
		
		$this->DB->stream_stat(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_tell()
	 */
	public function testStream_tell() {
		// TODO Auto-generated DBTest->testStream_tell()
		$this->markTestIncomplete ( "stream_tell test not implemented" );
		
		$this->DB->stream_tell(/* parameters */);
	}
	
	/**
	 * Tests DB->stream_write()
	 */
	public function testStream_write() {
		// TODO Auto-generated DBTest->testStream_write()
		$this->markTestIncomplete ( "stream_write test not implemented" );
		
		$this->DB->stream_write(/* parameters */);
	}
	
	/**
	 * Tests DB->unlink()
	 */
	public function testUnlink() {
		// TODO Auto-generated DBTest->testUnlink()
		$this->markTestIncomplete ( "unlink test not implemented" );
		
		$this->DB->unlink(/* parameters */);
	}
	
	/**
	 * Tests DB->url_stat()
	 */
	public function testUrl_stat() {
		// TODO Auto-generated DBTest->testUrl_stat()
		$this->markTestIncomplete ( "url_stat test not implemented" );
		
		$this->DB->url_stat(/* parameters */);
	}
}

