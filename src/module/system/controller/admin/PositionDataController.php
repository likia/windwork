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

use core\util\Pagination;
use core\mvc\Message;

/**
 * 商品分类管理
 *
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class PositionDataController extends \module\system\controller\admin\BaseController {

	/**
	 *
	 * @var \module\system\model\PositionDataModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
		$this->m = new \module\system\model\PositionDataModel();
		
		$this->initView();
		$this->view->assign('positions', \module\system\model\PositionModel::getInstance()->getPositions());
	}
	
	public function createAction() {		
		if($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->create()) {
				Message::setErr($this->m->getErrs());
			} else {
				Message::setOK("恭喜您！推荐成功");
			}

			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->view->render();
	}
	

	public function updateAction($id = 0) {
		if (!$id || !$this->m->setPkv($id)->isExist()) {
			$this->err404();
			return ;
		}
				
		if ($this->request->isPost()) {
			if(false !== $this->m->fromArray($_POST)->update()){
				Message::setOK('恭喜您！成功编辑推荐信息');
			} else {
				Message::setErr($this->m->getErrs());
			}
				
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->m->load();
		
		$this->view->assign('item', $this->m->toArray());
		$this->view->render();
	}
	
	public function listAction() {		
		$posid = (int)$this->request->getRequest('posid');		
		$cid   = (int)$this->request->getRequest('cid');
		
		if ($this->request->isPost()) {
			$handle = $this->request->getRequest('handle');
			// 排序
			if ($handle == 'sort') {
				foreach ($_POST['sort'] as $positionDataId => $displayOrder) {
					$this->m->setPkv($positionDataId);
					$this->m->updateDisplayOrder($displayOrder);
				}
			} elseif(empty($_POST['batchecked'])) {
				Message::setErr('请选择要处理的商品');
			} elseif($handle == 'delete') {
				if(false === $this->m->deleteByIds($_POST['batchecked'])) {
					Message::setErr($this->m->getLastErr());
				} 
			} 
		}
		
		$whArr = array();
		$posid && $whArr[] = array('posid', $posid);
		$cid && $whArr[] = array('cid', $cid);

		$cdt = array(
			'where' => $whArr,
			'order' => 'displayorder DESC, modifytime DESC',
		);
		
		$total = $this->m->count($cdt);
		$paging = new Pagination();
		$paging->setVar($total, 15, 'complex');
		
		
		$positionData = $this->m->select($cdt, $paging->offset, $paging->rows);

		$this->view->assign('pageHtml', $paging->getPager());
		$this->view->assign('positionData', $positionData);		
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
			Message::setOK('成功删除推荐信息');
		}
		
		if ($this->request->isAjaxRequest()) {
			$this->showMessage();
			return true;
		}
		 
		$this->app->dispatch('system.admin.position.list');
	}
	
}
	