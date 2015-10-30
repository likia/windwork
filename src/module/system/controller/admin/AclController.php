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


use core\mvc\Message;
use module\system\model\ModuleModel;
use module\system\model\ActModel;
use module\user\model\AclModel;
use module\user\model\RoleModel;

/**
 * 权限控制管理
 * 
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class AclController extends \module\system\controller\base\AdminController {
	/**
	 * 
	 * @var \module\user\model\AclModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
		
		$modObj  = new ModuleModel();
		$actObj  = new ActModel();
		$roleObj = new RoleModel();
		
		$this->initView()
		->assign('roles',       $roleObj->getRoles())
		->assign('acts',        $actObj->select(array(), 0, 9999))
		->assign('mods',        $modObj->getInstalledMods());
		
		$this->m = new AclModel();
	}
		
	/**
	 * 设置模块角色权限
	 * @param string $mod
	 */
	public function modAction($mod = '') {
		if (!$mod) {
			die('Error Param!');
		}
		
		// 修改模块权限控制列表
		if ($this->request->isPost()) {
			if (empty($_POST['acl_item'][$mod])) {
				Message::setErr('错误的参数！');
			} else {
			    $acl = &$_POST['acl_item'][$mod];
			    if($this->m->updateModAcl($mod, $acl)) {
			    	Message::setOK('成功修改模块角色访问权限！');
			    }
			}
		}
		
		$modAcls = $this->m->getRoleAclsByMod($mod);

		$actObj  = new ActModel();		
		$modActs = $actObj->getActsByMod($mod);

		$this->view->assign('_mod', $mod);
		$this->view->assign('modAcls', $modAcls);
		$this->view->assign('modActs', $modActs);
		$this->view->render();
	}

}
