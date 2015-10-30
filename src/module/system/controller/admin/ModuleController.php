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

use module\system\model\ModuleModel;
use core\mvc\Message;

/**
 * 模块管理
 * 
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ModuleController extends \module\system\controller\base\AdminController {
	/**
	 * 模块模型对象实例
	 * @var \module\system\model\ModuleModel
	 */
	private $m = null;

	public function __construct() {
		parent::__construct();
		$this->m = new ModuleModel();
		$this->initView();
	}
	
	/**
	 * 安装模块
	 * @param string $id
	 * @return boolean
	 */
	public function installAction($id = '') {
		if(empty($id)) {
			print 'Error Param';
			return false;
		}
		
		$this->m->setPkv($id);
		
		if(false === $this->m->install()){
			Message::setErr($this->m->getErrs());
			$this->showMessage();
			return false;
		} else {
			Message::setOK('成功安装模块!');
		}
		
		$this->app->dispatch('system.admin.module.list');
	}
	
	/**
	 * 卸载模块
	 * @param string $id
	 */
	public function uninstallAction($id = '') {
		if(false === $this->m->setPkv($id)->uninstall()){
			Message::setErr($this->m->getErrs());
			//$this->showMessage();
		} else {
			Message::setOK('成功卸载模块!');
		}

		$this->app->dispatch('system.admin.module.list');
	}
	

	/**
	 * 禁用模块
	 *
	 */
	public function deactivateAction($id = '') {
		if (empty($id)) {
			print 'Error Param';
			return false;
		}
		
		if($this->m->setPkv($id)->deactivate()){
			Message::setOK('成功禁用模块： '.$id);
		} else {
			Message::setErr($this->m->getErrs());
		}

		$this->response->sendRedirect('system.admin.module.list');
	}
	
	/**
	 * 启用模块
	 *
	 */
	public function activateAction($id = '') {
		if (empty($id)) {
			print 'Error Param';
			return false;
		}
		
		if($this->m->setPkv($id)->activate()){
			Message::setOK('成功启用模块： '.$id);
		} else {
			Message::setErr($this->m->getErrs());
		}

		$this->app->dispatch('system.admin.module.list');
	}
	
	/**
	 * 模块列表
	 */
	public function listAction() {		
		$mods = \module\system\model\ModuleModel::getList();
		
		// 操作说明
		$tips = array();
		$tips[] = '启用：开始使用一个模块，系统自动设置该模块的用户访问权限';
		$tips[] = '停用：停止使用一个模块，保留该模块的用户访问权限的设置，重新启用时不必重新设置访问权限。';
		$tips[] = '卸载：停止使用一个模块，并且去掉该模块的用户访问权限设置。（为安全起见，程序不自动删除该模块目录及文件）';
		
		//print_r($mods);
		$this->view->assign('mods', $mods);
		$this->view->assign('tips', $tips);
		$this->view->render();
	}

}