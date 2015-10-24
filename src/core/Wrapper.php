<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core;

/**
 * Wrapper管理器
 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.wrapper.html
 * @since       1.0.0
 */
class Wrapper {
	/**
	 * 
	 * @var array
	 */
	private static $wrappers = array(
		'db' => '\\core\\wrapper\\DB',
	);
	
	/**
	 * 设置wrapper对应的wrapper实现类，必须在core\App实例化之前调用才有效。
	 * 
	 * @param string $path
	 * @param string $class
	 */
	public static function initWrapper($path, $class) {
		$wrapper = static::getWrapperFromPath($path);
		if ($wrapper && $class) {
			static::$wrappers[$wrapper] = $class;
		}		
	}
	
	/**
	 * 注册wrapper
	 * @param string $wrapper
	 * @param string $class
	 * @throws Exception
	 */
	public static function registerWrapper($wrapper, $class = '') {
		$class || $class = static::$wrappers[$wrapper];
		if($wrapper && $class && class_exists($class)) {
			if(!in_array($wrapper, stream_get_wrappers())) {
				stream_register_wrapper($wrapper, $class);
			} 
		} else {
			throw new Exception("Unsupport wrapper: $wrapper!");
		}
	}
	
	/**
	 * 注册运行时使用的自定义wrapper
	 */
	public static function registerAllWrapper() {
		foreach (static::$wrappers as $wrapper => $class) {
			static::registerWrapper($wrapper, $class);
		}
	}
	
	public static function getWrapperFromPath($path) {
		$wrapper = '';
		if(preg_match("|(\\w+)://.*|", $path, $m)) {
			$wrapper = $m[1];
		}
		
		return $wrapper;
	}	
}