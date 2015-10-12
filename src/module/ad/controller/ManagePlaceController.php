<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\ad\controller;

use core\mvc\Message;

/**
 * 广告位管理
 *
 * @package     module.ad.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ManagePlaceController extends \module\system\controller\admin\AdminBase {
	
	/**
	 *
	 * @var \module\ad\model\AdPlaceModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
				
		$this->m = new \module\ad\model\AdPlaceModel();		
	}
	
	public function createAction() {		
		if($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->create()) {
				Message::setErr($this->m->getErrs());
			} else {
				Message::setOK('恭喜您，添加广告位成功！');
				$_POST = array();
			}
		}
		
		$this->view->render();
	}
	
	/**
	 * 更新广告位
	 * 
	 * @param int $id
	 */
	public function updateAction($id = 0) {
		if(!$id || !$this->m->setObjId($id)->isExist()) {
			$this->err404();
			return false;
		}
		
		if ($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->update()) {
				Message::setErr($this->m->getErrs());
			} else {
				Message::setOK('恭喜您，修改广告位成功！');
				$_POST = array();
			}
		}
		
		$this->m->load();
		
		$this->view->assign('place', $this->m->toArray());
		$this->view->render();
	}
	
	/**
	 * 广告位列表
	 */
	public function listAction() {
		$list = $this->m->getList();
		
		$this->view->assign('list', $list);
		$this->view->render();
	}
	
	/**
	 * 删除广告位
	 */
	public function deleteAction($id = 0) {
		if(!$id) {
			$this->err404();
			return false;
		}
		
		// 广告位跟广告不相依赖
		$this->m->setObjId($id);
		if(false !== $this->m->delete()) {
			Message::setOK('成功删除广告位！');
		} else {
			Message::setErr($this->m->getErrs());
		}
		
		$this->showMessage();
	}
	
}
