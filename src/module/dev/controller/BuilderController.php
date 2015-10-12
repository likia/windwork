<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\dev\controller;

use core\mvc\Message;

/**
 * 模块开发工具
 * 
 * @package     module.dev.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class BuilderController extends \module\system\controller\admin\AdminBase {
	/**
	 * 
	 * @var \module\dev\model\DevModel
	 */
	protected $m;
	
	public function __construct() {
		parent::__construct();
		$this->m = new \module\dev\model\DevModel();
		$moduleObj = new \module\system\model\ModuleModel();
		$mods = $moduleObj->getList();
		
		$this->initView();
		$this->view->assign('mods', $mods);
	}
	
	/**
	 * 模块列表
	 */
	public function modListAction() {
		$this->view->render();
	}

	/**
	 * 新建模块
	 */
	public function createModAction() {
		$moduleDir = SRC_PATH . "module";
		if (!is_writeable($moduleDir)) {
			Message::setErr('模块文件夹不可写，不能创建模块。');
			$this->showMessage();
			return false;
		}
		
		if ($this->request->isPost()) {
			if($this->m->createModule($_POST)) {
				Message::setOK('恭喜您，成功创建新模块！');
			} else {
				Message::setErr($this->m->getLastErr());
			}
		}
		
		$this->view->render();
	}

	/**
	 * 新增控制器
	 */
	public function addCtlAction() {
		if ($this->request->isPost()) {
			$data = array(
				'mod'       => $this->request->getRequest('themod'),
				'ctl'       => $this->request->getRequest('thectl'),
				'name'      => $this->request->getRequest('name'),
				'desc'      => $this->request->getRequest('desc'),
				'subdir'    => $this->request->getRequest('subdir'),
				'parent'    => $this->request->getRequest('parent'),
			);
			if($this->m->addCtl($data)) {
				Message::setOK('恭喜您，成功添加控制器类！');
			} else {
				Message::setErr($this->m->getLastErr());
			}
				
			if($this->request->isAjaxRequest()) {
				$this->showMessage();
				return;
			}
		}
	
		$this->view->render();
	}
	

	/**
	 * 新增模型
	 */
	public function addModelAction() {
		if ($this->request->isPost()) {
			$data = array(
				'mod'       => $this->request->getRequest('themod'),
				'name'      => $this->request->getRequest('name'),
				'model'     => $this->request->getRequest('model'),
				'desc'      => $this->request->getRequest('desc'),
				'table'     => $this->request->getRequest('table'),
				'db_table'  => $this->request->getRequest('db_table'),
				'table_name'=> $this->request->getRequest('table_name'),
				'table_desc'=> $this->request->getRequest('table_desc'),
			);
			if($this->m->addModel($data)) {
				Message::setOK('恭喜您，成功添加模型！');
			} else {
				Message::setErr($this->m->getLastErr());
			}
				
			if($this->request->isAjaxRequest()) {
				$this->showMessage();
				return;
			}
		}
	
		$this->view->render();
	}
}
