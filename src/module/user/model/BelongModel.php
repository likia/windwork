<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\user\model;

use core\mvc\Exception;

/**
 * 用户所属角色（1用户-多角色）
 * 
 * @package     module.user.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class BelongModel extends \core\mvc\Model {
	public $table = 'wk_user_belong_role';

	/**
	 * 获取用户所属角色
	 * @param int $uid
	 * @return array(roid1, 2, 3)
	 */
	public function getUserRoids($uid) {
		$roids = array();
		$rs = $this->select(array('fields' => 'roid', 'where' => array('uid', $uid)), 0, 999);
		$rs && $roids = array_column($rs, 'roid');
		
		return $roids;
	}
	
	/**
	 * 添加用户所属角色
	 *
	 * @return bool
	 */
	public function create() {
		if(empty($this->roid)) {
			$this->setErr('请选择角色！');
			return false;
		}
		
		if(empty($this->uid)) {
			$this->setErr('请选择用户！');
			return false;
		}
	
		return parent::create();
	}
	
	/**
	 * 编辑用户所属角色
	 * @param int $uid
	 * @return Ambigous <boolean, number>
	 */
	public function deleteByUid($uid) {
		return $this->deleteBy(array('uid', $uid));
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function update() {
		throw new Exception('不支持该方法');
	}
	
}

