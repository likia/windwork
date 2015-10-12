<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\article\controller;

/**
 * 
 * @package     module.article.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ShowController extends \core\mvc\Controller {
	/**
	 * 
	 * @var \module\article\model\ArticleCatModel
	 */
	private $catObj;
	
	/**
	 * 
	 * @var \module\article\model\ArticleModel
	 */
	private $itemObj;
	
	public function __construct() {
		parent::__construct();
		
		$this->catObj = new \module\article\model\ArticleCatModel();
		$this->itemObj = new \module\article\model\ArticleModel();
		
		$this->initView()->assign('articleCats', $this->catObj->getEnabledCatsTree());
	}

	/**
	 * 文章首页
	 */
	public function indexAction() {
	
		$this->view->render();
	}
	
	/**
	 * 分类页
	 */
	public function listAction($id = 0) {
		$cond = array();
		$cat  = array();
		if ($id) {
			if (is_numeric($id) && $this->catObj->loadBy(array(array('cid', $id), array('enabled', 1)))) {
				$cond[] = array('cid', $id);
				$cat = $this->catObj->toArray();
			} elseif (is_string($id) && $this->catObj->loadBy(array(array('slug', $id), array('enabled', 1)))) {
				$id = $this->catObj->getPkv();
				$cond[] = array('cid', $id);
				$cat = $this->catObj->toArray();
			}
			
		}
		
		$cond[] = array('status', 1);

		$cdt = array(
			'where' => $cond,
			'order' => 'displayorder DESC, id DESC',
		);
		
		$total = $this->itemObj->count($cdt);
		$paging = new \core\util\Pagination();
		$paging->setVar($total, 12);
			
		$list = $this->itemObj->select($cdt, $paging->offset, $paging->rows);

		$this->view->assign('pager', $paging->getPager());
		$this->view->assign('list',  $list);
		$this->view->assign('cat',   $cat);
		$this->view->assign('id',    @$cat['cid']);
		$this->view->assign('slug',  @$cat['slug']);
		
		$cat && $this->view->assign('title', $cat['name']);
		
		$tpl = '';
		$cat && $tpl = $cat['listtpl'];
		$this->view->render($tpl);
	}

	/**
	 * 详情
	 */
	public function itemAction($id = 0) {
		$cond = array();
		$cond[] = array((is_numeric($id) ? 'id' : 'slug'), $id);
		
		$item = $this->itemObj;
		
		if(!$id || !$item->loadBy($cond)) {
			$this->err404();
			return;
		}
		
		if ($item->status <= 0) {
			if (empty($_SESSION['isadmin'])) {
			    $this->err404();
			    return;
			} else {
				$item->content = '<h2 style="color:#F00; font-size:20px; border:1px solid #F00; background:#F5F5F5; padding:8px; margin:0 0 16px 0;">预览文章，该文章未发布</h2>' . $item->content;
			}
		}

		$cat = array();
		if ($item->cid && $this->catObj->setPkv($item->cid)->load()) {
			$cat = $this->catObj->toArray();
		}

		$newArticleList = $this->itemObj->getList($item->cid, 10, array(), 'dateline DESC');
		
		$this->view->assign('title',       $item->title);
		$this->view->assign('keyword',     $item->keyword);
		$this->view->assign('description', $item->description);
		$this->view->assign('id',          $id);
		$this->view->assign('slug',        $item->slug);
		$this->view->assign('cat',         $cat);
		$this->view->assign('newArticleList', $newArticleList);
		$this->view->assign('item',        $item->toArray());
		$this->view->render(empty($item->tpl) ? $cat['itemtpl'] : $item->tpl);
	}
	
	/**
	 * 搜索
	 */
	public function searchAction() {
		$keyword = $this->request->getRequest('keyword');
		$sort    = $this->request->getRequest('sort');
		
		$this->view->render();
	}
}