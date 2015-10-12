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

class ArticleCatModel extends \core\mvc\Model {
	protected $table = 'wk_article_cat';
	
	public function create() {
		if (empty($this->name)) {
			$this->setErr('请输入栏目名称。');
			return false;
		}
		
		if (empty($this->slug)) {
			$this->slug = $this->name;
		}
		$this->slug = preg_replace("/(\\s+)/", "-", $this->slug);
		$this->slug = urlencode($this->slug);
		
		$do = parent::create();		
		$do && self::clearCache();
		
		return $do;
	}
	
	public function update(){
		if (empty($this->name)) {
			$this->setErr('请输入栏目名称。');
			return false;
		}
		
		$cats = $this->getTree();
		
		// 不允许把本身或子分类设为上级分类
		if($this->parentid == $this->cid || in_array($this->parentid, $cats[$this->getObjId()]['chileArr'])) {
			$this->setErr('错误的上级分类，不允许选择自己或自己的子分类作为上级分类。');
			return false;
		}
		
		if (empty($this->slug)) {
			$this->slug = $this->name;
		}
		$this->slug = preg_replace("/(\\s+)/", "-", $this->slug);
		$this->slug = urlencode($this->slug);
		
		
		$do = parent::update();
		
		$do && self::clearCache();
		
		return $do;		
	}
	
	/**
	 * 获取已启用的栏目
	 */
	public function getEnabledCats() {
		$cdt = array(
			'where' => array('enabled', 1),
			'order' => 'displayorder ASC, cid ASC'
		);
		
		return $this->select($cdt , 0, 9999);
	}
	
	/**
	 * 获取所有栏目的栏目树
	 * @return array:
	 */
	public function getTree() {
		$cdt = array(
			'order' => 'displayorder, cid'
		);
		
		$cats = $this->select($cdt, 0, 9999);
		$tree = new \core\util\Tree();
		$tree->set($cats, 'cid', 'parentid');
		
		return $tree->get();
	}

	/**
	 * 获取已启用栏目的栏目树
	 * @return array:
	 */
	public function getEnabledCatsTree() {
		$cacheKey = "article/cat/enabled";
		
		if(!$catsTree = Factory::cache()->read($cacheKey)) {
			$cats = $this->getEnabledCats();
			$tree = new \core\util\Tree();
			$tree->set($cats, 'cid', 'parentid');
			
			$catsTree = $tree->get();
			Factory::cache()->write($cacheKey, $catsTree);
		}
		
		return $catsTree;
	}

	/**
	 * 删除一个栏目
	 * 
	 * @return bool
	 */
	public function delete() {
		// 不允许删除有子分类的栏目
		$cats = $this->getTree();
		if (empty($cats[$this->getObjId()])) {
			$this->setErr('该分类已删除或未添加！');
			return false;
		}
		
		if (!empty($cats[$this->getObjId()]['chileArr'])) {
			$this->setErr('该分类下还有子分类，不允许删除！');
			return false;			
		}
		
		// 不允许删除有内容的分类
		$article = new ArticleModel();
		if($article->count(array('where' => array('cid', $this->getObjId())))) {
			$this->setErr('该分类下还有文章，不允许删除！');
			return false;			
		}
		
		return parent::delete();		
	}
	
	public static function clearCache() {
		Factory::cache()->clear('article/cat');
	}
	
}