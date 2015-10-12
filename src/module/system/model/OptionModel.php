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
 * 系统选项模型
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class OptionModel extends \core\mvc\Model {
	/**
	 */
	protected $table = 'wk_option';
	
	/**
	 * 读取所有的配置信息
	 *
	 * @return array
	 */
	public static function getOptions() {
		$options = array();
		$thisObj = new self();
		$rs = $thisObj->select(array('fields' => 'name, value, isarray'), 0, 9999);
		foreach ($rs as $r) {
			$r['isarray'] && $r['value'] = $r['value'] ? unserialize($r['value']) : array();
			$options[$r['name']] = $r['value'];
		}

		return $options;
	}
	
	/**
	 * 获取可在后天编辑的配置项
	 * @param string $group
	 * @return array
	 */
	public function getEditableOptionsByGroup($group) {
		$cdt = array( 
		    'where' => array(array('allowedit', 1), array('group', $group)), 
		    'order' => 'displayorder ASC, name ASC'
		);
		
		$options = $this->select($cdt, 0, 999);
		
		foreach ($options as $k => $r) {
			$r['isarray'] && $options[$k]['value'] = $r['value'] ? unserialize($r['value']) : array();
		}
		
		return $options;
	}
	
	public function create() {}
		
	public function update() {}
	
	/**
	 * 修改配置选项的值
	 * @param string $optionName
	 * @param string $optionValue
	 * @return boolean
	 */
	public static function alterValue($optionName, $optionValue) {
		if (empty($optionName)) {
			return false;
		}

		if(is_array($optionValue)) {
			$optionValue = serialize($optionValue);
		}
		
		$thisObj = new static();
		$exec = $thisObj->setObjId($optionName)->alterField(array('value' => $optionValue));
				
		if(false === $exec) {
			throw new \core\mvc\Exception($thisObj->getLastErr());
		}
		
		self::clearCache();
		
		return true;
	}
		
	public static function clearCache() {
		Factory::cache()->delete('options');
	}
}



