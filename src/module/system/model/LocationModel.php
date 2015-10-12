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

use core\Factory;
use core\util\Tree;

/**
 * 地理位置标注点
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class LocationModel extends \core\mvc\Model {
	protected $table = 'wk_location';

	public function create() {
		if(!$this->name) {
			$this->setErr('请输入名称');
			return false;
		}
		
		$list = $this->getTree();

		// 经纬度
		if (!empty($this->lnglat) && preg_match("/([0-9\\.]+),([0-9\\.]+)/", $this->lnglat)) {
			list($this->lng, $this->lat) = explode(',', $this->lnglat);
		}
		
		if($this->upid && isset($list[$this->upid])) {
			$this->level = $list[$this->upid]['level'] + 1;
		} else {
			$this->level = 1;
		}
		
		$do = parent::create();

		static::clearCache();
		return $do;
	}
	
	public function update() {	
		if(!$this->name) {
			$this->setErr('请输入名称');
			return false;
		}
				
		$list = $this->getTree();

		// 经纬度
		if (!empty($this->lnglat) && preg_match("/([0-9\\.]+),([0-9\\.]+)/", $this->lnglat)) {
			list($this->lng, $this->lat) = explode(',', $this->lnglat);
		}		
		
		if($this->upid && isset($list[$this->upid]) && isset($list[$this->id])) {
			// 不允许选择自己的子分类作为上级分类
			if (in_array($this->upid, $list[$this->id]['descendantIdArr'])) {
				$this->setErr('不能选择自己或自己的子分类为上级分类！');
				return false;
			}
			
			$this->level = $list[$this->upid]['level'] + 1;
		} else {
			$this->level = 1;
		}
		
		$do = parent::update();
		static::clearCache();
		return $do;
	}
	
	/**
	 * 获取树形结构
	 *
	 * @return array
	 */
	public function getTree() {
		if(!$r = Factory::cache()->read('location')) {
			
			$rs = $this->select(array('order' => 'displayorder ASC, id ASC'), 0, 9999);
	
			$tree = new Tree();
			$tree->set($rs, 'id', 'upid');
			$r = $tree->get();
			
			Factory::cache()->write('location', $r);
		}
		//print_r($r);
		return $r;
	}
			
	public function delete() {
		$tree = $this->getTree();
			
		if(!isset($tree[$this->id])) {
			$this->setErr('该地址不存在');
			return false;
		}
	
		$item = $tree[$this->id];
		
		if ($item['chileArr']) {
			$this->setErr('该地址还有子地址，需要先删除子地址才能删除。');
			return false;
		}
	
		$do =  parent::delete();
		
		static::clearCache();
		
		return $do;
	}
	
	/**
	 * 修改显示顺序
	 * @param int $id
	 * @param int $value
	 */
	public function alterDisplayOrder($id, $value) {
		$m = new static();
		$m->setPkv($id);
		$do = $m->alterField(array('displayorder' => $value));
		
		static::clearCache();
		
		return $do;
	}

	/**
	 * 
	 */
	public function clearCache(){
		Factory::cache()->delete('location');
	}
}
