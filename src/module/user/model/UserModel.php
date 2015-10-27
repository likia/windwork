<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\user\model;

use core\Config;
use core\util\Validator;
use core\Factory;
use core\Lang;

/**
 * 
 * @package     module.user.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class UserModel extends \core\mvc\Model {
	
	protected $table = 'wk_user';
	
	/**
	 * 用户类型：管理员
	 * @var string
	 */
	const USER_TYPE_ADMIN  = 'admin';
	
	/**
	 * 用户类型：扩展用户类型
	 * @var string
	 */
	const USER_TYPE_EXT = 'ext';

	/**
	 * 用户类型：普通会员
	 * @var string
	 */
	const USER_TYPE_MEMBER = 'member';
	
	/**
	 * 用户角色类型
	 * @var array
	 */
	public $userTypes = array(
		'member' => '会员',
		'ext'  => '商家', 
		'admin'  => '管理员',
	);
	
	public function __construct() {
		parent::__construct();
		$this->userTypes = array(
			'member' => Lang::get('user_type_member'),
			'ext'    => Lang::get('user_type_ext'),
			'admin'  => Lang::get('user_type_admin'),
		);
		
		$this->userStatus = array(
			0 => Lang::get('user_status_0'),
			1 => Lang::get('user_status_1'),
			2 => Lang::get('user_status_2'),
			3 => Lang::get('user_status_3'),
		);
	}

	/**
	 * 用户状态：未审核
	 * @var string
	 */
	const USER_STATUS_UNVALID   = 0;

	/**
	 * 用户状态：已审核
	 * @var string
	 */
	const USER_STATUS_VALID     = 1;

	/**
	 * 用户状态：禁止发言
	 * @var string
	 */
	const USER_STATUS_DEN_POST  = -1;

	/**
	 * 用户状态：禁止访问
	 * @var string
	 */
	const USER_STATUS_DEN_VISIT = -2;

	/**
	 * 用户状态
	 * @var array
	 */
	public $userStatus = array();
		
	/**
	 * 添加用户
	 * 
	 * @param array $user
	 */
	public function create() {
		// 检查用户类型
		if (empty($this->type) || !in_array($this->type, array_keys($this->userTypes))) {
			$this->type = 'member';
		}

		// 检查用户角色
		if(!$this->checkRole()) {
			$this->setErr('错误的用户角色！');
			return false;
		}
		
		// 输入验证
	    $validate = array(
			'email' => array(
				'notEmpty' => '请输入邮箱！',
				'email'    => '邮箱格式错误！',
			),
			'uname' => array(
				'safeString' => '错误的用户名格式，用户名只允许使用字母或字母、数字、下划线混合，不允许纯数字或特殊符号。',
			),
			'mobile' => array(
				'mobile' => '错误的手机格式！',
			),
	    	'password' => array(
	    		'notEmpty' => '请输入密码！',
	    	),
    		'role' => array(
    			'notEmpty' => '请选择用户角色！',
    		),
    		
		);
		
		$validErrs = array();
		if (!Validator::Validate($this->toArray(), $validate, $validErrs)) {
			$this->setErr($validErrs);
			return false;
		}
				
		// 用户名不能重复
	    if ($this->uname && $this->isUserNameRepeat($this->uname)) {
			$this->setErr('该用户名已经被使用');
		}
		
		// Email不能重复
	    if ($this->email && $this->isEmailRepeat($this->email)) {
			$this->setErr('该邮箱已经被使用');
		}
		
	    if ($this->mobile && $this->isMobileRepeat($this->mobile)) {
			$this->setErr('该手机号已经被使用');
		}
		
		if($this->hasErr()) {
			return false;
		}
		
		$this->regdateline  = time();
		$this->logtime      = time();
		$this->salt         = dechex(mt_rand(0x100000, 0xFFFFFF));//base_convert(mt_rand(0x100000, 0xFFFFFF), 10, 16);
		$this->password     = pw($this->password, $this->salt);
		
		// TODO 异步上传头像
		if (!empty($_FILES) && !empty($_FILES['avatar'])&& !empty($_FILES['avatar']['tmp_name'])) {
			$file = &$_FILES['avatar'];
			$uploadObj = new \module\system\model\UploadModel();
			$uploadObj->setMime($file['type']);
			$uploadObj->setTempName($file['name']);
			$uploadObj->setSize($file['size']);
			$uploadObj->setErrno($file['error']);
			$uploadObj->setTempFile($file['tmp_name']);
				
			if($uploadObj->create()) {
				$this->avatarid = $uploadObj->getPkv();
				$this->avatar   = $uploadObj->getUrl();
			}
		}
		
		// 存贮前转换角色存贮数据结构
		if($this->role) {
			$this->role = (array)($this->role);
			$this->role = array_unique($this->role);

			$this->role = implode(',', $this->role);
		}
		
		$do = parent::create();
		if($do) {
			$belongObj = new BelongModel();
			$this->role = explode(',', $this->role);
			
			foreach ($this->role as $roid) {
				$belongObj->setPkv(array('roid' => $roid, 'uid' => $this->id));
				$belongObj->create();
			}				
		}
		
		return $do;
	}
	
	/**
	 * 读取用户详细信息
	 */
	public function load(){
		if(false === parent::load()) {
			return false;
		}
		
		$this->role = $this->role ? explode(',', $this->role) : array();
		
		return $this;
	}
	
	/**
	 * 更新用户信息
	 * 
	 * @param array $user
	 */
	public function update() {
		$oldUser = new self();
		if(!$oldUser->setPkv($this->id)->load()) {
			$this->setErr('该用户不存在');
			return false;
		}

		$this->type || $this->type = $oldUser->type;
		$this->role || $this->role = $oldUser->role;
		
		// 超级管理员用户类型、角色、状态不能改变
		if (\module\user\model\UserModel::isSuper($oldUser->uid)) {
			$this->type = $oldUser->type;
			$this->status = $oldUser->status;
			$this->role = array();
		}
				
		// 输入验证
		$validate = array(
			'nickname' => array(
				'notEmpty' => '请输入昵称！',
			),
			'email' => array(
				'notEmpty' => '请输入邮箱！',
				'email'    => '邮箱格式错误！',
			),
			'uname' => array(
				'safeString' => '错误的用户名格式，用户名只允许使用字母或字母、数字、下划线混合，不允许纯数字或特殊符号。',
			),
			'mobile' => array(
				'mobile' => '错误的手机格式！',
			),
		);
		
		$validErrs = array();
		if (!Validator::Validate($this->toArray(), $validate, $validErrs)) {
			$this->setErr($validErrs);
			return false;
		}
		
		if($this->email) {
			if (!Validator::isEmail($this->email)) {
				$this->setErr('错误的邮箱格式！');
				return false;
			}

			if ($this->isEmailRepeat($this->email)) {
				$this->setErr('该邮箱已经被使用');
				return false;
			}
		} else {
			$this->addLockFields('email');
		}

		// 用户名检查
		if (!$this->uname){
			$this->addLockFields('uname');
		} elseif($this->isUserNameRepeat($this->uname)) {		
			$this->setErr('该用户名已经存在');
			return false;
		} 	
		
		// 手机验证
		if (!$this->mobile) {
			$this->addLockFields('mobile');
		} elseif($this->isMobileRepeat($this->mobile)) {
			$this->setErr('该手机号已经被使用');
			return false;
		}
		
		// 修改密码
		if($this->password) {
			$this->salt     = base_convert(mt_rand(), 10, 36);
			$this->password = pw($this->password, $this->salt);			
		} else {
			$this->addLockFields('salt,password');
		}

		// 检查用户角色
		if(!$this->checkRole()) {
			$this->setErr('错误的用户角色！');
			return false;
		}
				
		// 头像
		if (!empty($_FILES) && !empty($_FILES['avatar'])&& !empty($_FILES['avatar']['tmp_name'])) {
			$file = &$_FILES['avatar'];
			$uploadObj = new \module\system\model\UploadModel();
			$uploadObj->setMime($file['type']);
			$uploadObj->setTempName($file['name']);
			$uploadObj->setSize($file['size']);
			$uploadObj->setErrno($file['error']);
			$uploadObj->setTempFile($file['tmp_name']);
			
			if($oldUser->avatarid && $uploadObj->setPkv($oldUser->avatarid)->load()) {
				if($uploadObj->update()) {
					$this->avatarid = $oldUser->avatarid;
					$this->avatar   = $uploadObj->getUrl();
				}
				//
			} else if($uploadObj->create()) {
				$this->avatarid = $uploadObj->getPkv();
				$this->avatar   = $uploadObj->getPath();
			}			
		}
		
		if($this->avatarid) {
			$storObj = Factory::storage();			
			$storObj->remove("avatar/big/{$this->id}.jpg");
			$storObj->remove("avatar/medium/{$this->id}.jpg");
			$storObj->remove("avatar/small/{$this->id}.jpg");
			$storObj->remove("avatar/tiny/{$this->id}.jpg");			
		}

		// 存贮钱转换角色存贮数据结构
		if($this->role) {
			$this->role = (array)($this->role);
			$this->role = array_unique($this->role);
			
			$belongObj = new BelongModel();
			$belongObj->deleteByUid($this->id);// 删除用户所属角色的关联
			
			foreach ($this->role as $roid) {
				$belongObj->setPkv(array('roid' => $roid, 'uid' => $this->id));
				$belongObj->create();
			}
			
			$this->role = implode(',', $this->role);
		}
				
		$do = parent::update();
				
		return $do;
	}
	
	/**
	 * 用户名是已否存在
	 * @param string $uname
	 * @return boolean
	 */
	public function isUserNameRepeat($uname) {
		$whArr = array();
		$whArr[] = array('uname', $uname);
		$this->id && $whArr[] = array('uid', $this->id, '<>');
		
		return (bool)$this->count(array('where' => $whArr));
	}

	/**
	 * 邮箱是否已经被注册
	 * @param string $email
	 * @return boolean
	 */
	public function isEmailRepeat($email) {
		$whArr = array();
		$whArr[] = array('email', $email);
		$this->id && $whArr[] =array('uid', $this->id, '<>');
		
		return (bool)$this->count(array('where' => $whArr));
	}
	
	/**
	 * 手机号是否已经被注册
	 * @param string $email
	 * @return boolean
	 */
	public function isMobileRepeat($mobile) {
		$whArr = array();
		$whArr[] = array('mobile', $mobile);
		$this->id && $whArr[] =array('uid', $this->id, '<>');
		
		return (bool)$this->count(array('where' => $whArr));
	}

	/**
	 *
	 * @param string $type
	 * @param int $offset
	 * @param int $rows
	 */
	public function getUsersByType($type = 'member', $offset = 0, $rows = 15) {
		$cdt = array(
		    'where' => array('type', $type),
			'order' => ' uid DESC'
		);
		$rs = $this->select($cdt, $offset, $rows);
		
		$users = array();
		foreach ($rs as $r) {
			$r['role'] = $r['role'] ? explode(',', $r['role']) : array();
			$users[$r['uid']] = $r;
		}
		
		return $users;
	}
	
	/**
	 * 用户是否已登录
	 * @return boolean
	 */
	public function isLogined() {
		return !empty($_SESSION['uid']);
	}

	/**
	 * 用户登出、注销
	 *
	 * @return bool
	 */
	public function logout() {
		session_destroy();
		$_SESSION = array();
		static::setGuestSession();
			
		$sso = '';
		return $sso;
	}
		
	/**
	 * 
	 * @param string $type
	 */
	public function getTotalUsersByType($type = 'member') {
		$whArr = array('type', $type);
		return $this->count(array('where' => $whArr));
	}
	
	/**
	 * 当前用户是否是超级管理员
	 */
	public static function isSuper($uid) {
		return Config::get('super_uid') == $uid;
	}
	
	/**
	 * 当前用户是否是管理员
	 */
	public static function isAdmin() {
		return $_SESSION['isadmin'];
	}

	/**
	 * 设置登录用户会话信息
	 */
	public static function setLoginSession(\module\user\model\UserModel $user) {
		// $_SESSION 保留其它信息
		$_SESSION['uid']       = $user->uid;
		$_SESSION['openid']    = $user->openid;
		$_SESSION['uname']     = $user->uname;
		$_SESSION['nickname']  = $user->nickname;
		$_SESSION['realname']  = $user->realname;
		$_SESSION['email']     = $user->email;
		$_SESSION['mobile']    = $user->mobile;
		$_SESSION['issuper']   = Config::get('super_uid') == $user->uid;
		$_SESSION['isadmin']   = $user->type == 'admin';
		$_SESSION['isext']     = $user->isext;
		$_SESSION['isextvalid']= $user->isextvalid;
		$_SESSION['role']      = $user->role;
		$_SESSION['locale']    = $user->locale;
		$_SESSION['salt']      = $user->salt;
		$_SESSION['password']  = $user->password;
		$_SESSION['avatarid']  = $user->avatarid;
		$_SESSION['status']    = $user->status;
		$_SESSION['type']      = $user->type;
		
		$_SESSION['logintime'] = time();
		$_SESSION['ip']        = \core\App::getInstance()->getRequest()->getClientIp();
		$_SESSION['auth']      = true;
	}
	
	/**
	 * 设置游客会话信息
	 */
	public static function setGuestSession() {
		$_SESSION['uid']       = 0;
		$_SESSION['openid']    = 0;
		$_SESSION['role']      = array(Config::get('user_guest_roid'));
		$_SESSION['uname']     = 'guest';
		$_SESSION['nickname']  = 'guest';
		$_SESSION['realname']  = 'guest';
		$_SESSION['type']      = 'guest';
		$_SESSION['status']    = 1;
		$_SESSION['email']     = '';
		$_SESSION['mobile']    = '';
		$_SESSION['issuper']   = 0;
		$_SESSION['isadmin']   = 0;
		$_SESSION['isext']     = 0;
		$_SESSION['locale']    = \core\Lang::getLocale();
		$_SESSION['salt']      = '';
		$_SESSION['password']  = '';
		$_SESSION['avatarid']  = 0;
		$_SESSION['logintime'] = 0;
		$_SESSION['ip']        = \core\App::getInstance()->getRequest()->getClientIp();
		$_SESSION['auth']      = false;
		$_SESSION['isextvalid']= 0;
	}
	
	/**
	 * 获取用户快照（个人简单信息）
	 * 
	 * @return array
	 */
	public static function getSnapshot() {
		$profile = array(
			'uid'        => $_SESSION['uid'],
			'uname'      => $_SESSION['uname'],
			'realname'   => $_SESSION['realname'],
			'email'      => $_SESSION['email'],
			'mobile'     => $_SESSION['mobile'],
			'role'       => $_SESSION['role'],
			'type'       => $_SESSION['type'],
			'isadmin'    => $_SESSION['isadmin'],
			'isext'      => $_SESSION['isext'],
			'avatarid'   => $_SESSION['avatarid'],
			'avatar'     => $_SESSION['avatar'],
			'auth'       => $_SESSION['auth'],
			'logintime'  => $_SESSION['logintime'],
		);
		
		return $profile;
	}
		
	/**
	 * 删除用户，不删除超级管理员
	 * @param array $uidsArr
	 * @return bool
	 */
	public function deleteByUids($uidsArr) {		
		$whereArr = array(
			array('uid', $uidsArr, 'in'), 
			array('uid', Config::get('super_uid'), '!=')
		);
		$r = $this->deleteBy($whereArr);
		
		return $r;
	}
		
	/**
	 * 检查用户角色是否正确
	 * @return boolean
	 */
	private function checkRole() {	
		// 超级管理员不限制
		if (static::isSuper($this->uid)) {
			return true;
		}
			
		if(!$this->type || !$this->role) {
			return false;
		}
		
		$roleObj = new RoleModel();
		$roles = $roleObj->getRolesByType($this->type);
		$roleIdArr = array_keys($roles);
		
		// 会员用户必须是会员角色
		if ($this->type != 'admin' && count($this->role) > 1) {
			// 管理员才允许有多个角色
			return false;
		}
		
		$role = (array)($this->role);
		
		if (array_diff($role, $roleIdArr)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 初始化用户信息
	 */
	public static function init() {
		if(isset($_SESSION['uid'])) {
			// 用户信息已在用户首次访问或登录时定义到session
		} else {
			static::setGuestSession();
		}
	}
	
	/**
	 * 当前实例用户是否是编辑
	 * @return boolean
	 */
	public function isExt() {
		return $this->isext;
	}

	/**
	 * 根据openid 加载会员信息，如果会员不存在可以自动注册会员
	 * @param string $openId
	 * @param bool $autoRegister 是否自定注册会员
	 * @return Ambigous <boolean, \module\user\model\UserModel>
	 */
	public function loadByWXOpenId($openId, $autoRegister = false) {
		$load = $this->loadBy(array('openid', $openId));
		
		if(!$load && $autoRegister) {
			$accessToken = \core\util\wx\Api::getAccessTokenByAppIdSecret(Config::get('wx_app_id'), Config::get('wx_app_secret'));
			$userInfo = \core\util\wx\Api::getBasicUserInfoByAccessTokenOpenId($accessToken, $openId);
			
			// 自动注册微信用户
			if($this->wxRegister($userInfo)) {
				$load = $this->load();
			}			
		}
				
		return $load;
	}
	
	/**
	 * 微信自动
	 * @param array $entry
	 */
	public function wxRegister($entry) {
		$userEntry = array(
			'type'     => 'member',
			'role'     => Config::get('user_reg_roid'),
			'regdateline' => time(),
			'logtime'  => time(),
			'regip'    => \core\App::getInstance()->getRequest()->getClientIp(),
			'openid'   => $entry['openid'],
			'nickname' => $entry['nickname'],
			'avatar'   => $entry['headimgurl'],
			'sex'      => $entry['sex'] == 2 ? 0 : ($entry['sex'] == 0) ? 2 : 1, // 性别
		);
		
		$this->fromArray($userEntry);
		
		$do = parent::create();
		
		if($do) {
			$this->load();
			static::setLoginSession($this);
			
			$belongObj = new BelongModel();
			$belongObj->setPkv(array('roid' => $userEntry['role'], 'uid' => $this->getPkv()));
			$belongObj->create();
		}
		
		return $do;
	}
	
	/**
	 * 把会员设置为分销员
	 * @param int $uid
	 */
	public function setUserAsBiz($uid) {
		$data = array(
			'isext' => 1, 
			'type' => 'ext',
			'role' => Config::get('ext_reg_roid'),
		);
		// 把普通会员设为分销员
    	$setBiz = $this->updateBy($data, array(array('uid', $uid), array('type', 'member'))); // 
    	
    	if(!$setBiz) {
    		// 把管理员设为分销员
    		$this->setPkv($uid);
    		$this->alterField(array('isext' => 1)); // 把用户设置为分销员用户
    	}

    	$_SESSION['isext'] = Config::get('ext_reg_roid');
    	$_SESSION['type'] = 'ext';
    	$_SESSION['isext'] = 1;
    	
    	return $this;
	}
}

