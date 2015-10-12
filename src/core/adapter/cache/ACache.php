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
 * 缓存操作抽象类
 * 实现：file、saekv、memcache、db
 * 
 * @package     core.adapter.cache
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.cache.html
 * @since       1.0.0
 */
abstract class ACache {
	/**
	 * 缓存读取次数
	 * @var int
	 */
	public static $readTimes  = 0;
	
	/**
	 * 缓存写入次数
	 * @var int
	 */
	public static $writeTimes = 0;
	
	/**
	 * 缓存读写总次数
	 * @var int
	 */
	public static $execTimes  = 0;
	
	/**
	 * 当前请求读取缓存内容的总大小(k)
	 * @var float
	 */
	public static $readSize   = 0;

	/**
	 * 当前请求写入取缓存内容的总大小(k)
	 * @var float
	 */
	public static $writeSize  = 0;
	
	/**
	 * 是否启用缓存
	 * @var bool
	 */
	protected $enabled = true;
	
	/**
	 * 是否压缩缓存内容
	 *
	 * @var bool
	 */
	protected $isCompress = true;
	
	/**
	 * 缓存过期时间长度(s)
	 *
	 * @var int
	 */
	protected $expire = 3600;
	
	/**
	 * 缓存目录
	 *
	 * @var string
	 */
	protected $cacheDir = 'data/cache';

	/**
	 * 构造函数中设置缓存实例相关选项
	 * @param array $cfg
	 */
	public function __construct($cfg) {
		$this->enabled = $cfg['cache_enabled'];
		$this->isCompress = $cfg['cache_compress'];
		$this->setCacheDir($cfg['cache_dir']);
		$this->setExpire($cfg['cache_expire']);
	}
	
	/**
	 * 设置是否压缩缓存内容
	 * @param bool $isCompress
	 * @return \core\adapter\cache\ACache
	 */
	public function setIsCompress($isCompress) {
		$this->isCompress = (bool)$isCompress;
		return $this;
	}
	
	/**
	 * 设置缓存目录
	 * @param string $dir
	 * @return \core\adapter\cache\ACache
	 */
	public function setCacheDir($dir){
		$this->cacheDir = rtrim($dir, '/');
	
		if(!is_dir($this->cacheDir)) {
			@mkdir($this->cacheDir, 0755, true);
		}
		
		return $this;
	}

	/**
	 * 设置缓存默认过期时间（s）
	 *
	 * @param int $expire
	 * @return \core\adapter\cache\ACache
	 */
	public function setExpire($expire) {
		$this->expire = (int) $expire;
		return $this;
	}

	/**
	 * 确保不是锁定状态
	 * 最多做$tries次睡眠等待解锁，超时则跳过并解锁
	 *
	 * @param string $key 缓存下标
	 * @return \core\adapter\cache\ACache
	 */
	protected function checkLock($key) {
		if ($this->isLocked($key)) {
			$tries = 16;
			$count = 0;
			do {
				usleep(100);
				$count ++;
			} while ($count <= $tries && $this->isLocked($key));  // 最多做$tries次睡眠等待解锁，超时则跳过并解锁
		
			$this->isLocked($key) && $this->unlock($key);		
		}
		
		return $this;
	}
	
	/**
	 * 缓存单元是否已经锁定
	 *
	 * @param string $key
	 * @return bool
	 */
	abstract protected function isLocked($key);
	
	/**
	 * 锁定
	 *
	 * @param string $key
	 * @return \core\adapter\cache\ACache
	*/
	abstract protected function lock($key);
	
	/**
	 * 解锁
	 *
	 * @param string $key
	 * @return \core\adapter\cache\ACache
	*/
	abstract protected function unlock($key);
}

