<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\article\controller\admin;

use core\mvc\Message;

/**
 * 栏目管理控制管理
 *
 * @package     module.article.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class CatController extends \module\system\controller\admin\AdminBase {
	/**
	 * 文章分类模型
	 * @var \module\article\model\ArticleCatModel
	 */
	private $m = null;
	
	/**
	 * 初始化栏目列表树
	 */
	public function __construct() {
		parent::__construct();
		$this->m = new \module\article\model\ArticleCatModel();
		
		$this->initView()
		  ->assign('cats', $this->m->getTree());
	}
	
	/**
	 * 新建分类
	 * @return boolean
	 */
	public function createAction() {
		
		if($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->create()) {
				Message::setErr($this->m->getErrs());
			} else {
				Message::setOK('恭喜您！添加文章栏目成功！');
			}

			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		

		$this->view->assign('cats', $this->m->getEnabledCatsTree());
		$this->view->render();
	}
	
	/**
	 * 更新栏目
	 * @param number $cid
	 * @return void|boolean
	 */
	public function updateAction($cid = 0) {
		if (!$cid) {
			print 'Error Param';
			return ;
		}
		
		$this->m->setObjId($cid);
		
		if ($this->request->isPost()) {
			if(false !== $this->m->fromArray($_POST)->update()){
				Message::setOK('成功编辑栏目');
			} else {
				Message::setErr($this->m->getErrs());
			}
				
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->m->load();
		
		$this->view->assign('cid',  $cid);
		$this->view->assign('item', $this->m->toArray());
		$this->view->assign('cats', $this->m->getTree());
		$this->view->render();
	}
	
	/**
	 * 栏目列表
	 */
	public function listAction() {
		if ($this->request->isPost() && !empty($_POST['displayorder'])) {
			foreach ($_POST['displayorder'] as $cid => $sort) {
				if($this->m->setObjId($cid)->load() && $this->m->getDisplayorder() !== $sort) {
					$this->m->setDisplayorder($sort);
					$this->m->update();
				}
			}

			Message::setOK('恭喜您！编辑栏目顺序成功！');
			$this->view->assign('cats', $this->m->getTree());
		}
		$this->view->render();
	}

	/**
	 * 删除分类
	 * @param number $cid
	 * @return boolean
	 */
	public function deleteAction($cid = 0) {
		if (empty($cid)) {
			print 'Error Param';
			return false;
		}
		 
		$this->m->setObjId($cid);
		 
		if(false === $this->m->delete()) {
			Message::setErr($this->m->getErrs());
		} else {
			Message::setOK('成功删除文章栏目');
		}
		
		if ($this->request->isAjaxRequest()) {
			$this->showMessage();
			return true;
		}
		 
		$this->app->dispatch('article.managecat.list');
	}
	
}
