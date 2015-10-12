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
 * 地区模型
 *
 * 
 * 
 * @package     module.system.model
 * @copyright   
 * @author      windwork.org <cmm@windwork.org>
 */
class DistrictModel extends \core\mvc\Model {
    /**
     * 模型对应的数据表
     * @var string
     */
    protected $table = 'wk_district';
    
    /**
     * 
     * @param number $upid
     * @return array
     */
    public function getListByUpid($upid = 0) {
    	$cdt = array(
    		'fields' => 'id, name, upid',
    		'where' => array(
	    		array('upid', $upid)
	    	),
    		'order' => 'displayorder DESC, id ASC'
    	);
    	
    	$list = $this->select($cdt, 0, 200);    	
    	return $list;
    }
    
    /**
     * 根据id列表获取地区列表
     * @param array $idList
     * @return array
     */
    public function getDistrictListInIdList($idList) {
    	if (!$idList) {
    		return array();
    	}
    	$cdt = array(
    		'fields' => 'id, upid, name',
    		'where' => array('id', $idList, 'in'),
    	);
    	
    	$list = array();
    	$rs = $this->select($cdt, 0, 100);
    	foreach ($rs as $row) {
    		$list[$row['id']] = $row;
    	}
    	
    	return $list;
    }
}
