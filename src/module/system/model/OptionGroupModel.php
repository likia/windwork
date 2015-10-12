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
 * 系统选项模型
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class OptionGroupModel extends \core\mvc\Model {
	/**
	 */
	protected $table = 'wk_option_group';
	
	public static function getOptionEnabledGroups() {
		$thisObj = new static();
		$rs = $thisObj->select(array('where' => array('enabled', 1), 'order' => 'displayorder'), 0, 999);
		return $rs;
	}
}



