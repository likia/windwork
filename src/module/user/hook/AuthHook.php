<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\user\hook;

use core\App;
use module\user\model\AclModel;
use core\IHook;

/**
 * 
 * @package     module.user.hook
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class AuthHook implements IHook {	
	
	/**
	 * 
	 */
	public function execute($params = array()) {
		$app = App::getInstance();
		$mod = strtolower($_GET['mod']);
		$ctl = strtolower($_GET['ctl']);
		$act = strtolower($_GET['act']);

		if(!isset($_SESSION['uid'])) {
			// 用户信息已在用户首次访问或登录时定义到session 
			\module\user\model\UserModel::setGuestSession();
		}
		
		// 用户是否已被锁
		if (!empty($_SESSION['islocked'])) {
			$app->dispatch("system.default.error/403");
			return false;
		}
		
		if(in_array($act, array('login', 'logout'))) {
			return true;
		}
		
		if ("{$mod}.{$ctl}" == 'system.default') {
			return true;
		}
		
		// 系统管理后台
		if($app->getCtlObj() instanceof \module\system\controller\admin\BaseController) {
			//*
			// TODO 后台相关初始化
			if(empty($_SESSION['admincpchecked']) && "system.default.login" != "{$mod}.{$ctl}.{$act}") {
				if(!empty($_SESSION['isadmin'])) {
					// 是管理员，验证登录后台
					"system.admin.admincp.login" == "{$mod}.{$ctl}.{$act}" or
					$app->dispatch("system.admin.admincp.login/forward:".urlencode($app->getRequest()->getRequestUrl()));
				} else if(!empty($_SESSION['uid'])) {
					// 不是管理员并且已登录，提示无权访问
					$app->dispatch("system.default.error/403");
				} else {
					// 用户未登录则显示登录页面
					$app->dispatch("system.default.error/401");
				}
				
			    return false;
			}
			//*/
			if(empty($_SESSION['isadmin'])) {
				if(!empty($_SESSION['uid'])) {
					// 不是管理员并且已登录，提示无权访问
					$app->dispatch("system.default.error/403");
					return;
				} else {
					// 用户未登录则显示登录页面
					$app->dispatch("system.default.error/401");
					return;
				}
			}
		}

		// 用户未登录则显示登录页面
		AclModel::isAccessable($mod, $ctl, $act, true);
	}
}
