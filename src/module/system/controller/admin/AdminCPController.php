<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\controller\admin;

use core\Config;
use module\user\model\AclModel;
use module\system\model\MenuModel;

/**
 * 后台入口
 * 
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class AdminCPController extends \module\system\controller\admin\AdminBase {
	
	/**
	 * 管理后台登录
	 */
	public function loginAction() {
		// TODO 后台登录
		if ($this->request->isPost()) {
			$account = $this->request->getRequest('account');
			$password  = $this->request->getRequest('password');
			if($account && $password 
			  && ($_SESSION['uname'] == $account || $_SESSION['email'] == $account)
			  && $_SESSION['password'] == pw($password, $_SESSION['salt'])) {
				$_SESSION['admincpchecked'] = 1;
				$this->response->sendRedirect("system.admin.admincp.index");
				return true;
			}
			
		}
		
		$this->view->render();
	}
	
	/**
	 * 后台欢迎页
	 */
	public function welcomeAction() {
	    $this->view->render();
	}
	
	/**
	 * 管理后台首页
	 */
	public function indexAction() {		
		$baseUrl = Config::get('base_url');

		$menuObj = new MenuModel();		
		$rs = $menuObj->getEnabledTree();		
		
		$menu = array();
		foreach ($rs as $item) {
			if ($item['upid']) {
				continue;
			}
			
			$item['chile'] = array();
			
			foreach ($rs as $item2) {
				if ($item2['upid'] != $item['id']) {
					continue;
				}
				
				// 没有权限则
				$router = new \core\Router();
				$router->parseUrl($item2['url']);
				
				if(!AclModel::isAccessable($router->params['mod'], $router->params['ctl'], $router->params['act'])) {
					continue;
				}
				
				if($baseUrl && false === strpos($item2['url'], $baseUrl)) {
					$item2['url'] = $baseUrl . $item2['url'];
				}
				
				$item2['url'] .= "/hash:".csrfToken();
				$item['chile'][] = $item2;
			}

			$menu[$item['id']] = $item;
		}
		
		$this->view->assign('menu', $menu);
		$this->view->render();
	}
}