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

use core\Config;
use core\util\Validator;
use module\system\model\UploadModel;
use core\Factory;

class AdModel extends \core\mvc\Model {
	protected $table = 'wk_ad';
		
	/**
	 *
	 */
	public function load(){
		if(false === parent::load()) {
			return false;
		}
	
		$this->content = unserialize($this->content);
		
		$rObj = new AdPlaceRModel();
		$rs = $rObj->select(array('fields' => 'placeid', 'where' => array('id', $this->getPkv())));
		
		$this->placeids = array();
		foreach ($rs as $place) {
			$this->placeids[] = $place['placeid'];
		}
		
		return true;
	}
	
	/**
	 * 添加广告
	 */
	public function create(){
		if (!$this->checkForm()) {
			return false;
		}
		
		$this->content = $this->content[$this->type];
		
		if ($this->type == 'image') {
			if(!empty($_FILES['content-image']['tmp_name'])) {		
				$uploadObj = new UploadModel();
				$uploadObj->setTempFile($_FILES['content-image']['tmp_name']);
				$uploadObj->setTempName($_FILES['content-image']['name']);
				$uploadObj->imgWatermark = 0;
				
				if(false === $uploadObj->create()){
					$this->setErr($uploadObj->getErrs());
					return false;
				}
				
				$this->content['src'] = $uploadObj->getUrl();
			} else {
				$this->content['src'] = Config::get('ui_nopic');
			}
		}
	
		if ($this->type == 'flash' && !empty($_FILES['content-flash']['tmp_name'])) {
			$uploadObj = new UploadModel();
			$uploadObj->setTempFile($_FILES['content-flash']['tmp_name']);
			$uploadObj->setTempName($_FILES['content-flash']['name']);
			
			if(false === $uploadObj->create()){
				$this->setErr($uploadObj->getErrs());
				return false;
			}
				
			$this->content['flash'] = $uploadObj->getUrl();
		}

		$this->dateline  = time();
		$this->starttime = strtotime($this->startdate);
		$this->endtime   = strtotime($this->enddate);
		
		$cr = parent::create();
	
		if ($cr && $this->placeid) {
			// 关联广告位
			$placeRObj = new AdPlaceRModel();
			
			foreach ($this->placeid as $adPlaceId) {
				$placeRObj->setPkv(array('id' => $this->getPkv(), 'placeid' => $adPlaceId));
				$placeRObj->create();
			}			
		}
		
		$this->clearCache();
		return $cr;
	}
	
	public function update(){
		if (!$this->checkForm()) {
			return false;
		}
		
		$this->content = $this->content[$this->type];

		if ($this->type == 'image' && !empty($_FILES['content-image']['tmp_name'])) {
			$uploadObj = new UploadModel();
			$uploadObj->setTempFile($_FILES['content-image']['tmp_name']);
			$uploadObj->setTempName($_FILES['content-image']['name']);
			$uploadObj->imgWatermark = 0;
	
			if(false === $uploadObj->create()){
				$this->setErr($uploadObj->getErrs());
				return false;
			}
			$this->content['src'] = $uploadObj->getUrl();
		}
		
		if ($this->type == 'flash' && !empty($_FILES['content-flash']['tmp_name'])) {
			$uploadObj = new UploadModel();
			$uploadObj->setTempFile($_FILES['content-flash']['tmp_name']);
			$uploadObj->setTempName($_FILES['content-flash']['name']);
			
			if(false === $uploadObj->create()){
				$this->setErr($uploadObj->getErrs());
				return false;
			}
				
			$this->content['flash'] = $uploadObj->getUrl();
		}
		
		$this->starttime = strtotime($this->startdate);
		$this->endtime   = strtotime($this->enddate);
		
		$up = parent::update();
		if (false !== $up && $this->placeid) {
			$placeRObj = new AdPlaceRModel();
			$placeRObj->deleteByAdId($this->id);
			
			// 关联广告位
			$placeRObj = new AdPlaceRModel();
				
			foreach ($this->placeid as $adPlaceId) {
				$placeRObj->setPkv(array('id' => $this->id, 'placeid' => $adPlaceId));
				$placeRObj->create();
			}
				
			$this->clearCache();
		}
		
		return $up;
	}
	
	private function checkForm(){
		$rules = array(
			'type'      => array('notEmpty' => '请选择广告内容类型'),
			'placeid'   => array('notEmpty' => '请选择广告位'),
			'name'      => array('notEmpty' => '请输入广告标题'),
			'startdate' => array('notEmpty' => '请选择广告开始时间', 'date' => '广告开始时间格式错误'),
			'enddate'   => array('notEmpty' => '请选择广告结束时间', 'date' => '广告结束时间格式错误'),
		);
		
		$validErrs = array();
		$valid = Validator::Validate($this->toArray(), $rules, $validErrs);
		
		if ($validErrs) {
			$this->setErr($validErrs);
		}
		
		return !(bool)$validErrs;
	}
	
	/**
	 * 根据广告位id获取广告
	 *
	 * @param int $placeId
	 */
	public static function getAdsByPlaceId($placeId, $rows = 20, $timeLimit = 0) {
		$whArr = array(
			array('ad.id', 'r.id', '=', 'field'),
			array('r.placeid', $placeId),			
		);
		$timeLimit && $whArr[] = array(array('ad.dateline', 0), array('OR', array('ad.starttime', $timeLimit, '>'), array('ad.endtime', $timeLimit, '<')));
	    $options = array(
	    	'fields' => 'ad.*, r.placeid',
	    	'table'  => 'wk_ad ad, wk_ad_place_r r',
	    	'where'  => $whArr,
	    	'order' => 'displayorder, id',
	    );
	    
		$thisObj = new static();
		
		$ads = $thisObj->select($options, 0, $rows);
		
		foreach ($ads as $key => $ad) {
			$ads[$key]['content'] = unserialize($ad['content']);
		}
	
		return $ads;
	}

	public function getList($placeId = 0, $offset = 0, $rows = 999){
		if($placeId) {
			$this->getAdsByPlaceId($placeId, $offset, $rows);
		} else {
			$cdt = array(
				'order' => 'displayorder, id',
			);
			$list = parent::select($cdt, $offset, $rows);
		}
		
		foreach ($list as $key => $ad) {
			$list[$key]['content'] = unserialize($ad['content']);
			
			$rObj = new AdPlaceRModel();				
			$rs = $rObj->select(array('fields' => 'placeid', 'where' => array('id', $ad['id'])), 0, 999);
			
			$placeIdArr = array();
			foreach ($rs as $place) {
				$placeIdArr[] = $place['placeid'];
			}
			
			$list[$key]['placeid'] = $placeIdArr;
		}
		
	    return $list;
	}

	/**
	 * 删除广告
	 */
	public function delete() {
		try {
			$do = parent::delete();			
			$rObj = new AdPlaceRModel();
			$rObj->deleteByAdId($this->id);
		} catch (\core\Exception $e) {
			$this->setErr($e->getMessage());
		}
		
		return $do;
	}
	
	public static function clearCache() {
		Factory::cache()->clear('ad');
	}
	
}