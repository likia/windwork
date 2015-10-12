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

use module\article\model\ArticleModel;
use core\Lang;
use core\mvc\Message;

/**
 * 文章管理
 *
 * @package     module.article.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ContentController extends \module\system\controller\admin\AdminBase {
	
	/**
	 *
	 * @var \module\article\model\ArticleModel
	 */
	private $m = null;
	
	public function __construct() {
		parent::__construct();
		
		$this->m = new \module\article\model\ArticleModel();
				
		$catObj = new \module\article\model\ArticleCatModel();
		$cats = $catObj->getEnabledCatsTree();
		
		$this->initView();
		$this->view->assign('cats', $cats);
		$this->view->assign('articleStatusList', \module\article\model\ArticleModel::$statusList);
		$this->view->assign('positions', \module\system\model\PositionModel::getInstance()->getPositions());
	}
	
	public function createAction() {
		if($this->request->isPost()) {
			if(false === $this->m->fromArray($_POST)->create()) {
				Message::setErr($this->m->getErrs());
			} else {
				/*
				if($_POST['msg'] == 1 && $_POST['status'] == 1){
					$msg = new \module\message\model\MessageModel();
					$data['title'] = $_POST['title'];
					$data['content'] = $_POST['content'];
					$data['cid'] = 4;
					if(false === $msg->fromArray($data)->create()){
						Message::setErr($msg->getErrs());
					}else{
						Message::setOk('消息推送成功！');
					}
				}
				*/
				Message::setOK('恭喜您，添加文章成功！');
				$_POST = array();
			}
		}
		
		$this->view->assign('uuid',  empty($_POST['uuid']) ? \core\Common::guid() : $_POST['uuid']);
		$this->view->render();
	}
	
	/**
	 * 更新文章
	 * 
	 * @param int $aid
	 */
	public function updateAction($id = 0) {
		if (!$id) {
			$this->err404();
			return false;
		}
		
		$this->m->setPkv($id);
		
		if ($this->request->isPost()) {
			if(false !== $this->m->fromArray($_POST)->update()){
				Message::setOK('成功编辑文章');
			} else {
				Message::setErr($this->m->getErrs());
			}
		
			if ($this->request->isAjaxRequest()) {
				$this->showMessage();
				return true;
			}
		}
		
		$this->m->load();
		
		$uploadObj = new \module\system\model\UploadModel();		
		
		// 文章相册
		$this->view->assign('album', $uploadObj->getAlbumByRid($this->m->uuid));

		// 文章图片
		//$this->view->assign('photos', $uploadObj->getImgByRid($this->m->uuid, 500));

		// 推荐
		$this->view->assign('positionIds', \module\system\model\PositionDataModel::getInstance()->getPosIdsByTypeItem('article', $id));
		
		$this->view->assign('id', $id);
		$this->view->assign('uuid', $this->m->uuid);
		$this->view->assign('article', $this->m->toArray());
		$this->view->render();
	}
	
	/**
	 * 文章列表
	 */
	public function listAction() {
		$keyword = $this->request->getRequest('keyword');
		$cid     = (int)$this->request->getRequest('cid');
		$catList = $this->view->cats;
		$handle  = $this->request->getRequest('handle');
				
		if ($handle && $this->request->isPost()) {			
			// 排序
			if ($handle == 'sort') {
				foreach ($_POST['displayorder'] as $articleId => $displayOrder) {
					$this->m->setPkv($articleId);
					$this->m->updateDisplayOrder($displayOrder);
				}
			} elseif(empty($_POST['batchecked'])) {
				Message::setErr('请选择要处理的文章');
			} elseif($handle == 'del') {
				$this->m->deleteByIds($_POST['batchecked']);
			} elseif($handle == 'trash' || $handle == 'draft' || $handle == 'published') {
				$status = $handle == 'trash' ? -1 : ($handle == 'published' ? 1 : 0);
				foreach ($_POST['batchecked'] as $articleId) {
					$this->m->setPkv($articleId);
					$this->m->updateStatus($status);
				}
			}
		}
		
		$whereArr = array();
		
		// 关键词搜索
		if ($keyword) {
			$keyword = $_GET['keyword'] = $_REQUEST['keyword'] = urldecode(urldecode($keyword));
			$keyword = htmlspecialchars($keyword);
			$whereArr[] = array(
				'or',
				array('keyword', "%{$keyword}%", 'like'),
				array('description', "%{$keyword}%", 'like'),				
			);
		}
		
		// 分类
		if ($cid && isset($catList[$cid])) {
			$currCat = $catList[$cid];
			$whereArr[] = array('cid', $currCat['ancestorIdArr']);
		}
		
		$cdt = array(
			'where' => $whereArr,
			'order' => 'displayorder DESC, id DESC'
		);
		
		$total = $this->m->count($cdt);
		$paging = new \core\util\Pagination();
		$paging->setVar($total, 20, 'complex');
		$list = $this->m->select($cdt, $paging->offset, $paging->rows);
		
		foreach ($list as $k => $v) {
			if ($keyword) {
				$list[$k]['title'] = str_replace($keyword, "<span class=red>{$keyword}</span>", $v['title']);
			}
			$list[$k]['label_status'] = Lang::get(ArticleModel::$statusList[$v['status']]);
			$list[$k]['label_date'] = date('Y-m-d H:i:s', $v['dateline']);
		}

		$this->view->assign('cid',  $cid);
		$this->view->assign('list', $list);
		$this->view->assign('keyword', $keyword);
		$this->view->assign('paging', $paging->getPager());
		
		$this->view->render();
	}
	
	/**
	 * 删除文章
	 * @param int $ids
	 */
	public function deleteAction($ids = 0) {
		if (!$ids) {
			$this->err404();
			return false;
		}
		$ids = (array)$ids;
		
		if(false !== $this->m->deleteByIds($ids)) {
			Message::setOK('成功删除文章！');
		} else {
			Message::setErr($this->m->getErrs());
		}
		
		if ($this->request->isAjaxRequest()) {
			$this->showMessage();
			return true;
		} else {
			$this->app->dispatch("{$this->mod}/{$this->ctl}/list");
		}
	}
	
}
