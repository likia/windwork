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

/**
 * 商品分类管理
 *
 * @package     module.system.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class PositionController extends \module\system\controller\admin\BaseController {

	/**
	 *
	 * @var \module\system\model\PositionModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
		$this->m = new \module\system\model\PositionModel();
		$this->initView();
	}
	
	public function createAction() {		
		if($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->create()) {
				Message::setErr($this->m->getErrs());
			} else {
				Message::setOK("恭喜您！成功添加推荐位：“{$_POST['name']}”！");
			}

			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->view->render();
	}
	

	public function updateAction($id = 0) {
		if (!$id) {
			print 'Error Param';
			return ;
		}
		
		$this->m->setPkv($id);
		if (!$this->m->isExist()) {
			Message::setErr('该推荐位不存在！');
		} else if ($this->request->isPost()) {
			if(false !== $this->m->fromArray($_POST)->update()){
				Message::setOK('恭喜您！成功编辑推荐位');
			} else {
				Message::setErr($this->m->getErrs());
			}
				
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->m->load();
		
		$this->view->assign('position', $this->m->toArray());
		$this->view->render();
	}
	
	public function listAction() {		
		//所有推荐位
		$positions = $this->m->getPositions();
		$this->view->assign('positions', $positions);
		$this->view->render();
	}

	public function deleteAction($id = 0) {
		if (empty($id)) {
			print 'Error Param';
			return false;
		}
		 
		$this->m->setPkv($id);
		 
		if(false === $this->m->delete()) {
			Message::setErr($this->m->getErrs());
		} else {
			Message::setOK('成功删除推荐位');
		}
		
		if ($this->request->isAjaxRequest()) {
			$this->showMessage();
			return true;
		}
		 
		$this->app->dispatch('system.admin.position.list');
	}
	
}
	