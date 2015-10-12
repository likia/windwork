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
 * 文件缓存操作实现类
 * 
 * @package     core.adapter.cache
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.cache.html
 * @since       1.0.0
 */
class File extends ACache implements ICache, \core\adapter\IFactoryAble {
	const CACHE_SUMMARY = "<?php\n/**\n * Auto generate by windwork cache engine,please don't edit me.\n */\nexit;\n?>";
	
	/**
	 * 锁定
	 *
	 * @param string $key
	 * @return \core\adapter\cache\File
	 */
	protected function lock($key) {
		$cachePath = $this->getCachePath($key);
		$cacheDir  = dirname($cachePath);
		if(!is_dir($cacheDir)) {
		    if(!@mkdir($cacheDir, 0755, true)) {
			    if(!is_dir($cacheDir)) {
			        throw new Exception("Could not make cache directory");
			    }
		    }
		}

		// 设定缓存锁文件的访问和修改时间
		@touch($cachePath . '.lock');
	}
  
	
	/**
	 * 缓存单元是否已经锁定
	 *
	 * @param string $key
	 * @return bool
	 */
	protected function isLocked($key) {
		$cachePath = $this->getCachePath($key);
		clearstatcache();
		return is_file($cachePath . '.lock');
	}
			
	/**
	 * 获取缓存文件
	 *
	 * @param string $key
	 * @return string
	 */
	private function getCachePath($key) {
		$path = $this->cacheDir . "/{$key}.php";
		$path = \core\File::safePath($path);
		return $path;
	}
		
	/**
	 * 设置缓存
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $expire = 0  如果要设置不删除缓存，请设置一个大点的整数
	 * @return \core\adapter\cache\File
	 */
	public function write($key, $value, $expire = 0) {
		if (!$this->enabled) {
			return ;
		}

		if (!$expire) {
			$expire = $this->expire;
		}
	
		self::$execTimes ++;
		self::$writeTimes ++;
		
		$this->checkLock($key);
	
		$data = array('time' => time(), 'expire' => $expire, 'valid' => true, 'data' => $value);
		
		$this->lock($key);
	
		try {
			$this->store($key, $data);
			$this->unlock($key);
		} catch (Exception $e) {
			$this->unlock($key);
			throw $e;
		}
	}
	
	/**
	 * 读取缓存
	 *
	 * @param string $key
	 * @param bool $checkExpired = true 是否检查过期情况
	 * @return mixed
	 */
	public function read($key, $checkExpired = true) {
		if (!$this->enabled) {
			return null;
		}
		
	    self::$execTimes ++;
	    self::$readTimes ++;
	    
	    $this->checkLock($key);
	    
		$cachePath = $this->getCachePath($key);
		if (is_file($cachePath) && is_readable($cachePath)) {
			$data = substr(file_get_contents($cachePath), strlen(static::CACHE_SUMMARY));
			
			if ($data) {
				self::$readSize += strlen($data)/1024;
	
				if($this->isCompress && function_exists('gzdeflate') && function_exists('gzinflate')) {
					$data = gzinflate($data);
				}
	
				$data = unserialize($data);
				 
				$data['isExpired'] = ($data['expire'] && (time() - $data['time']) > $data['expire']) ? true : false;				
			}
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
	 * @return \core\adapter\cache\File
	 */
	public function delete($key) {
		if(empty($key)) {
			return false;
		}
	
		self::$execTimes ++;
	
		$file = $this->getCachePath($key);
		if(is_file($file)) {
			$this->checkLock($key);
			$this->lock($key);
			@unlink($file);
			$this->unlock($key);
		}
	}
	
	/**
	 * 清空指定目录下所有缓存
	 *
	 * BUG: saekv 不支持wrapper访问目录
	 * @param string $dir = ''
	 * @return \core\adapter\cache\File
	 */
	public function clear($dir = '') {
		$dir = $this->getCachePath($dir);
		$dir = dirname($dir);
		
		is_dir($dir) && \core\File::clearDir($dir);
	
		self::$execTimes ++;
	}
	
	/**
	 * 解锁
	 *
	 * @param string $key
	 * @return \core\adapter\cache\File
	 */
	protected function unlock($key) {
		$cachePath = $this->getCachePath($key);
		@unlink($cachePath . '.lock');
	}
	
	
	/**
	 * 缓存变量
	 * 为防止信息泄露，缓存文件格式为php文件，并以"<?php exit;?>"开头
	 *
	 * @param string $key 缓存变量下标
	 * @param string $value 缓存变量的值
	 * @return bool
	 */
	private function store($key, $value) {
		$cachePath = $this->getCachePath($key);
		$cacheDir  = dirname($cachePath);
	
		if(!is_dir($cacheDir) && !@mkdir($cacheDir, 0755, true)) {
			throw new Exception("Could not make cache directory");
		}
	
		$value = serialize($value);
	
		if($this->isCompress && function_exists('gzdeflate') && function_exists('gzinflate')) {
			$value = gzdeflate($value);
		}
	
		self::$writeSize += strlen($value)/1024;
		
	    return @file_put_contents($cachePath, static::CACHE_SUMMARY. $value);
	}
	
}
