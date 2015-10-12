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
 * 后台菜单
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class BannedIPModel extends \core\mvc\Model {
	protected $table = 'wk_banned_ip';

	protected function check() {
		$this->ip1 = (int)$this->ip1;
		$this->ip2 = (int)$this->ip2;
		$this->ip3 = (int)$this->ip3;
		$this->ip4 = (int)$this->ip4;
		
		if(!$this->ip1) {
			$this->setErr('错误的IP');
			return false;
		}
		
		return true;
	}
	
	/**
	 * 添加被禁止的IP
	 */
	public function create() {
		if (!$this->check()) {
			return false;
		}
				 
		$do = parent::create();
		static::clearCache();
		
		return $do;
	}

	/**
	 * 修改被禁止的IP
	 */
	public function update() {	
		if (!$this->check()) {
			return false;
		}
		
		$do = parent::update();	
		static::clearCache();
		
		return $do;	
	}
	
	/**
	 * IP是否已被禁止访问
	 * @param string $ip
	 * @return bool
	 */
	public function isBanned($ip) {
		$cacheKey = "bannedip/$ip";
		if (null === $isBanned = Factory::cache()->read($cacheKey)) {
			list($ip1, $ip2, $ip3, $ip4) = explode('.', $ip);
			
			// TODO 封IP段
			$whArr = array();
			$whArr[] = array('ip1', $ip1);
			$whArr[] = array('ip2', $ip2);
			$whArr[] = array('ip3', $ip3);
			$whArr[] = array('ip4', $ip4);
			
			$isBanned = (bool)$this->count(array('where' => $whArr));			
			Factory::cache()->write($cacheKey, $isBanned);
		} 
		
		return $isBanned;
	}
	
	public static function clearCache() {
		Factory::db()->clear('bannedip');
	}
}
