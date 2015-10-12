<?php
use core\Factory;
use module\user\model\UserModel;

require_once 'src\test\unittestinit.php';
require_once 'src\module\user\model\user.php';
require_once 'PHPUnit\Framework\TestCase.php';

class TUser extends \module\user\table\UserTable {
	protected $table = 'user_test';	
}


/**
 * User test case.
 */
class UserTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var \module\user\model\User
	 */
	private $User;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->User = new UserModel();
		$tableName = $this->User->getTableObj()->getTable();
		copyTable($tableName, 'user_test');
		$this->User->setTableObj(new TUser());
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated UserTest::tearDown()

		Factory::db()->exec("DROP TABLE user_test");
		$this->User = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	
	/**
	 * Tests User->create()
	 */
	public function testCreate() {
		$user = array(
			'uname' => 'cm',
			'password' => '123456',
			'email' => 'my@qq.com',
			'role' => 1
		);
		$this->User->fromArray($user);
		$r = $this->User->create(/* parameters */);
		if(false === $r) {
			Test::trace($this->User->getErrs());
		}
		
		Test::trace($this->User->toArray());
		
		$this->assertNotEmpty($r);
	}
	
	/**
	 * Tests User->read()
	 */
	public function testLoad() {
		$this->testCreate();
		$uid = $this->User->getObjId();
		
		$userObj = new UserModel();
		
		$userObj
		->setTableObj(new TUser())
		->setObjId($uid);
		
		$r = $userObj->load();
		Test::trace($r->toArray());
		$this->assertTrue((bool)$r);
	}
	
	/**
	 * Tests User->update()
	 */
	public function testUpdate() {
		$this->testCreate();
		$oldUser = $this->User;
		
		//TODO
		$userObj = clone $oldUser;
		$userObj->nickname = 'simon';
		$r = $userObj->update();
		
		$r || Test::trace($userObj->getErrs());

		$this->assertTrue((bool)$r);

		$loadObj = clone $oldUser; 
		$loadObj->load();

		$this->assertNotEquals((string)$oldUser->nickname, $userObj->nickname);
		$this->assertEquals($loadObj->nickname, $userObj->nickname);
	}
	
	/**
	 * Tests User->isUserNameExist()
	 */
	public function testIsUserNameExist() {
		$this->testCreate();
		$this->assertTrue($this->User->isUserNameExist('cm'));
		$this->assertFalse($this->User->isUserNameExist(uniqid()));
	}
	
	/**
	 * Tests User->isEmailExists()
	 */
	public function testIsEmailExists() {
		$email = "my@windwork.org";
		$this->testCreate();
		$this->User->email = $email;
		$this->User->update();
		$this->assertTrue($this->User->isEmailExists($email));
		$this->assertFalse($this->User->isEmailExists('tyhjk,.oijuhg@my.cn'));
	}
	
	/**
	 * Tests User->login()
	 */
	public function testLogin() {		
		$user = array(
			'uname' => 'cm',
			'password' => '123456',
			'email' => 'my@qq.com',
			'role' => 1
		);
		$obj = $this->User;
		$obj->fromArray($user)->create();

		$this->assertTrue($obj->login($user['uname'], $user['password'], 'uname'));
		$this->assertTrue($obj->login($user['email'], $user['password'], 'email'));
		$this->assertTrue($obj->login($obj->getObjId(),  $user['password'], 'uid'));

		$this->assertFalse($obj->login($user['uname'], 'xxxxxxx', 'uname'));
		$this->assertFalse($obj->login($user['email'], 'jjjjjjj', 'email'));
		$this->assertFalse($obj->login($obj->getObjId(),  'yyyyyyy', 'uid'));
		$this->assertFalse($obj->login('x@y.z', $user['password'], 'email'));
		$this->assertFalse($obj->login(99999,  $user['password'], 'uid'));
	}
	
	/**
	 * Tests User->getUsersByType()
	 */
	public function testGetUsersByType() {
	}
	
	/**
	 * Tests User->logout()
	 */
	public function testLogout() {
		
	}
	
	/**
	 * Tests User->getTotalUsersByType()
	 */
	public function testGetTotalUsersByType() {
		
	}
	
	/**
	 * Tests User::isSuper()
	 */
	public function testIsSuper() {
		
	}
	
	/**
	 * Tests Userodel::setLoginSession()
	 */
	public function testSetLoginSession() {
		
	}
	
		
	/**
	 * Tests User->deleteByUids()
	 */
	public function testDeleteByUids() {
		$this->testCreate();
		
		$this->assertTrue($this->User->isExist());		
		$this->User->deleteByUids(array(1, 2, 3));
		$this->assertFalse($this->User->isExist());
	}
	
	/**
	 * Tests User::getAvatarUrl()
	 */
	public function testGetAvatarUrl() {
		// TODO Auto-generated UserTest::testGetAvatarUrl()
		$this->markTestIncomplete ( "getAvatarUrl test not implemented" );
		
		UserModel::getAvatarUrl(/* parameters */);
	}
}

