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
 * 广告管理
 *
 * @package     module.ad.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ManageAdController extends \module\system\controller\admin\AdminBase {
	
	/**
	 *
	 * @var \module\ad\model\AdModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
				
		$this->m = new \module\ad\model\AdModel();	

		$placeObj = new \module\ad\model\AdPlaceModel();
		$placeList = $placeObj->getList();
		
		$this->view->assign('placeList', $placeList);
	}
	
	public function createAction() {		
		if($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->create()) {
				Message::setErr($this->m->getErrs());
			} else {
				Message::setOK('恭喜您，添加广告成功！');
				$_POST = array();
			}
		}
		
		$this->view->render();
	}
	
	/**
	 * 更新广告
	 * 
	 * @param int $aid
	 */
	public function updateAction($id = 0) {
		if(!$id || !$this->m->setObjId($id)->isExist()) {
			$this->err404();
			return false;
		}
		
		if ($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->update()) {
				Message::setErr($this->m->getLastErr());
			} else {
				Message::setOK('恭喜您，修改广告成功！');
				$_POST = array();
			}
		}
		
		$this->m->load();
		
		$this->view->assign('ad', $this->m->toArray());
		$this->view->render();
	}
	
	/**
	 * 广告列表
	 */
	public function listAction() {
		$placeId = (int)$this->request->getRequest('placeid');
		
		$list = $this->m->getList($placeId, 0, 999);
		//print_r($list);
		$this->view->assign('list', $list);		
		$this->view->render();
	}
	
	/**
	 * 删除广告
	 * @param int $id
	 */
	public function deleteAction($id = 0) {
		if(!$id || !$this->m->setObjId($id)->load()) {
			$this->err404();
			return;
		}
		
		if (false === $this->m->delete()) {
			Message::setErr($this->m->getLastErr());
		} else {
			Message::setOK('删除广告成功！');
		}
		
		$this->showMessage();
	}
	
}
