<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\user\controller;

use module\user\model\AccountModel;
use core\Factory;
use core\mvc\Message;
use core\Config;
use module\user\model\UserModel;
use core\Common;

/**
 * 
 * @package     module.user.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class AccountController extends \core\mvc\Controller {
	private $account;
	
	public function __construct() {
		parent::__construct();
		$this->initView();
		$this->account = new AccountModel();
		
		if (Common::checkMobile() || $this->request->getGet('ismobile')) {
			$this->view->isMobileView = 1;
		}
		
	}
	
	/**
	 * 根据旧密码修改密码
	 */
	public function setPasswordAction() {
		$accountObj = new AccountModel();
		$accountObj->setPkv($_SESSION['uid']);
				
		if ($this->request->isPost() && \core\Request::checkRePost()) {
			$oldPassword  = $this->request->getRequest('oldpassword');
			$newPassword  = $this->request->getRequest('newpassword');
			
			// 验证码			
			$captchaOpt = Config::get('captcha_enabled_opt');
			$secode     = $this->request->getRequest('secode');
			
			if(!empty($captchaOpt['setpassword']) && !Factory::captcha()->check($secode, 'setpassword')) {
				Message::setErr($secode ? '验证码错误' : '请输入验证码');
			} else {
				$accountObj->load();
				if(false !== $accountObj->updatePasswordByOldPassword($newPassword, $oldPassword)) {
					Message::setOK('修改密码成功');					
				} else {
					Message::setErr($accountObj->getErrs());
				}
			}
			
		}
		
		$accountObj->load();
		
		$this->view->assign('account', $accountObj->toArray());
		$this->view->render();
	}
	
	/**
	 * 注册
	 */
	public function registerAction() {
		// 已登录用户直接跳转到用户中心
		if (!empty($_SESSION['uid'])) {
			$this->response->sendRedirect(url("user.Account.profile", true));
			return ;
		}
		
		if(!Config::get('user_allow_reg')) {
			$this->showMessage(Config::get('user_reg_stop_reason'));
			return;
		}
		
		if ($this->request->isPost() && \core\Request::checkRePost()) {
			// 检查验证码
			$captchaOpt = Config::get('captcha_enabled_opt');
			if(!empty($captchaOpt['reg']) && !Factory::captcha()->check($this->request->getRequest('secode'), 'reg')) {
				Message::setErr('验证码错误');
			} else {
				$account = array(
					'email'     => $this->request->getPost('email'),
					'password'  => $this->request->getPost('password'),
					'password2' => $this->request->getPost('password2'),
					'type'      => $this->request->getPost('accounttype'),
				);
				
				$this->account->fromArray($account);
				if(false === $this->account->register()) {
					Message::setErr($this->account->getErrs());
				} else {
					// 注册成功后登录
					if(false !== $this->account->login($account['email'], $account['password'])) {
					    if ($_SESSION['isadmin']) {
							$this->response->sendRedirect('system.admin.admincp.index');
						} else {
							$this->response->sendRedirect('user.account.center');
						}
						
					} else {
						Message::setErr($this->account->getErrs());
						$this->showMessage();
					}
					return;
				}
				
				if ($this->request->isAjaxRequest()) {
					$this->showMessage();
					return;
				}
			}
		}
		
		$this->view->assign('title', '注册');
		$this->view->render();
	}

	/**
	 * 登录
	 */
	public function loginAction() {
		// 用户已登录则跳入用户中心
		if($this->account->isLogined()) {
			$this->response->sendRedirect('user.account.center');
			return;
		}

		$forward = $this->request->getRequest('forward');
		
		// 来自微信的登录，使用微信登录
		if ($this->request->isFromWeixin()) {
			$forward = paramEncode($forward);
			$this->response->sendRedirect("user.oauth.weixin.login/forward:{$forward}");
			return;
		}
		
		if($this->request->isPost() && \core\Request::checkRePost()) {
			$captchaOpt = Config::get('captcha_enabled_opt');
			$secode     = $this->request->getRequest('secode');
			// 验证码
			if(!empty($captchaOpt['login']) && !Factory::captcha()->check($secode, 'sec')) {
				Message::setErr($secode ? '验证码错误' : '请输入验证码');
			} else {
				$account   = $this->request->getRequest('account');
				$password  = $this->request->getRequest('password');
			
				$accountObj = new AccountModel();
				if(false === $accountObj->login($account, $password)) {
					Message::setErr($accountObj->getLastErr());
				} else {
					// 跳转
					Message::setOK('登录成功');
					if (!$this->request->isAjaxRequest()) {						
						if(!$forward){
							$referer = $this->request->getRefererUrl();
							$refererObj = new \core\mvc\Router();
							$refererObj->parseUrl($referer);
							if($refererObj->params['act'] != 'login') {
								$forward = $referer;
							} else if (!empty($_SESSION['isadmin'])) {
								$forward = url('system.admin.admincp.index');
							} else {
								$forward = url('user.account.center');
							}
						}
						
						$forward = urldecode(urldecode($forward));
						$this->response->sendRedirect($forward);
						return true;
					}
				}
			}

			if ($this->request->isAjaxRequest()) {
				$snapshot = UserModel::getSnapshot();
				$this->showMessage($snapshot);
				return ;
			}
		}
		
		$this->view->assign('title', '登录');
		
		// ajax登录框
		if ($this->request->isAjaxRequest()) {
			$this->view->render('user/account.login.ajax.html');
			$ctx = ob_get_clean();
			$this->showMessage($ctx);
			return;
		}

		$this->view->assign('forward', $forward);
		$this->view->render();
	}

	/**
	 * 登出
	 */
	public function logoutAction() {
		$forward = $this->request->getRequest('forward');
		$forward = $forward ? paramDecode($forward) : $this->request->getRefererUrl();
		
		$userObj = new AccountModel();
		$sso = $userObj->logout();
		
		if ($sso || $this->request->isAjaxRequest()) {
			Message::setOK('退出成功！');
			$snapshot = UserModel::getSnapshot();
			$this->showMessage($snapshot);
			
			return true;
		}
		
		$this->response->sendRedirect($forward);
	}
	
	/**
	 * 取回密码
	 */
	public function forgetPasswordAction() {
		if($this->request->isPost() && \core\Request::checkRePost()) {
		    $captchaOpt = Config::get('captcha_enabled_opt');
			$secode     = $this->request->getRequest('secode');
			
			// 验证码
			if(!empty($captchaOpt['forgetpassword']) && !Factory::captcha()->check($secode, 'forgetpassword')) {
				Message::setErr($secode ? '验证码错误' : '请输入验证码');
			} else {
				$email = $this->request->getRequest('email');
				$accountObj = new \module\user\model\AccountModel();
				
				// 使用邮箱取回密码
				if(false !== $accountObj->sendResetPasswordContentByEmail($email)) {
					Message::setOK('你已成功提交取回密码信息，请在24小时内到你的邮箱查收修改密码！');				
					$this->showMessage();
					return;
				} else {
					Message::setErr($accountObj->getErrs());
				}
			}			
		}
		
		$this->view->assign('title', '取回密码');				
		$this->view->render();		
	}
	
	public function resetPasswordAction() {
		$email = $this->request->getRequest('email');
		$hash = $this->request->getRequest('resetpasswordhash');
		
		if (!$hash && !$email) {
			Message::setErr('无效的取回密码链接！');
			$this->showMessage();
		}
		
		$email = paramDecode($email);
		$accountObj = new \module\user\model\AccountModel();
		$userInfo = $accountObj->getUserByEmailResetPasswordHash($email, $hash);
		
		if(!$userInfo) {
			Message::setErr('取回密码链接无效！');
		    $this->showMessage();
		    return;
		} else if ($userInfo['resetpasswordtime'] < (time() - 24*3600)) {
			Message::setErr('取回密码信息已过期，请重新<a href="'.url('user.account.forgetpassword').'">申请取回密码</a>！');
		    $this->showMessage();
		    return;
		}
		
		// 重置密码
		if($this->request->isPost() && \core\Request::checkRePost()) {
			$newPassword = $this->request->getRequest('newpassword');
			if(false !== $accountObj->updatePasswordByEmailResetPasswordHash($newPassword, $email, $hash)) {
				Message::setOK('重置密码成功！<a href="'.url('user.account.login').'">马上登录</a>');
			} else {
				Message::setErr($accountObj->getErrs());
			}
			$this->showMessage();
		    return;
		}
	
		$this->view->render();
	}
	
	/**
	 * (修改)个人信息
	 */
	public function profileAction() {
		if ($this->request->isAjaxRequest()) {			
			$snapshot = UserModel::getSnapshot();
			$this->showMessage(array('profile' => $snapshot));
			return true;
		}
		
		if(!$this->account->loadCurrentUser()) {
			$this->err401();
			return false;
		}
		
		if($this->request->isPost() && \core\Request::checkRePost()) {
			// TODO 修改更多资料	
			$account = array(
				//'mobile'    => $_POST['mobile'], 
				'nickname'    => $_POST['nickname'], 
				'description' => $_POST['description'],
			);			
			$this->account->fromArray($account);
			
			// 修改基本资料
			if(false === $this->account->updateProfile()) {
				Message::setErr($this->account->getErrs());
			} else {
				Message::setOK('修改个人信息成功！');
				$this->account->loadCurrentUser();
			}
			
		}
				
		$this->view->assign('account', $this->account->toArray());
		$this->view->render();
	}
	
	/**
	 * 用户中心
	 * @param int $uid
	 */
	public function centerAction() {
		if(!$this->account->loadCurrentUser()) {
			$this->response->sendRedirect('user.account.login');
			return;
		}
				
		$this->view->assign('account', $this->account->toArray());
		$this->view->render();
	}	
}