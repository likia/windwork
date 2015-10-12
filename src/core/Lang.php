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
 * 语言包管理/访问类 
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.lang.html
 * @since       1.0.0
 */
class Lang {
	/**
	 * 语言值列表
	 * @var string
	 */
	public static $lang = array();
	
	/**
	 * 访客使用的语言
	 * @var string
	 */
	protected static $locale = 'zh_CN';

	/**
	 * 设置访客使用的语言
	 * @param string $locale
	 * @throws Exception
	 */
	public static function setLocale($locale) {
		// 语言包存在则设置为访客使用的语言
		if(is_dir(SRC_PATH . "language/{$locale}")) {
			static::$locale = $locale;
		} else {
			throw new Exception('not exists language: '.$locale, Exception::ERROR_SYSTEM_ERROR);
		}
	}
	
	/**
	 * 获取语言
	 * @return string
	 */
	public static function getLocale() {
		return static::$locale;
	}
	
	/**
	 * 添加语言包
	 * 重复的选项将被新的选项替换
	 *
	 * @param string $baseName
	 */
	public static function add($baseName) {
		$locale = static::$locale;		
		$langFile = "language/{$locale}/{$baseName}.php";

		if (!is_file(SRC_PATH.$langFile)) {
			throw new Exception("File \"{$langFile}\" not exists!");
		}
		
		$lang = include_once SRC_PATH . $langFile;
		if ($lang && is_array($lang)) {
			self::$lang = array_merge(self::$lang, $lang);
		}
		return self::$lang;
	}
	
	/**
	 * 获取语言字符串
	 *
	 * @param string $key
	 * @return string
	 */
	public static function get($key) {
		// 语言包下标大写		
		return isset(self::$lang[$key]) ? self::$lang[$key] : $key;
	}
	
	/**
	 * 获取所有已定义的语言变量
	 *
	 * @return array
	 */
	public static function getLangs() {
		return self::$lang;
	}
	
}