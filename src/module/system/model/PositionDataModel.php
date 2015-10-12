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

/**
 *
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class PositionDataModel extends \core\mvc\Model {
	protected $table = 'wk_position_data';

	/**
	 * 新增推荐位一条内容
	 */
	public function create() {
		if (empty($this->title)) {
			$this->setErr('请输入标题');
			return false;
		}

		if (empty($this->posid)) {
			$this->setErr('请选择推荐位。');
			return false;
		}
		
		
		$do = parent::create();	
		$do && self::clearCache();
		
		return $do;
	}
	
	/**
	 * 更新推荐位一条内容
	 */
	public function update(){
		if (empty($this->title)) {
			$this->setErr('请输入标题。');
			return false;
		}
		
		if (empty($this->posid)) {
			$this->setErr('请选择推荐位。');
			return false;
		}
				
		$do = parent::update();
		
		$do && self::clearCache();
		
		return $do;		
	}

	/**
	 * 获取推荐信息，按显示排顺序、推荐时间逆序排序
	 * @param int $posid
	 * @param int $rows
	 * @return array
	 */
	public function getPositionData($posid = 0, $rows = 999) {
		$cdt = array(
			'where' => $posid ? array('posid', $posid) : '',
			'order' => 'displayorder DESC, pushtime DESC'
		);
		
		$positionData = $this->select($cdt, 0, $rows);
		
		return $positionData;
	}
	
	/**
	 * 删除一个主题的推荐内容
	 * @param string $type
	 * @param int|string $id
	 * @return bool
	 */
	public function deleteByTypeItem($type, $id) {
		$cdt = array();
		$cdt[] = array('type', $type);
		$cdt[] = array('item', $id);
		$do = $this->deleteBy($cdt);
		
		$do && static::clearCache();
		
		return $do;
	}
	
	public function getPosIdsByTypeItem($type, $item) {
		$cdt = array(
			'fields' => 'posid',
			'where' => array(array('type', $type), array('item', $item)),
			'order' => 'posid ASC'
		);
		$rs = $this->select($cdt, 0, 999);
				
		$ids = array();
		if($rs) {
			foreach ($rs as $r) {
				$ids[] = $r['posid'];
			}
		}
		
		return $ids;
	}
	
	/**
	 * 根据推荐位删除推荐内容
	 * @param int $posId
	 * @return bool
	 */
	public function deleteByPosid($posId) {
		$whereArr = array('posid', $posId);
		$do = $this->deleteBy(array('where' => $whereArr));
		$do && static::clearCache();
		
		return $do;
	}
		
	/**
	 * 推荐位是否存在商品
	 * @param int $gid
	 * @param int $posid
	 * @return bool
	 */
	public function itemInPos($posId, $itemId, $type = '') {
		$whArr = array(
			array('posid', $posId),
			array('item',  $itemId),
		);
		
		$type && $whArr[] = array('type', $type);
		 
		return (bool)($this->count(array('where' => $whArr)));
	}

	/**
	 * 修改推荐信息系那是顺序
	 * @param int $displayOrder
	 * @return bool
	 */
	public function updateDisplayOrder($displayOrder) {
		if(!$this->getObjId()) {
			$this->setErr('请设置id！');
			return false;
		}
	
		$item = array('displayorder' => $displayOrder);
		
		$do = $this->alterField($item);
		
		self::clearCache();
		
		return $do;
	}
	
	/**
	 * 根据id删除多条推荐信息
	 * @param array $ids
	 * @return Ambigous <boolean, number>
	 */
	public function deleteByIds($ids) {
		$do = $this->deleteBy(array('id', $ids));

		self::clearCache();
		
		return $do;
	}
	
	public static function clearCache() {
		Factory::cache()->clear('position');
	}
	
}