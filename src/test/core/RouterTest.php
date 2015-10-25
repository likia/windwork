<?php
require_once 'src/core/App.php';
require_once 'src/core/mvc/Router.php';

use \core\mvc\Router;

\core\App::getInstance();

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Router test case.
 */
class RouterTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Router
	 */
	private $Router;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated RouterTest::setUp()
		
		$this->Router = new Router(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated RouterTest::tearDown()
		$this->Router = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
		
	/**
	 * Tests Router->toUrl()
	 */
	public function testToUrl() {
		// TODO Auto-generated RouterTest->testToUrl()
		$this->markTestIncomplete ( "toUrl test not implemented" );
		
		$this->Router->toUrl(/* parameters */);
	}
	
	/**
	 * Tests Router->parseUrl()
	 */
	public function testParseUrl() {
		$opt = Router::$options;

		Router::$options['host_info'] = 'http://localhost';
		Router::$options['base_path'] = '/demo/';
		Router::$options['base_url'] = 'index.php?';
		Router::$options['url_rewrite'] = 0;
		Router::$options['url_rewrite_ext'] = '.html';

		$obj = clone $this->Router;
		$url = 'http://localhost/demo/index.php?m.c.a/sdds/a:aa/b:bb&zz=55&xx=111&yy=222#sddsds';
		$obj->parseUrl($url);
		$rUrl = $obj->toUrl(1);
		$this->assertEquals('http://localhost/demo/index.php?m.c.a/sdds/a:aa/b:bb/zz:55/xx:111/yy:222.html#sddsds', $rUrl);

		$obj = clone $this->Router;
		$url = 'http://localhost/demo/index.php?m.c.a/sdds/a:aa/b:bb/mod:mm/act:aa#sddsds';
		$obj->parseUrl($url);
		$rUrl = $obj->toUrl(1);
		$this->assertEquals('http://localhost/demo/index.php?mm.c.aa/sdds/a:aa/b:bb.html#sddsds', $rUrl);

		$obj = clone $this->Router;
		$url = 'http://localhost/demo/index.php?m.c.a/sdds/a:aa/b:bb&zz=55&xx=111&yy=222&mod=m2&ctl=c2#sddsds';
		$obj->parseUrl($url);
		$rUrl = $obj->toUrl(1);
		$this->assertEquals('http://localhost/demo/index.php?m2.c2.a/sdds/a:aa/b:bb/zz:55/xx:111/yy:222.html#sddsds', $rUrl);
		
		Router::$options['base_url'] = '';
		Router::$options['url_rewrite'] = 1;
		Router::$options['url_rewrite_ext'] = '';

		$obj = clone $this->Router;
		$url = 'http://localhost/demo/m.c.a/sdds/a:aa/b:bb';
		$obj->parseUrl($url);
		
		$rUrl = $obj->toUrl(1);
		$this->assertEquals($url, $rUrl);
		

		Router::$options['url_rewrite_ext'] = '.wk';
		$obj = clone $this->Router;
		$url = 'http://localhost/demo/m.c.a/sdds/a:aa/b:bb?zz=55&xx=111&yy=222#sddsds';
		$obj->parseUrl($url);
		$rUrl = $obj->toUrl(1);
		$this->assertEquals('http://localhost/demo/m.c.a/sdds/a:aa/b:bb/zz:55/xx:111/yy:222.wk#sddsds', $rUrl);
		
		Router::$options = $opt;
	}
	
	/**
	 * Tests Router::buildUrl()
	 */
	public function testBuildUrl() {
		$opt = Router::$options;
		
		$set = array(
			'host_info'       => 'http://www.yoursite.com', // http://www.yoursite.com
			'base_path'       => '/ctx/', // /ctx/
			'base_url'        => 'index.php?', // index.php? || empty string
	
			'locale'          => '', // empty as zh_CN
			'url_encode'      => 0, // URL是否进行编码
			'url_full'        => 0, // 是否使用完整URL
			'url_rewrite'     => 0, // 是否使用URL重写
			'url_rewrite_ext' => '.html', // URL重写后缀
			'default_mod'     => 'system', // 默认模块
			'default_ctl'     => 'default',  // 默认控制器
			'default_act'     => 'index',  // 默认action
		);
		Router::$options = &$set;
		
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2');
		$this->assertEquals('index.php?m.c.a/xxx/a:1/b:2.html', $url);

		$set['base_url'] = '';
		$set['url_rewrite'] = 1;
		
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2');
		$this->assertEquals('m.c.a/xxx/a:1/b:2.html', $url);
		
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2#xxxxx');
		$this->assertEquals('m.c.a/xxx/a:1/b:2.html#xxxxx', $url);
		
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2?r=rr&w=ww#xxxxx');
		$this->assertEquals('m.c.a/xxx/a:1/b:2/r:rr/w:ww.html#xxxxx', str_replace('&amp;', '&', $url));

		// test full url
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2', 1);
		$exp = "{$set['host_info']}{$set['base_path']}{$set['base_url']}m.c.a/xxx/a:1/b:2.html";
		$this->assertEquals($exp, $url);
		
		// url简短化

		Router::$rules['todo']  = 'm.c.a2/aboutUs/do:1/go:2';
		Router::$rules['about'] = 'm.c.a2/aboutUs';
		Router::$rules['short'] = 'm.c.a2';

		$rUrl = Router::buildUrl('m.c.a2');
		$this->assertEquals('short.html', $rUrl);
		
		$rUrl = Router::buildUrl('m.c.a2/aboutUs');
		$this->assertEquals('about.html', $rUrl);

		$rUrl = Router::buildUrl('m.c.a2/aboutUs/x/p1:1/p2:2');
		$this->assertEquals('about/x/p1:1/p2:2.html', $rUrl);

		$rUrl = Router::buildUrl('m.c.a2/aboutUs/do:1/go:2/p1:1/p2:2');
		$this->assertEquals('todo/p1:1/p2:2.html', $rUrl);

		//
		$set['url_rewrite'] = 0;
		$set['base_url'] = 'index.php?';
		$set['host_info'] = 'http://www.yoursite.com:8080';
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2', 1);
		$expUrl = "{$set['host_info']}{$set['base_path']}{$set['base_url']}m.c.a/xxx/a:1/b:2.html";
		$this->assertEquals($expUrl, $url);

		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2?aa=1&bb=2&cc=3');
		$expUrl = "index.php?m.c.a/xxx/a:1/b:2/aa:1/bb:2/cc:3.html";
		$this->assertEquals($expUrl, $url);

		$set['url_encode'] = 1;
		$url = Router::buildUrl('m.c.a/xx');
		$expUrl = "index.php?q_bS5jLmEveHg.html";
		$this->assertEquals($expUrl, $url);
		
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2?aa=1&bb=2#aa');
		$expUrl = "index.php?q_bS5jLmEveHh4L2E6MS9iOjIvYWE6MS9iYjoy.html#aa";
		$this->assertEquals($expUrl, $url);

		$set['url_rewrite'] = 1;
		$set['base_url'] = '';
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2?aa=1&bb=2#aa');
		$expUrl = "q_bS5jLmEveHh4L2E6MS9iOjIvYWE6MS9iYjoy.html#aa";
		$this->assertEquals($expUrl, $url);

		$set['url_rewrite'] = 0;
		$set['base_url'] = 'index.php?';
		
		$url = Router::buildUrl('m.c.a/xxx/a:1/b:2', 1);
		$expUrl = "http://www.yoursite.com:8080/ctx/index.php?q_bS5jLmEveHh4L2E6MS9iOjI.html";
		$this->assertEquals($expUrl, $url);

		$obj = clone $this->Router;
		$obj->parseUrl($url);
		$rUrl = $obj->toUrl();
		$this->assertEquals('index.php?q_bS5jLmEveHh4L2E6MS9iOjI.html', $rUrl);
		$set['url_encode'] = 0;
		$rUrl = $obj->toUrl();
		$this->assertEquals('index.php?m.c.a/xxx/a:1/b:2.html', $rUrl);
		
		Router::$options = $opt;
	}
}

