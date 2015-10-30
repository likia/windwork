<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\cache;

/**
 * 缓存操作接口
 * 实现：file、saekv、memcache、db
 *
 * @package     core.adapter.cache
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.cache.html
 * @since       1.0.0
 */
interface ICache {

	/**
	 * 设置缓存
	 *
	 * @param string $cacheKey
	 * @param mixed $value
	 * @param int $expire = 0  单位秒，为0则使用配置文件中的缓存时间设置（3600秒），如果要设置不删除缓存，请设置一个大点的整数
	 * @return \core\adapter\cache\CacheInterface
	 */
	public function write($cacheKey, $value, $expire = 0);
	
	/**
	 * 读取缓存
	 *
	 * @param string $cacheKey
	 * @param bool $checkExpired = true 是否检查过期情况
	 * @return mixed 不存在的缓存返回 null
	*/
	public function read($cacheKey, $checkExpired = true);
	
	/**
	 * 删除缓存
	 *
	 * @param string $cacheKey
	 * @return \core\adapter\cache\ACache
	*/
	public function delete($cacheKey);
	
	/**
	 * 清空指定目录下的所有缓存
	 * 
	 * @param string $dir = ''
	 * @return \core\adapter\cache\CacheInterface
	 */
	public function clear($dir = '');
}

