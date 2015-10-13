<?php

require_once 'test\phpunit.php';
require_once 'core/mvc/template.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Template test case.
 */
class TemplateTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Template
	 */
	private $Template;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated TemplateTest::setUp()
		
		$this->Template = new \core\mvc\Template(/* parameters */);

		$this->Template->setCompileId('test')
		->setTplDir(SRC_PATH.'test/data/template/default')
		->setCompiledDir(SRC_PATH.'test/data/compiled');
		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated TemplateTest::tearDown()
		$this->Template = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Template->__set()
	 */
	public function test__set() {
		// TODO Auto-generated TemplateTest->test__set()
		$this->markTestIncomplete ( "__set test not implemented" );
		
		$this->Template->__set(/* parameters */);
	}
	
	/**
	 * Tests Template->__get()
	 */
	public function test__get() {
		// TODO Auto-generated TemplateTest->test__get()
		$this->markTestIncomplete ( "__get test not implemented" );
		
		$this->Template->__get(/* parameters */);
	}
	
	/**
	 * Tests Template->setTplDir()
	 */
	public function testSetTplDir() {
		// TODO Auto-generated TemplateTest->testSetTplDir()
		$this->markTestIncomplete ( "setTplDir test not implemented" );
		
		$this->Template->setTplDir(/* parameters */);
	}
	
	/**
	 * Tests Template->setForceCompile()
	 */
	public function testSetForceCompile() {
		// TODO Auto-generated TemplateTest->testSetForceCompile()
		$this->markTestIncomplete ( "setForceCompile test not implemented" );
		
		$this->Template->setForceCompile(/* parameters */);
	}
	
	/**
	 * Tests Template->setMergeCompile()
	 */
	public function testSetMergeCompile() {
		// TODO Auto-generated TemplateTest->testSetMergeCompile()
		$this->markTestIncomplete ( "setMergeCompile test not implemented" );
		
		$this->Template->setMergeCompile(/* parameters */);
	}
	
	/**
	 * Tests Template->setCompiledDir()
	 */
	public function testSetCompiledDir() {
		// TODO Auto-generated TemplateTest->testSetCompiledDir()
		$this->markTestIncomplete ( "setCompiledDir test not implemented" );
		
		$this->Template->setCompiledDir(/* parameters */);
	}
	
	/**
	 * Tests Template->setCompileId()
	 */
	public function testSetCompileId() {
		// TODO Auto-generated TemplateTest->testSetCompileId()
		$this->markTestIncomplete ( "setCompileId test not implemented" );
		
		$this->Template->setCompileId(/* parameters */);
	}
	
	/**
	 * Tests Template->assign()
	 */
	public function testAssign() {
		// TODO Auto-generated TemplateTest->testAssign()
		$this->markTestIncomplete ( "assign test not implemented" );
		
		$this->Template->assign(/* parameters */);
	}
	
	/**
	 * Tests Template->getVar()
	 */
	public function testGetVar() {
		// TODO Auto-generated TemplateTest->testGetVar()
		$this->markTestIncomplete ( "getVar test not implemented" );
		
		$this->Template->getVar(/* parameters */);
	}
	
	/**
	 * Tests Template->getVars()
	 */
	public function testGetVars() {
		// TODO Auto-generated TemplateTest->testGetVars()
		$this->markTestIncomplete ( "getVars test not implemented" );
		
		$this->Template->getVars(/* parameters */);
	}
	
	/**
	 * Tests Template->render()
	 */
	public function testRender() {
		
		$this->Template->assign('var', '测试变量。。。。。。');
		$this->Template->assign('arr', array('11111111', '2222222222222222'));
		
		$this->Template->render('test.html');
		
		ob_flush();
	}
	
	/**
	 * Tests Template->renderJs()
	 */
	public function testRenderJs() {
		// TODO Auto-generated TemplateTest->testRenderJs()
		$this->markTestIncomplete ( "renderJs test not implemented" );
		
		$this->Template->renderJs(/* parameters */);
	}
	
	/**
	 * Tests Template->quote()
	 */
	public function testquote() {
		// TODO Auto-generated TemplateTest->testquote()
		$this->markTestIncomplete ( "quote test not implemented" );
		
		$this->Template->quote(/* parameters */);
	}
	
	/**
	 * Tests Template->url()
	 */
	public function testUrl() {
		// TODO Auto-generated TemplateTest->testUrl()
		$this->markTestIncomplete ( "url test not implemented" );
		
		$this->Template->url(/* parameters */);
	}
}

