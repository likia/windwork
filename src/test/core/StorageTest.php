<?php

require_once 'src\test\phpunit.php';
require_once 'src\core\Storage.php';
require_once 'PHPUnit\Framework\TestCase.php';

use core\Storage;
use core\App;
use core\Url;

/**
 * Storage test case.
 */
class StorageTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Storage
	 */
	private $Storage;
	
	private $revert = array();
		
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
				
		chdir(dirname(dirname(dirname(__FILE__))));

		Url::$isFull = false;
		Url::$isRewrite = false;
		Url::$locale = '';
		
		$this->Storage = Storage::getInstance(uniqid());

		$this->revert['isFullUrl']  = core\Url::$isFull;
		$this->revert['dir']        = $this->Storage->getStorageDir();
		$this->revert['siteUrl']    = $this->Storage->getSiteUrl();
		$this->revert['isRewrite']  = core\Url::$isRewrite;
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {

		core\Url::$isFull = $this->revert['isFullUrl'];
		$this->Storage->setStorageDir($this->revert['dir']);
		$this->Storage->setSiteUrl($this->revert['siteUrl']);
		core\Url::$isRewrite = $this->revert['isRewrite'];
		$this->Storage = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests $this->Storage->isStatic()
	 */
	public function testIsStatic() {
		$this->Storage->setStorageDir('');
		$r = $this->Storage->isStatic();
		$this->assertNotEmpty($r, 1);

		$this->Storage->setStorageDir('stor');
		$r = $this->Storage->isStatic();
		$this->assertNotEmpty($r, 2);

		
		$Storage = Storage::getInstance('mmc');
		$Storage->setStorageDir('mmc://localhost/');
		$r = $Storage->isStatic();
		$this->assertEmpty($r, 3);
		
		$this->Storage->setStorageDir('http://db.com/');
		$r = $this->Storage->isStatic();
		$this->assertNotEmpty($r);
	}
	
	
	/**
	 * Tests $this->Storage->getThumbUrl()
	 */
	public function testGetThumbUrl() {
		$surl = $this->Storage->getSiteUrl();		
		core\Url::$isFull = 0;
		
		$this->Storage->setSiteUrl('storage');
		$imgId = 112;
		$width = 100;
		$height = 90;
		
		$r = $this->Storage->getThumbUrl($imgId, $width, $height);	
		$exp = 'storage/'. $this->Storage->getThumbPath($imgId, $width, $height);
		
		$this->assertEquals($exp, $r, $r);
		
		$this->Storage->setStorageDir('saekv://localhost:8089/storage/aa');
		
		$this->Storage->setSiteUrl('storage/aa');
		$r = $this->Storage->getThumbUrl($imgId, $width, $height);
		$exp = 'storage/aa/'.$this->Storage->getThumbPath($imgId, $width, $height);
		
		$this->assertEquals($exp, $r, "$exp != $r");
		
		
		$testSiteUrl = 'http://my.site.com/storage';
		$this->Storage->setSiteUrl($testSiteUrl);
		$r = $this->Storage->getThumbUrl($imgId, $width, $height);
		$exp = $testSiteUrl.'/'.$this->Storage->getThumbPath($imgId, $width, $height);
		$this->assertEquals($exp, $r, $r);
		
		$this->Storage->setSiteUrl($surl);

	}
	
	/**
	 * Tests $this->Storage->getAvatarUrl()
	 */
	public function testGetAvatarUrl() {
		// TODO Auto-generated StorageTest::testGetAvatarUrl()
		$this->markTestIncomplete ( "getAvatarUrl test not implemented" );
		
		$this->Storage->getAvatarUrl(/* parameters */);
	}
	
	/**
	 * Tests $this->Storage->getUrl()
	 */
	public function testGetUrl() {
		$surl = $this->Storage->getSiteUrl();
		
		$r = $this->Storage->getUrl('thumb/11/22/199-small.jpg');
		$expected = 'storage/thumb/11/22/199-small.jpg';
		$this->assertEquals($expected, $r, $r);

		$this->Storage->setSiteUrl(basename(PHP_SELF).'?storage');
		$this->Storage->setStorageDir('saekv://storage');
		$r = $this->Storage->getUrl('thumb/11/22/199-small.jpg');
		$expected = basename(PHP_SELF).'?storage/thumb/11/22/199-small.jpg';
		$this->assertEquals($expected, $r, $r);
		
		$this->Storage->setSiteUrl($surl);
	}
	
	/**
	 * Tests $this->Storage->getFullUrl()
	 */
	public function testGetFullUrl() {
		Url::$isRewrite = 0;
		$r = $this->Storage->getFullUrl('thumb/11/22/199-small.jpg');
		$expected = 'http://localhost/storage/thumb/11/22/199-small.jpg';
		$this->assertEquals($expected, $r, $r);

		Url::$isRewrite = 0;
		$this->Storage->setSiteUrl('http://localhost/'.basename(PHP_SELF).'?storage');
		$r = $this->Storage->getFullUrl('thumb/11/22/199-small.jpg');
		$expected = 'http://localhost/'.basename(PHP_SELF).'?storage/thumb/11/22/199-small.jpg';
		$this->assertEquals($expected, $r, $r);
		
	}
	
	/**
	 * Tests $this->Storage->getRealPath()
	 */
	public function testGetRealPath() {
		$this->Storage->setStorageDir('storage');
		$r = $this->Storage->getRealPath('test/mx.jpg');
		$expected = 'storage/test/mx.jpg';
		$this->assertEquals($expected, $r, $r);	
	}
	
	/**
	 * Tests $this->Storage->removeThumb()
	 */
	public function testRemoveThumb() {
		$this->Storage->setStorageDir('storage');
		$path = $this->Storage->getThumbPath(1, 1, 1);
		$file = $this->Storage->getRealPath($path);
		$dir  = dirname($file);
				
		if(!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		
		file_put_contents($file, 'xxxx');
		$this->assertNotEmpty(is_file($file));
		
		$r = $this->Storage->removeThumb(1);

		$this->assertEmpty(is_file($file));
		
	}
	
	/**
	 * Tests $this->Storage->clearThumb()
	 */
	public function testClearThumb() {
		$p = $this->Storage->getThumbPath(256, 4, 4);
		$f = $this->Storage->getRealPath($p);
		
		is_dir(dirname($f)) || mkdir(dirname($f), 0755, 1);
		file_put_contents($f, 'xxx');
		
		$f = $f.'.thumb.jpg';
		file_put_contents($f, 'xxx');
		
		$this->assertNotEmpty(file_get_contents($f));		
		$this->Storage->clearThumb();
		
	}
	
	/**
	 * Tests $this->Storage->getThumbPath()
	 */
	public function testGetThumbPath() {
		// TODO Auto-generated StorageTest::testGetThumbPath()
		$this->markTestIncomplete ( "getThumbPath test not implemented" );
		
		$this->Storage->getThumbPath(/* parameters */);
	}
	
	/**
	 * Tests $this->Storage->load()
	 */
	public function testLoad() {
		// TODO Auto-generated StorageTest::testLoad()
		$this->markTestIncomplete ( "load test not implemented" );
		
		$this->Storage->load(/* parameters */);
	}
	
	/**
	 * Tests $this->Storage->safePath()
	 */
	public function testSafePath() {

		$r = $this->Storage->safePath('../../');
		$exp = '././';
		$this->assertEquals($exp, $r, $r);

		$r = $this->Storage->safePath('............./................../................../');
		$exp = './././';
		$this->assertEquals($exp, $r, $r);

		$r = $this->Storage->safePath('............');
		$exp = '.';
		$this->assertEquals($exp, $r, $r);
	}
}

