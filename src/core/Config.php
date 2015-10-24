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
 * 系统配置操作类
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.config.html
 * @since       1.0.0
 */
final class Config {
	/**
	 * 配置选项
	 * 
	 * @var array
	 */
	protected static $configs = array();
		
	/**
	 * 设置配置选项
	 *
	 * @param string $name
	 * @param string $value
	 */
	public static function set($name, $value) {
		self::$configs[$name] = $value;
	}
	
	/**
	 * 读取变量
	 *
	 * @param string $name
	 * @return string
	 */
	public static function get($name) {
		return isset(self::$configs[$name]) ? self::$configs[$name] : null;
	}
	
	/**
	 * 读取所有的配置信息
	 * 可使用引用调用
	 *
	 * @return array
	 */
	public static function &getConfigs() {
		return self::$configs;
	}
	
	/**
	 * 覆盖设置配置信息内容
	 * @param array $configs
	 */
	public static function setConfigs($configs) {
		self::$configs = $configs;
	}
}
