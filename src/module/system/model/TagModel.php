<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\model;

use core\adapter\db\SqlBuilder;
/**
 * 
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class TagModel extends \core\mvc\Model {
	protected $table = 'wk_tag';
	
	/**
	 * 根据标签类型获取热门标签
	 * @param string $type
	 * @param string $rows
	 */
	public static function getHotTagsByType($type, $rows = 20) {
		// TODO
		$sql = "SELECT t.id, t.name, COUNT(t.id) num FROM wk_tag t, wk_tag_item i
				WHERE t.id = i.tagid AND i.type = %s
				GROUP BY t.id
				ORDER BY num DESC
				LIMIT %i";
		$rs = self::db()->getAll($sql, array($type, $rows));
		
		foreach ($rs as $k => $r) {
			$rs[$k]['slug'] = urlencode($r['name']);
		}
		
		return $rs;		
	}
	
	/**
	 * 添加标签组
	 * @param string $tags 多个标签用半角逗号(,)隔开
	 * @param string $type
	 * @param string|int $itemId
	 */
	public static function createTags($tags, $type, $itemId) {
		$tags = explode(',', $tags);
		$tags = array_unique($tags);
		
		$thisObj = new self();
		$thisObj->type = $type;
		$thisObj->itemid = $itemId;
		
		foreach ($tags as $tag) {
			$thisObj->name = $tag;
			$thisObj->create();
		}
	}

	/**
	 * 新建标签
	 */
	public function create() {
		if(!$this->name) {
			$this->setErr('标签名称不能为空');
			return false;
		}

		if(!$this->type) {
			$this->setErr('错误的标签类型');
			return false;
		}
		
		if(!$this->itemid) {
			$this->setErr('错误的标签关联id');
			return false;
		}
		
		$do = true;
		
		// 标签不存在则创建
		if (!$this->loadBy(array('name', $this->name))) {
			$do = parent::create();
		}
		
		$itemObj = new TagItemModel();		
		$itemObj->tagid  = $this->id;
		$itemObj->itemid = $this->itemid;
		$itemObj->type   = $this->type;
		
		if ($do && !$itemObj->isExist()) {
			$do = $itemObj->create();
		}
		
		return $do;
	}

	public function deleteByName($name) {
		if(!$this->loadBy(array('name', $name))) {
			$this->setErr('该标签不存在！');
			return false;
		}
		
		$itemObj = new TagItemModel();
		$do = $itemObj->deleteByTagId($this->id);
		if(false !== $do) {
			$do = $this->delete();
		}
		
		return $do;
	}
}
