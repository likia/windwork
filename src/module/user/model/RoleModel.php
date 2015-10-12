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

use core\Factory;

/**
 * 
 * @package     module.user.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class RoleModel extends \core\mvc\Model {
	public $table = 'wk_user_role';

	/**
	 * 添加角色
	 *
	 * @return bool
	 */
	public function create() {
		if(empty($this->type)) {
			$this->setErr('请选择角色类型！');
			return false;
		}
	
		if(empty($this->name)) {
			$this->setErr('角色名称必填！');
			return false;
		}
	
		
		$do = parent::create();
		
		if(false !== $do) {
			static::clearCache();
			// TODO添加角色权限到权限控制列表数据表
		}
		
		return $do;
	}

	/**
	 * 编辑角色
	 *
	 * @return bool
	 */
	public function update() {
		if(empty($this->name)) {
			$this->setErr('角色名称必填！');
			return false;
		}
		
		$this->addLockFields('type'); // 不允许修改角色类型
		
		if(false === parent::update()) {
			return false;
		}
		
		static::clearCache();
		$this->load();
		return true;
	}
	
	/**
	 * 根据用户类型获取角色列表
	 * @param string $type admin|ext|member，默认==null获取所有角色类型用户
	 * @param bool $enabled 是否已启用，默认==null获取所有
	 * @return array
	 */
	public function getRoles($type = null, $enabled = null) {
		$roles = array();
		
		$cacheKey = 'user/role/roles';		
		$type !== null && $cacheKey .= "-t_{$type}";
		$enabled !== null && $cacheKey .= "-e_{$enabled}";
		
		if(!$roles = Factory::cache()->read($cacheKey)) {
			$whArr = array();		
			if($type) {
				$whArr[] = array('type', $type);
			}
			$enabled !== null && $whArr[] = array('disabled', !$enabled);
			
			$cdt = array(
			    'where' => $whArr,
				'order' => 'type, displayorder ASC, roid ASC',
			);
			
			$rs = $this->select($cdt, 0, 999);
		    foreach ($rs as $r) {
				$roles[$r['roid']] = $r;
			}
			
			Factory::cache()->write($cacheKey, $roles);
		}
				
		return $roles;
	}
	
	/**
	 * 取得已启用的所有角色
	 * 
	 * @return array
	 */
	public function getEnabledRoles() {
		return $this->getRoles(null, true);
	}
	
	/**
	 * 根据角色类型获取角色
	 * @param string $type admin|ext|member
	 * @return array
	 */
	public function getRolesByType($type) {
		return $this->getRoles($type);
	}
	
	/**
	 * 根据角色类型获取已启动的角色
	 * @param string $type admin|ext|member
	 * @return array
	 */
	public function getEnabledRolesByType($type) {
		return $this->getRoles($type, true);
	}
		
	/**
	 * 删除角色
	 * 
	 * return bool
	 */
	public function delete() {
		$this->loaded || $this->load();
		
		if (!$this->allowdel) {
			$this->setErr('不允许删除该角色！');
			return false;
		}

		static::clearCache();
		return parent::delete();
	}
	
	public static function clearCache() {
		Factory::cache()->clear('user/role');
	}
}

