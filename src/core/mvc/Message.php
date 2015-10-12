<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\mvc;

/**
 * 设置提示信息类，将在视图中显示 
 * 
 * @package     core.mvc
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.mvc.message.html
 * @since       1.0.0
 */
class Message {
	/**
	 * 
	 * @var string
	 */
	const TYPE_OK = 'ok';

	/**
	 *
	 * @var string
	 */
	const TYPE_WARN = 'warn';

	/**
	 *
	 * @var string
	 */
	const TYPE_ERR = 'err';
	

	/**
	 *
	 * @var array
	 */
	private static $messages = array(
		self::TYPE_OK   => array(),
		self::TYPE_WARN => array(),
		self::TYPE_ERR  => array(),
	);
	
	/**
	 * 是否有提示信息
	 * 
	 * @return bool
	 */
	public static function hasMessage(){
		return static::hasErr() || static::hasWarn() || static::hasOK();
	}
	
	/**
	 * 获取所有提示信息
	 * 
	 * @return array
	 */
	public static function getMessages(){
		return static::$messages;
	}
	
	/**
	 * 设置错误信息
	 * 
	 * @param string|array $msg
	 * @param int $code 状态码，当$msg为标量的时候有效
	 */
	public static function setErr($msg, $code = null){
		static::setMessage(static::TYPE_ERR, $msg, $code);
	}
	
	/**
	 * 设置警告信息
	 * 
	 * @param string|array $msg
	 * @param int $code 状态码，当$msg为标量的时候有效
	 */
	public static function setWarn($msg, $code = null){
		static::setMessage(static::TYPE_WARN, $msg, $code);	
	}
	
	/**
	 * 设置”操作正确“提示信息
	 * 
	 * @param string|array $msg
	 * @param int $code 状态码，当$msg为标量的时候有效
	 */
	public static function setOK($msg, $code = null){
		static::setMessage(static::TYPE_OK, $msg, $code);
	}
	
	/**
	 * 是否有警告信息
	 * 
	 * @return bool
	 */
	public static function hasWarn(){
		return !empty(static::$messages[static::TYPE_WARN]);
	}
	
	/**
	 * 是否有正确操作信息
	 * 
	 * @return bool
	 */
	public static function hasOK(){
		return !empty(static::$messages[static::TYPE_OK]);
	}
	
	/**
	 * 是否有错误信息
	 * 
	 * @return bool
	 */
	public static function hasErr(){
		return !empty(static::$messages[static::TYPE_ERR]);
	}
	
	/**
	 * 获取所有警告信息
	 * 
	 * @return array
	 */
	public static function getWarns(){
		return static::$messages[static::TYPE_WARN];
	}

	/**
	 * 获取所有”正确操作“信息
	 * 
	 * @return array
	 */
	public static function getOKs(){
		return static::$messages[static::TYPE_OK];
	}
	
	/**
	 * 获取所有错误信息
	 * 
	 * @return array
	 */
	public static function getErrs(){
		return static::$messages[static::TYPE_ERR];
	}
	
	/**
	 * 设置消息内容
	 * @param string $type
	 * @param string|array $msg
	 * @param string $code
	 */
	private static function setMessage($type, $msg, $code = null) {
		if (!isset(static::$messages[$type])) {
			static::$messages[$type] = array();
		}
		
		if(is_scalar($msg)) {
			if($code) {
				static::$messages[$type][$code] = $msg;
			} else {
				static::$messages[$type][] = $msg;
			}
			
		} else if(is_array($msg)) {
			foreach ($msg as $str) {
				static::$messages[$type][] = $str;
			}
		}
	}
	
	/**
	 * 清空所有消息
	 */
	public static function clear() {
		foreach (static::$messages as $key => $tmp) {
			if (in_array($key, array(static::TYPE_OK, static::TYPE_WARN, static::TYPE_ERR))) {
				static::$messages[$key] = array();
			} else {
				unset(static::$messages[$key]);
			}
		}
	}
}
