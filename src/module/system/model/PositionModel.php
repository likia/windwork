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
class PositionModel extends \core\mvc\Model {
	protected $table = 'wk_position';
	
	/**
	 * 添加新的推荐位
	 */
	public function create() {
		if (empty($this->name)) {
			$this->setErr('请输入推荐位名称。');
			return false;
		}
		
		$do = parent::create();	
		$do && self::clearCache();
		
		return $do;
	}
	
	/**
	 * 更新推荐位
	 */
	public function update(){
		if (empty($this->name)) {
			$this->setErr('请输入推荐位名称。');
			return false;
		}
		
		$do = parent::update();
		
		$do && self::clearCache();
		
		return $do;		
	}
	

	/**
	 * 删除一个推荐位
	 * 
	 * @return bool
	 */
	public function delete() {
		if(parent::delete()) {
			$positionDataObj = new PositionDataModel();
			$positionDataObj->deleteByPosid($this->id);			
		} else {
			return false;
		}	
	}
	
	/**
	 * 获取所有推荐位
	 * 
	 * @return array
	 */
	public function getPositions() {
		if(null === ($positions = Factory::cache()->read('positions'))) {
			$rs = $this->select(array('order' => 'displayorder, id'), 0, 999);
			foreach ($rs as $item) {
				$positions[$item['id']] = $item;
			}
			
			Factory::cache()->write('positions', $positions);
		}
		
		return $positions;
	}

	/**
	 * 获取推荐位信息及数据
	 * 返回错误信息格式：array('error' => '错误信息')
	 * @todo 参数未实现
	 * @param int $posid
	 * @return array
	 */
	public static function getPositionData($posid, $type = '', $cid = null, $mustPic = false) {
		$posKey = "position/$posid";
		$position = \core\Factory::cache()->read($posKey);
	
		if (!$position) {
			$position = new \module\system\model\PositionModel();
			if($position->setPkv($posid)->load()) {
				// 获取推荐位数据
				$positionDataObj = new \module\system\model\PositionDataModel();
				$position->data = $positionDataObj->getPositionData($posid, $position->shownum);
					
				$position = $position->toArray(); // 返回要求数组格式
			} else {
				$position = array('error' => '推荐位不存在');
			}
	
			\core\Factory::cache()->write($posKey, $position);
		}
	
		return $position;
	}
	
	public static function clearCache() {
		Factory::cache()->delete('positions');
		Factory::cache()->clear('position');
	}
	
}