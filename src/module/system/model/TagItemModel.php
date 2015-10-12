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


/**
 * 
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class TagItemModel extends \core\mvc\Model {
	protected $table = 'wk_tag_item';

	public function deleteByTagId($tagId) {
		return $this->deleteBy(array('tagid', $tagId));
	}

	public function deleteByTagIdType($tagId, $type) {
		$whArr = array(
			array('tagid', $tagId),
			array('type', $type)
		);
		
		return $this->deleteBy($whArr);
	}
	
	public function deleteByItemIdType($itemId, $type) {
		$whArr = array(
		    array('itemid', $itemId),
			array('type', $type)
		);
		
		return $this->deleteBy($whArr);
	}
	
	/**
	 * 删除不在标签列表中的标签关联
	 * @param string $tags 多个标签用半角逗号(,)隔开
	 * @param string $type
	 * @param string|int $type
	 */
	public function deleteItemTagNotIn($tags, $type, $itemId) {
		$whArr = array();
		$whArr[] = array('type', $type);
		$whArr[] = array('itemid', $itemId);
		$whArr[] = array('tags', $tags, 'notin');
		// TODO
		if ($tags) {
			$notIn = \core\adapter\db\SqlBuilder::where('t.name', explode(',', $tags), 'notin');
			$rs = self::db()->getAll("SELECT i.tagid FROM wk_tag t, wk_tag_item i WHERE t.id = i.tagid AND {$notIn}");
			if ($rs) {
				$tagIdArr = array();
				foreach ($rs as $r) {
					$tagIdArr[] = $r['tagid'];
				}
				
				$whArr[] = array('tagid', $tagIdArr, 'in');
			}			
		}
		
		return $this->deleteBy($whArr);
	}
}
