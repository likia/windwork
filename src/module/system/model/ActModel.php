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
 * 功能列表
 *
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ActModel extends \core\mvc\Model {
	protected $table = 'wk_act';
	
	public function create() {
		if(empty($this->name)) {
			$this->setErr('功能名称不能为空');
			return false;
		}
		
		if(empty($this->mod)) {
			$this->setErr('功能所属模块不能为空');
			return false;
		}
		
		if(empty($this->ctl)) {
			$this->setErr('功能所属控制器不能为空');
			return false;
		}
		
		if(empty($this->act)) {
			$this->setErr('功能方法不能为空');
			return false;
		}

		$this->mod = strtolower($this->mod);
		$this->ctl = strtolower($this->ctl);
		$this->act = strtolower($this->act);
		
		$do = parent::create();	
			
		static::clearCache();
		
		return $do;
	}

	/**
	 * 
	 * @param string $name
	 * @param string $mod
	 * @param string $ctl
	 * @param string $act
	 */
	public function add($name, $mod, $ctl, $act) {
		$obj = new self();
		$arr = array(
			'name' => $name,
			'mod'  => strtolower($mod),
			'ctl'  => strtolower($ctl),
			'act'  => strtolower($act),
		);
		
		$this->fromArray($arr);
		
		return $this->create();
	}
	
	/**
	 * 移除模块功能
	 * 
	 * @param string $mod
	 * @throws \core\mvc\Exception
	 */
	public function removeByMod($mod) {
		return $this->deleteBy(array('mod', strtolower($mod)));
	}
	
	/**
	 * 获取模块功能列表
	 * 
	 * @param string $mod
	 * @return array
	 */
	public function getActsByMod($mod) {
		$mod = strtolower($mod);
		
		$cacheKey = 'acts/getActsByMod/'.$mod;
		$acts = Factory::cache()->read($cacheKey);
		if (!$acts) {
			$opt = array(
				'fields' => 'name, ctl,act',
				'where'  => array('mod', $mod),
				'order'  => 'mod, ctl, act',
			);
			
			$rs = $this->select($opt, 0, 999);
			$acts = array();
			if ($rs) {
				foreach ($rs as $r) {
					$acts[$r['ctl']][$r['act']] = $r['name'];
				}
				
				Factory::cache()->write($cacheKey, $acts);
			}
			
		}
				
		return $acts;
	}
	
	public static function clearCache() {
		Factory::cache()->delete('acts');
	}
}

