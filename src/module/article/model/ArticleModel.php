<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\article\model;

use core\Factory;
use core\Common;

class ArticleModel extends \core\mvc\Model {
	protected $table = 'wk_article';
	
	public static $statusList = array(
		'-1' => 'trash',
		'0'  => 'draft',
		'1'  => 'published',
	);
	
	public function load(){
		$do = parent::load();
		if($do) {
			$contentObj = new ArticleContentModel();
			if($contentObj->setObjId($this->getObjId())->load()) {
				$this->fromArray($this->toArray() + $contentObj->toArray());
			}			
		}
		
		return $do;
	}
	
	/**
	 * 获取已发布的文章
	 * @param int $offset
	 * @param int $rows
	 * @param string $order
	 */
	public function getPublished($offset = 0, $rows = 20, $order = '') {
		$cdt = array(
			'where' => array('status', 1),
			'order' => $order ? \core\adapter\db\SqlBuilder::order($order) : 'id desc'
		);
		return $this->select($cdt, $offset, $rows);
	}
	
	/**
	 * 保存前预处理
	 */
	protected function preSave() {
		if (empty($this->uuid)) {
			$this->setErr('错误的uuid参数。');
			return false;
		}
		
		if (empty($this->title)) {
			$this->setErr('请输入文章标题。');
			return false;
		}
		
		if (empty($this->slug)) {
			$this->slug = $this->title;
		}
		$this->slug = \core\Common::stripSlug($this->slug);
		
		if(empty($this->description)) {
			$this->description = strip_tags($this->content);
		}
		$this->description = Common::substr(trim($this->description), 400);

		if($this->content){
			$this->content = \module\system\model\UploadModel::fetchContentImage($this->content, $this->uuid);
		}
		return true;
	}
	
	public function create() {
		if(!$this->preSave()) {
			return false;
		}
		
		$this->userip = Common::userIp();
		$this->dateline = time();
		
	    $do = parent::create();
		
	    $do && ArticleContentModel::getInstance()->fromArray($this->toArray())->create();
		
		// 处理推荐
		if (false !== $do && !empty($this->position)) {
			$posData = array(
				'title'    => $this->title,
				'desc'     => $this->description,
				'url'      => \core\Router::buildUrl("article.show.item/{$this->id}"),
				'picid'    => $this->picid,
				'cid'      => $this->cid,
				'type'     => 'article',
				'item'     => $this->id,
				'displayor'=> $this->displayor,
				'pushtime' => time()
			);

			$positionDataObj = new \module\system\model\PositionDataModel();
			foreach ($this->position as $position) {
				$posData['posid'] = $position;
				$positionDataObj->fromArray($posData)->create();
			}
		}
		return $do;
	}
		
	public function update(){
		if(!$this->preSave()) {
			return false;
		}

		$this->userip = Common::userIp();
		$this->modifiedtime = time();
		
	    $do = parent::update();	
		if(false !== $do) {
			$contentObj = new ArticleContentModel();
			$contentObj->setObjId($this->getObjId())
			->setContent($this->content)
			->update();
		}
		
		$positionDataObj = new \module\system\model\PositionDataModel();
		// 删除当前文章旧的推荐数据
		$positionDataObj->deleteByTypeItem('article', $this->getObjId());
		
		// 处理推荐
		if (false !== $do && !empty($this->position)) {
			$posData = array(
				'title'    => $this->title,
				'desc'     => $this->description,
				'url'      => \core\Router::buildUrl("article.show.item/{$this->id}"),
				'picid'    => $this->picid,
				'cid'      => $this->cid,
				'type'     => 'article',
				'item'     => $this->id,
				'displayor'=> $this->displayor,
				'pushtime' => time()
			);
			
			foreach ($this->position as $position) {
				$posData['posid'] = $position;
				$positionDataObj->fromArray($posData)->create();
			}
		}
		
		return $do;		
	}
	

	/**
	 * 删除一个栏目
	 * 
	 * @return bool
	 */
	public function delete() {
		$contentObj = new ArticleContentModel();
		$positionDataObj = new \module\system\model\PositionDataModel();
		
		$do = parent::delete();
		if(false !== $do) {
			$contentObj->setObjId($this->getObjId())->delete();
			$positionDataObj->deleteByTypeItem('article', $this->getObjId());
		}
			
		
		return $do;		
	}
	
	/**
	 * 根据id删除文章
	 * @param array $ids
	 * @return bool
	 */
	public function deleteByIds($ids){
		$whereArr = array('id', $ids, 'in');
		
		$do = $this->deleteBy($whereArr);
		if($do) {
			$contentObj = new ArticleContentModel();
			$contentObj->deleteBy($whereArr);
			$positionDataObj = new \module\system\model\PositionDataModel();
			$positionDataObj->deleteBy(array(array('type', 'article'), array('item', $ids, 'in')));
		}
		return false !== $do;
	}
	
	public static function clearCache() {
		Factory::cache()->clear('article');
	}

	/**
	 * 修改文章排序
	 * @param int $displayOrder
	 * @return bool
	 */
	public function updateDisplayOrder($displayOrder) {
		if(!$this->getObjId()) {
			$this->setErr('请设置文章id！');
			return false;
		}

		$do = parent::alterField(array('displayorder' => $displayOrder));
		
		return $do;
	}
	
	/**
	 * 根据栏目获取已发布的排序最前的文章
	 * 
	 * @param int $cid
	 * @param int $rows
	 * @param array $cond
	 * @param string $sort
	 * @return array
	 */
	public static function getList($cid, $rows = 10, $cond = array(), $sort = 'displayorder DESC, id ASC') {
		$hash = md5(serialize(func_get_args()));
		if (!$list = Factory::cache()->read("article/$hash")) {
			$cid && $cond[] = array('cid', $cid);
			$cond[] = array('status', 1); // 已发布

			$cdt = array(
				'where' => $cond,
				'order' => $sort,
			);
			
			$obj = new static();
			$list = $obj->select($cdt, 0, $rows);
			
			Factory::cache()->write("article/$hash", $list);
		}
		
		return $list;
	}
	
	/**
	 * 最新文章列表
	 * @param int $cid
	 * @param int $rows
	 * @return array
	 */
	public static function getNewArticleList($cid = 0, $rows) {
		return static::getList($cid, $rows, array(), 'dateline DESC');
	}

	/**
	 * 修改文章状态
	 * @param int $status 0:草稿，1:发布，-1:垃圾
	 * @return bool
	 */
	public function updateStatus($status) {
		if(!$this->getObjId()) {
			$this->setErr('请设置文章id！');
			return false;
		}

		return $this->alterField(array('status' => $status));
	}

}