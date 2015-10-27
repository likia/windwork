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

use module\system\model\MenuModel;
use core\mvc\Message;

/**
 * 后台菜单管理
 * 
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class MenuController extends \module\system\controller\admin\BaseController {
	/**
	 * 
	 * @var \module\system\model\MenuModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
		$this->m = new MenuModel();
		$this->initView();
	}
	
	public function listAction() {
		$menu = $this->m->getTree();
		
		$this->view->assign('menu', $menu);
		$this->view->render();
	}

	/**
	 * 添加菜单
	 */
	public function createAction() {		
		if ($this->request->isPost()) {
			if($this->m->fromArray($_POST)->create()){
				Message::setOK('成功添加菜单');
			} else {
				Message::setErr($this->m->getErrs());
			}
			
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->view->assign('menu', $this->m->getTree());
		$this->view->render();
	}

	/**
	 * 修改菜单
	 * 
	 * @param int $id 菜单id
	 */
	public function updateAction($id = '') {
		if (!$id) {
			print 'Error Param';
			return ;
		}
		
		$this->m->setPkv($id);
		
		if ($this->request->isPost()) {
			if(false !== $this->m->fromArray($_POST)->update()){
				Message::setOK('成功编辑菜单');
			} else {
				Message::setErr($this->m->getErrs());
			}
			
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}			
		}
		$this->m->load();
		$item = $this->m->toArray();
		$this->view->assign('id',   $id);
		$this->view->assign('item', $item);
		$this->view->assign('menu', $this->m->getTree());
		$this->view->render();
	}
	
	/**
	 * 删除单个后台菜单
	 * 
	 * @param int $id
	 */
	public function deleteAction($id = '') {
	    if (empty($id)) {
	    	print 'Error Param';
	    	return ;
	    }
	    
	    $this->m->setPkv($id);
	    
	    if(false === $this->m->delete()) {
	    	Message::setErr($this->m->getErrs());
	    } else {
	    	Message::setOK('成功删除后台菜单');
	    }

	    if ($this->request->isAjaxRequest()) {
	    	$this->showMessage();
	    	return true;
	    }
	    
	    $this->app->dispatch('system.admin.menu.list');
	}
	
	/**
	 * 排序
	 */
	public function sortAction() {
		if ($this->request->isPost()) {
			foreach ($_POST['sort'] as $key => $val) {
				$this->m->setPkv($key)->load();
				if($this->m->displayOrder == $val) {
					continue;
				}
				$this->m->setDisplayOrder($val)->update();
			}
			
			Message::setOK('排序成功');

			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->app->dispatch('system.admin.menu.list');
	}
}
