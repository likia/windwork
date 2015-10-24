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
 * SaeKVDB缓存操作实现类
 * 
 * @package     core.adapter.cache
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.cache.html
 * @since       1.0.0
 */
class SaeKV extends ACache implements ICache, \core\adapter\IFactoryAble {
	/**
	 * SaeKV实例
	 * @var \SaeKV
	 */
	protected $kv = null;
	
	/**
	 * 构造函数初始化缓存操作实例
	 * @param array $cfg
	 * @throws \core\adapter\cache\Exception
	 */
	public function __construct(array $cfg) {
		parent::__construct($cfg);
		$this->kv = new \SaeKV();
		if(!$this->kv->init()) {
			throw new Exception('请启用	KVDB');
		}
	}
	
	/**
	 * 锁定
	 *
	 * @param string $key
	 * @return \core\adapter\cache\SaeKV
	 */
	protected function lock($key) {
		$cachePath = $this->getCachePath($key);
		$this->kv->set($cachePath . '.lock', 1);
	}
  
	
	/**
	 * 缓存单元是否已经锁定
	 *
	 * @param string $key
	 * @return bool
	 */
	protected function isLocked($key) {
		$cachePath = $this->getCachePath($key);
		return (bool)$this->kv->get($cachePath . '.lock');
	}
			
	/**
	 * 获取缓存文件
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getCachePath($key) {
		$key = preg_replace('/\.\.+/', '.', $key);
		$path = $this->cacheDir . '/' . ltrim($key, '/');
		return trim($path);
	}
	
	
	/**
	 * 设置缓存
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $expire  如果要设置不删除缓存，请设置一个大点的整数
	 * @return \core\adapter\cache\SaeKV
	 */
	public function write($key, $value, $expire = 0) {
		if (!$this->enabled) {
			return null;
		}

		if (!$expire) {
			$expire = $this->expire;
		}
			
		$data = array('time' => time(), 'expire' => $expire, 'valid' => true, 'data' => $value);
		
		$data = serialize($data);
		if($this->isCompress && function_exists('gzdeflate') && function_exists('gzinflate')) {
			$data = gzdeflate($data);
		}

		$this->checkLock($key);
		$this->lock($key);
		$this->kv->set($this->getCachePath($key), $data);		
		$this->unlock($key);

		self::$execTimes ++;
		self::$writeTimes ++;
		self::$writeSize += strlen($data)/1024;
		
		return $this;
	}
	
	/**
	 * 读取缓存
	 *
	 * @param string $key
	 * @param bool $checkExpired 是否检查过期情况
	 * @return mixed
	 */
	public function read($key, $checkExpired = true) {
		if (!$this->enabled) {
			return ;
		}
		
	    self::$execTimes ++;
	    self::$readTimes ++;
	    
	    $this->checkLock($key);
	    
		$cachePath = $this->getCachePath($key);
		if (false !== ($data = $this->kv->get($cachePath))) {
			self::$readSize += strlen($data)/1024;

			if($this->isCompress && function_exists('gzdeflate') && function_exists('gzinflate')) {
				$data = gzinflate($data);
			}

			$data = unserialize($data);
			 
			$data['isExpired'] = ($data['expire'] && (time() - $data['time']) > $data['expire']) ? true : false;
		}
			 
		if (!empty($data) && (!$checkExpired || ($data['valid'] && !$data['isExpired']))) {
			return $data['data'];
		}
	
		return null;
	}
		
	/**
	 * 删除缓存
	 *
	 * @param string $key
	 * @return \core\adapter\cache\SaeKV
	 */
	public function delete($key) {
		if(empty($key)) {
			throw new Exception('错误的参数！');
		}
	
		self::$execTimes ++;

		$this->checkLock($key);
		
		try {
			$this->kv->delete($this->getCachePath($key));
			$this->unlock($key);
		} catch (Exception $e) {
			$this->unlock($key);
			throw $e;
		}
	}
	
	/**
	 * 清空指定目录的缓存，不指定则清空所有缓存
	 * @param string $dir
	 * @return \core\adapter\cache\SaeKV
	 */
	public function clear($dir = '') {
		$dir = $this->getCachePath($dir);
		$dir = rtrim($dir, '/');
		$startKey = null;
		while (true) {
			$ret = $this->kv->pkrget($dir, 20, $startKey);
			
			if ($ret) {
				break;
			}
			
			foreach($ret as $key => $vtmp) {
				$this->kv->delete($key);
			}
			
			end($ret);
			$startKey = key($ret);
		}
		
		self::$execTimes ++;
		
		return $this;
	}
	
	/**
	 * 解锁
	 *
	 * @param string $key
	 * @return \core\adapter\cache\SaeKV
	 */
	protected function unlock($key) {
		$cachePath = $this->getCachePath($key);
		$this->kv->delete($cachePath . '.lock');
		
		return $this;
	}		
}
