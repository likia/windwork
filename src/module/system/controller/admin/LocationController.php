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

// TODO 分页显示，按层级显示（每页只显示一层）

use module\system\model\LocationModel;
use core\mvc\Message;

/**
 * 管理地理位置标注点
 * 
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class LocationController extends \module\system\controller\admin\AdminBase {
	/**
	 * 
	 * @var \module\system\model\LocationModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
		$this->initView();
		$this->m = new LocationModel();
	}
	
	public function listAction() {
		$list = $this->m->getTree();
		
		$this->view->assign('list', $list);
		$this->view->render();
	}

	/**
	 * 添加地理位置点
	 */
	public function createAction() {		
		if ($this->request->isPost()) {
			if($this->m->fromArray($_POST)->create()){
				Message::setOK('成功添加地理位置');
			} else {
				Message::setErr($this->m->getErrs());
			}
			
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->view->assign('list', $this->m->getTree());
		$this->view->render();
	}

	/**
	 * 修改地理位置点
	 * 
	 * @param int $id 地理位置id
	 */
	public function updateAction($id = '') {
		if (!$id) {
			print 'Error Param';
			return ;
		}
		
		$this->m->setPkv($id);
		
		if ($this->request->isPost()) {
			if(false !== $this->m->fromArray($_POST)->update()){
				Message::setOK('成功编辑地理位置');
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
		$this->view->assign('list', $this->m->getTree());
		$this->view->render();
	}
	
	/**
	 * 删除单个后台地理位置
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
	    	Message::setOK('成功删除后台地理位置');
	    }

	    if ($this->request->isAjaxRequest()) {
	    	$this->showMessage();
	    	return true;
	    }
	    
	    $this->app->dispatch('system.admin.location.list');
	}
	
	/**
	 * 排序
	 */
	public function sortAction() {
		if ($this->request->isPost()) {
			foreach ($_POST['sort'] as $key => $val) {				
				$this->m->alterDisplayOrder($key, $val);
			}
			
			Message::setOK('排序成功');

			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->app->dispatch('system.admin.location.list');
	}
}
