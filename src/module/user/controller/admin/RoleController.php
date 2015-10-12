<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\user\controller\admin;

use core\mvc\Message;

/**
 * 管理用户角色
 * 
 * @package     module.user.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class RoleController extends \module\system\controller\admin\AdminBase {
	/**
	 * 角色模型
	 * @var \module\user\model\RoleModel
	 */
	private $role;

	/**
	 * 初始化角色模型
	 */
	public function __construct(){
		parent::__construct();
		$this->role = new \module\user\model\RoleModel();		
		$this->view->assign('roleTypes', \module\user\model\UserModel::getInstance()->userTypes);
	}
	
	/**
	 * 角色列表
	 */
	public function listAction() {
		$type = $this->request->getRequest('type');
		$type || $type = 'member';
		
		$roles = $this->role->getRolesByType($type);
		
		$this->view
		  ->assign('roles', $roles)
		  ->assign('type', $type)
		  ->render();
	}
	
	/**
	 * 修改角色
	 * @param number $roid
	 * @return boolean
	 */
	public function updateAction($roid = 0) {
		$roid = (int)$roid;
		if(!$roid) {
			Message::setErr('错误的参数！');
			$this->showMessage();
			return false;
		}
		
		$this->role->setPkv($roid);
		if(!$this->role->isExist()) {
			Message::setErr('角色不存在！');
			$this->showMessage();
			return false;
		}
		
		if ($this->request->isPost()) {
			if(false === $this->role->fromArray($_POST)->update()) {
				Message::setErr($this->role->getErrs());
			} else {
				Message::setOK('编辑角色成功！');
			}
		}
		
		$this->role->load();
		
		$this->view
		->assign('role', $this->role->toArray())
		->assign('type', $this->role->getType)
		->render();
	}
	
	/**
	 * 删除角色
	 * @param number $roid
	 */
	public function deleteAction($roid = 0) {
		$roid = (int)$roid;
		if(!$roid) {
			Message::setErr('错误的参数！');
		} elseif(false === $this->role->setPkv($roid)->delete()){
			Message::setErr($this->role->getErrs());
		} else {
			Message::setOK('删除角色成功！');
		}
		
		if($this->request->isAjaxRequest()) {
			$this->showMessage();
		} else {
			$this->app->dispatch('user.role.list');
		}
	}
	
	/**
	 * 添加角色
	 */
	public function createAction() {
		$type = $this->request->getRequest('type');
		if(!in_array($type, array('admin', 'ext', 'member'))) {
			$type = 'member';
		}
		
		if($this->request->isPost()) {
			if(false === $this->role->fromArray($_POST)->create()) {
				Message::setErr($this->role->getErrs());
			} else {
				Message::setOK('恭喜您！添加角色成功！');
			}
		}
		
		$this->view->assign('type', $type);
		$this->view->render();
	}
}