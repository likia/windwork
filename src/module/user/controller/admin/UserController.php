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

use core\Config;
use core\mvc\Message;
use module\user\model\RoleModel;
use module\user\model\UserModel;

/**
 * 
 * @package     module.user.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class UserController extends \module\system\controller\admin\AdminBase {
	/**
	 *
	 * @var \module\user\model\UserModel
	 */
	protected $user;
	
	public function __construct(){
		parent::__construct();
		$this->user = new UserModel();
		
		$this->view->assign('userTypeList',    $this->user->userTypes);
		$this->view->assign('userStatusList',  $this->user->userStatus);
	}
	
	/**
	 * 添加新用户
	 */
	public function createAction() {
		$type = $this->request->getRequest('type');
		// 默认用户类型为普通会员
		if (!in_array($type, array_keys($this->user->userTypes))) {
			$type = 'member';
		}
		
		// 获取该用户类型的角色
		$roleObj = new RoleModel();
		$roles = $roleObj->getRolesByType($type);
		
		if(!$roles) {
			Message::setErr('请先添加'.$this->user->userTypes[$type].'角色');
			Message::setErr("<a href=\"index.php?user.admin.role.create/type:{$type}/hash:".csrfToken()."\">立即添加</a>");
			$this->showMessage();
			return;
			
		}

		if($this->request->isPost()) {
			if(false === $this->user->fromArray($_POST)->create()) {
				Message::setErr($this->user->getErrs());
			} else {
				$_POST = array();
				$uid = $this->user->getObjId();
				$msg = '成功添加用户'.$this->user->userTypes[$type]
				     . " <a href='index.php?user.admin.user.update/{$uid}/hash:".csrfToken()."'>编辑</a>";
				Message::setOK($msg);
			}
		}

		$this->view->assign('roles', $roles);
		$this->view->assign('type', $type);
		$this->view->render();
	}
	
	public function updateAction($uid = 0) {
		$uid = (int)$uid;
		if(!$uid || !$this->user->setObjId($uid)->load()) {			
			Message::setErr('错误：该用户不存在！');
			$this->showMessage();
			return false;
		}
		
		// 管理员的个人信息只有自己能改
		if (!$this->user->isSuper($_SESSION['uid']) && $this->user->isadmin && $uid != $_SESSION['uid']) {
			Message::setErr('错误：你不能修改该用户。');
			$this->showMessage();
			return false;
		}
				
		if ($this->request->isPost()) {
			$this->user->fromArray($_POST);
			if(false !== $this->user->update()) {
				Message::setOK('成功编辑用户信息');
			} else {
				Message::setErr($this->user->getErrs());
			}
		}
		
		$this->user->load();
				
		// 获取该用户类型的可选角色
		$roleObj = new RoleModel();
		$roles = $roleObj->getRolesByType($this->user->type);

		$this->view->assign('type',  $this->user->type);
		$this->view->assign('user',  $this->user->toArray());
		$this->view->assign('roles', $roles);
		$this->view->render();
	}
	
	public function deleteAction($uids = array()) {
		$uids = (array)$uids;
		
		if(false !== $this->user->deleteByUids($uids)) {
			Message::setOK('成功删除用户！');
		} else {
			Message::setErr($this->user->getErrs());
		}
		
		$this->showMessage();
	}
	
	/**
	 * 用户列表，根据用户类型显示
	 */
	public function listAction() {
		$type = $this->request->getRequest('type');
		if (!in_array($type, array_keys($this->user->userTypes))) {
			$type = 'member';
		}
		
		$totals = $this->user->getTotalUsersByType($type);
		$paging = new \core\util\Pagination();
		$paging->setVar($totals, Config::get('user_manage_page_rows'));
		
		$roles = array();
		$roleObj = new RoleModel();
		$rs = $roleObj->select(array(), 0, 100);
		foreach ($rs as $r) {
			$roles[$r['roid']] = $r;
		}	
		$users = $this->user->getUsersByType($type, $paging->offset, $paging->rows);

		$this->view->assign('type',  $type);
		$this->view->assign('users', $users);
		$this->view->assign('roles', $roles);
		$this->view->assign('pager', $paging->getPager());
		$this->view->render();
	}
}