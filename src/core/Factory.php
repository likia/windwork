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
 * 可选实现库的工厂对象
 * cache,captcha,crypt,db,image,mail,log,mq,session等操作的实例创建的统一管理类
 * 相关默认操作类在config.php中配置
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.factory.html
 * @since       1.0.0
 */
class Factory {
	/**
	 * 工厂对象实例
	 * @var array
	 */
	protected static $instance = array();
	
	/**
	 * 获取组件工厂对象实例
	 *
	 * @param string $type 获取实例类型
	 * @param string $class 实例的置配器
	 * @param array $cfg
	 * @return \core\Object
	 */
	public static function getInstance($type, $class = '', $cfg = array()) {
		// 获取带命名空间的类名
		$class   = empty($class) ? Config::get("factory_".strtolower($type)) : $class;
		$class   = "\\core\\adapter\\{$type}\\" . ucfirst($class);		
		$scope   = $class;
		
		$cfg && $scope .= md5(serialize($cfg));
		$cfg || $cfg = Config::getConfigs();
		
		// 如果该类实例不存在则创建
		if(empty(static::$instance[$scope])) {
		    static::$instance[$scope] = new $class($cfg);
		}
		
		return static::$instance[$scope];
	}	

	/**
	 * 获取数据库操作对象实例
	 * 
	 * @param string $class 数据库操操作类
	 * @param array $cfg
	 * @return \core\adapter\db\IDB
	 */
	public static function db($class = '', $cfg = array()) {
		return static::getInstance('db', $class, $cfg);
	}
	
	/**
	 * 获取图片处理对象实例
	 * 
	 * @param string $class 图片处理类
	 * @param array $cfg
	 * @return \core\adapter\image\IImage
	 */
	public static function image($class = '', $cfg = array()) {
		return static::getInstance('image', $class, $cfg);
	}

	/**
	 * 获取消息队列操作对象实例
	 * 
	 * @param string $class 消息队列操作类
	 * @param array $cfg
	 * @return \core\adapter\mq\IMQ
	 */
	public static function mq($class = '', $cfg = array()) {
		return static::getInstance('mq', $class, $cfg);
	}

	/**
	 * 获取Session操作对象实例
	 * 
	 * @param string $class 缓存操作类
	 * @param array $cfg
	 * @return \core\adapter\session\ISession
	 */
	public static function session($class = '', $cfg = array()) {
		return static::getInstance('session', $class, $cfg);
	}

	/**
	 * 发送电子邮件实例
	 *
	 * @param string $class
	 * @param array $cfg
	 * @return \core\adapter\mailer\IMailer
	 */
	public static function mailer($class = '', $cfg = array()) {
		return static::getInstance('mailer', $class, $cfg);
	}

	/**
	 * 日志对象实例
	 *
	 * @param string $class
	 * @param array $cfg
	 * @return \core\adapter\logger\ILogger
	 */
	public static function logger($class = '', $cfg = array()) {
		return static::getInstance('logger', $class, $cfg);
	}
	
	/**
	 * 可逆加密解密对象实例
	 *
	 * @param string $class
	 * @param array $cfg
	 * @return \core\adapter\crypt\ICrypt
	 */
	public static function crypt($class = 'AzDG', $cfg = array()) {
		return static::getInstance('crypt', $class, $cfg);
	}

	/**
	 * 验证码对象实例
	 *
	 * @param string $class
	 * @param array $cfg
	 * @return \core\adapter\captcha\ICaptcha
	 */
	public static function captcha($class = '', $cfg = array()) {
		return static::getInstance('captcha', $class, $cfg);
	}
	
	/**
	 * print_r 输出已经创建的实例
	 * 
	 */
	public static function trace() {
		print_r(static::$instance);
	}

	/**
	 * 获取默认缓存对象实例
	 * @param array $class
	 * @param array $cfg
	 * @return \core\adapter\cache\ICache
	 */
	public static function cache($class = '', $cfg = array()) {
		return static::getInstance('cache', $class, $cfg);
	}
	
	/**
	 * 发送短信组件
	 * @param string $class = ''
	 * @param array $cfg = array()
	 * @throws \core\Exception
	 * @return \core\adapter\sms\ISMS
	 */
	public static function sms($class = '', $cfg = array()) {
		empty($class) && $class = Config::get("factory_sms");
		empty($cfg) && $cfg = Config::get("sms");
		
		return static::getInstance('sms', $class, $cfg);
	}
}
