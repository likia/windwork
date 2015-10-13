<?php
use \core\util\Form;

require_once 'src\test\phpunit.php';
require_once 'src\core\util\form.php';
require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Form test case.
 */
class FormTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Form
	 */
	private $Form;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated FormTest::setUp()
		
		$this->Form = new Form(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated FormTest::tearDown()
		$this->Form = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Form->addElement()
	 */
	public function testAddElement() {
		// TODO Auto-generated FormTest->testAddElement()
		$this->markTestIncomplete ( "addElement test not implemented" );
		
		$this->Form->addElement(/* parameters */);
	}
	
	/**
	 * Tests Form->addElements()
	 */
	public function testAddElements() {
		// TODO Auto-generated FormTest->testAddElements()
		$this->markTestIncomplete ( "addElements test not implemented" );
		
		$this->Form->addElements(/* parameters */);
	}
		
	/**
	 * Tests Form->makeForm()
	 */
	public function testMakeForm() {
		// TODO Auto-generated FormTest->testMakeForm()
		$this->markTestIncomplete ( "makeForm test not implemented" );
		
		$this->Form->makeForm(/* parameters */);
	}
	
	/**
	 * Tests Form->setAction()
	 */
	public function testSetAction() {
		// TODO Auto-generated FormTest->testSetAction()
		$this->markTestIncomplete ( "setAction test not implemented" );
		
		$this->Form->setAction(/* parameters */);
	}
	
	/**
	 * Tests Form->setEnctype()
	 */
	public function testSetEnctype() {
		// TODO Auto-generated FormTest->testSetEnctype()
		$this->markTestIncomplete ( "setEnctype test not implemented" );
		
		$this->Form->setEnctype(/* parameters */);
	}
	
	/**
	 * Tests Form->setId()
	 */
	public function testSetId() {
		// TODO Auto-generated FormTest->testSetId()
		$this->markTestIncomplete ( "setId test not implemented" );
		
		$this->Form->setId(/* parameters */);
	}
	
	/**
	 * Tests Form->setMethod()
	 */
	public function testSetMethod() {
		// TODO Auto-generated FormTest->testSetMethod()
		$this->markTestIncomplete ( "setMethod test not implemented" );
		
		$this->Form->setMethod(/* parameters */);
	}
	
	/**
	 * Tests Form->setTarget()
	 */
	public function testSetTarget() {
		// TODO Auto-generated FormTest->testSetTarget()
		$this->markTestIncomplete ( "setTarget test not implemented" );
		
		$this->Form->setTarget(/* parameters */);
	}
	
	/**
	 * Tests Form->setAppend()
	 */
	public function testSetAppend() {
		// TODO Auto-generated FormTest->testSetAppend()
		$this->markTestIncomplete ( "setAppend test not implemented" );
		
		$this->Form->setAppend(/* parameters */);
	}
	
	/**
	 * Tests Form->setHasFormTag()
	 */
	public function testSetHasFormTag() {
		// TODO Auto-generated FormTest->testSetHasFormTag()
		$this->markTestIncomplete ( "setHasFormTag test not implemented" );
		
		$this->Form->setHasFormTag(/* parameters */);
	}
	
	/**
	 * Tests Form::arrValueFormat()
	 */
	public function testArrValueFormat() {
		// TODO Auto-generated FormTest::testArrValueFormat()
		$this->markTestIncomplete ( "arrValueFormat test not implemented" );
		
		Form::arrValueFormat(/* parameters */);
	}
	
	/**
	 * Tests Form->setIsMakeDiv()
	 */
	public function testSetIsMakeDiv() {
		// TODO Auto-generated FormTest->testSetIsMakeDiv()
		$this->markTestIncomplete ( "setIsMakeDiv test not implemented" );
		
		$this->Form->setIsMakeDiv(/* parameters */);
	}
}

