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

use core\util\Validator;
use core\Config;
use core\Factory;

/**
 * 普通会员账号相关
 * 
 * @package     module.user.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class AccountModel extends UserModel {
	/**
	 * 使用旧密码更新密码
	 * @param string $password 新密码
	 * @param string $oldPassword 旧密码
	 * @return boolean|number
	 */
	public function updatePasswordByOldPassword($password, $oldPassword) {
		if (!$this->loaded) {
			throw new \core\mvc\Exception('请先加载对象！');
		}
		
		if ($password == $oldPassword) {
			$this->setErr('旧密码和新密码不能一样！');
			return false;
		}
		
		// 检查旧密码是否正确，如果没有设置旧密码则无需验证
		if ($oldPassword && $this->password != pw($oldPassword, $this->salt)) {
			$this->setErr('您输入的旧密码不正确，请重新输入！');
			return false;
		}
		
		$salt     = dechex(mt_rand(0x100000, 0xFFFFFF));
		$password = pw($password, $salt);
		
		// 需更新的密码及salt
		$data = array(
			'salt' => $salt,
			'password' => $password
		);
		
		return $this->updateBy($data, $this->pkvWhere());
	}
	
	/**
	 * 发送重置密码信息到邮箱
	 * 
	 * @param string $email
	 * @return bool
	 */
	public function sendResetPasswordContentByEmail($email) {
		if (!$this->loadBy(array('email', $email))) {
			$this->setErr('该邮箱不存在！');
			return false;
		}
		
		$hash = \core\Common::guid(32);
		$data = array(
			'resetpasswordhash' => $hash,
			'resetpasswordtime' => time()
		);
		
		$siteName = Config::get('site_name');
		$siteUrl = Config::get('host_info');
		$url = \core\Router::buildUrl("user.account.resetpassword/resetpasswordhash:{$hash}/email:" . urlencode($email));
		
		$message = "亲，<br />　　这是你在<strong>{$siteName}</strong>重置密码的邮件，"
				 . "<a href=\"{$url}\">点击这里重置密码</a>\n<br /><br />"
				 . "如果该链接不能点击打开，请复制如下链接到浏览器中打开：<br />{$url}";

		$cls = Config::get('mail_type') == 1 ? 'Mail' : '';
		
		// 发送取回密码邮件
		$mailer = Factory::mailer($cls);
		
		if(false == $mailer->send($email, '忘记密码', $message)) {
			$this->setErr('发送邮件失败，请联系管理员');
			return false;
		}
		
		return $this->updateBy($data, array('email', $email));
	}
	
	/**
	 * 根据邮箱和重置密码的哈希值获取用户信息
	 * @param string $email
	 * @param string $hash
	 * @return boolean|array
	 */
	public function getUserByEmailResetPasswordHash($email, $hash) {
		if(!$email || !$hash) {
			$this->setErr('错误的参数！');
			return false;
		}
		
		$options = array(
			'where' => array(
				array('email', $email), 
				array('resetpasswordhash', $hash),
		    )
		);
				
		return $this->fetchRow($options);
	}
	
	/**
	 * 根据邮箱、重置密码哈希值设置密码
	 * @param string $password
	 * @param string $email
	 * @param string $hash
	 * @return boolean
	 */
	public function updatePasswordByEmailResetPasswordHash($password, $email, $hash) {
		if(!$password || !$email || !$hash) {
			$this->setErr('错误的参数！');
			return false;
		}
				
		$salt      = dechex(mt_rand(0x100000, 0xFFFFFF));
		$password  = pw($password, $salt);
		
		$data = array(
			'password' => $password, 
			'salt' => $salt, 
			'resetpasswordhash' => '', 
			'resetpasswordtime' => ''
		);
		$whArr = array(
			array('email', $email),
			array('resetpasswordhash', $hash),
		);
		
		return $this->updateBy($data, $whArr);
	}
	
	/**
	 * 注册处理
	 */
	public function register() {
		if($this->type == 'ext') {
			$this->isextvalid = 0;
			$this->role = Config::get('ext_reg_roid');  // 修车厂注册角色ID
		} else {
			$this->type = 'member';
			$this->role = Config::get('user_reg_roid'); // 用户注册角色ID
		}
		
		return parent::create();
	}


	/**
	 * 登录，根据用户输入账号自动选择登录方式
	 *
	 * @param string $account   账号
	 * @param string $password  密码
	 * @return boolean
	 */
	public function login($account, $password) {
		$account = trim($account);
		
		if (empty($account) || empty($password)) {
			$this->setErr('账号和密码不能为空！');
			return false;
		}
		
		// 登录方式
		if (Validator::isMobile($account)) {
			$loginBy = 'mobile'; // 手机号的登录
		} elseif (Validator::isEmail($account)) {
			$loginBy = 'email'; // 邮箱登录
		} else {
			$loginBy = 'uname'; // 用户名登录
		}
	
		if (!$this->loadBy(array($loginBy , $account))) {
			$this->setErr('用户不存在！');
			return false;
		} elseif ($this->password != pw($password, $this->salt)) {
			$this->setErr('密码错误！');
			return false;
		}
	
		static::setLoginSession($this);
	
		return true;
	}
	
	/**
	 * 会员修改个人基本资料
	 * 
	 * @return bool
	 */
	public function updateProfile() {
		$data = array(
// 			'mobile'    => $this->mobile,
			'nickname'  => $this->nickname,
			'description' => $this->description,
		);

		// 头像 TODO 异步上传
		if (!empty($_FILES) && !empty($_FILES['avatar'])&& !empty($_FILES['avatar']['tmp_name'])) {
			$file = &$_FILES['avatar'];
			$uploadObj = new \module\system\model\UploadModel();
			$uploadObj->setMime($file['type']);
			$uploadObj->setTempName($file['name']);
			$uploadObj->setSize($file['size']);
			$uploadObj->setErrno($file['error']);
			$uploadObj->setTempFile($file['tmp_name']);
			
			$oldUser = clone $this;			
			if($oldUser->avatarid && $uploadObj->setPkv($oldUser->avatarid)->load()) {
				if($uploadObj->update()) {
					$data['avatarid'] = $uploadObj->getPkv();
					$data['avatar']   = $uploadObj->getPath();
				}
				//
			} else if($uploadObj->create()) {
				$data['avatarid'] = $uploadObj->getPkv();
				$data['avatar']   = $uploadObj->getPath();
			}
			
			if ($data['avatar']) {
				$storObj = Factory::storage();
				$storObj->remove("avatar/big/{$this->id}.jpg");
				$storObj->remove("avatar/medium/{$this->id}.jpg");
				$storObj->remove("avatar/small/{$this->id}.jpg");
				$storObj->remove("avatar/tiny/{$this->id}.jpg");
			}
		}
				
		$do = $this->updateBy($data, $this->pkvWhere());
		
		return $do;
	}
		
	/**
	 * 加载当前登录用户消息
	 */
	public function loadCurrentUser() {
		if(!$_SESSION['uid'] || !$this->setPkv($_SESSION['uid'])->load()) {
		    $this->logout();
		    return false;
		}
		
		return true;
	}
}