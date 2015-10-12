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

use module\system\model\NavModel;
use core\mvc\Message;

/**
 * 网站前台导航管理
 * 
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class NavController extends \module\system\controller\admin\AdminBase {
	/**
	 * 
	 * @var \module\system\model\NavModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
		$this->m = new NavModel();
		$this->initView();
		
		$this->view->assign('navs', $this->m->getTree());
	}
	
	public function listAction() {
		if ($this->request->isPost()) {
			// TODO 排序
		}
		
		$this->view->render();
	}

	/**
	 * 添加导航链接
	 */
	public function createAction() {		
		if ($this->request->isPost()) {
			if($this->m->fromArray($_POST)->create()){
				Message::setOK('成功添加导航链接');
				$_POST = array();
			} else {
				Message::setErr($this->m->getErrs());
			}
			
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->view->render();
	}

	/**
	 * 修改导航链接
	 * 
	 * @param int $id 导航链接id
	 */
	public function updateAction($id = '') {
		if (!$id) {
			print 'Error Param';
			return ;
		}
		
		$this->m->setPkv($id);
		
		if ($this->request->isPost()) {
			if(false !== $this->m->fromArray($_POST)->update()){
				Message::setOK('成功编辑导航链接');
			} else {
				Message::setErr($this->m->getErrs());
			}
			
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}			
		}
		
		$this->m->load();
		$nav = $this->m->toArray();
		
		$this->view->assign('id',   $id);
		$this->view->assign('nav',  $nav);
		$this->view->assign('navs', $this->m->getTree());
		$this->view->render();
	}
	
	/**
	 * 删除单个导航链接
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
	    	Message::setOK('成功删除导航链接');
	    }

	    if ($this->request->isAjaxRequest()) {
	    	$this->showMessage();
	    	return true;
	    }
	    
	    $this->app->dispatch('system.admin.nav.list');
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

			$this->view->assign('navs', $this->m->getTree());
			
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->app->dispatch('system.admin.nav.list');
	}
}
