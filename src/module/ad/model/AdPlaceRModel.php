<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\ad\model;

use core\Factory;

class AdPlaceRModel extends \core\mvc\Model {
	protected $table = 'wk_ad_place_r';
		
	public function deleteByPlaceId($placeId) {
	    $whArr = array('placeid', $placeId);
		$do = $this->deleteBy($whArr);
		if(false === $do) {
			$this->setErr(Factory::db()->getLastErr());
		}
		
		return $do;		
	}
	
	public function deleteByAdId($id) {
		$whArr = array('id', $id);
		$do    = parent::deleteBy($whArr);
				
		return false !== $do;
	}
	
	
	/**
	 * 根据广告id获取广告关联的广告位id数组
	 * @param int $id
	 */
	public function getPlaceIdArrByAdId($id) {		
		$rs = $this->select(array('fields' => 'placeid', 'where' => array('id', $id)));
		
		$placeids = array();
		foreach ($rs as $place) {
			$placeids[] = $place['placeid'];
		}
		
		return $placeids;
	}
	
	
}