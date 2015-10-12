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
 * Windwork定义异常类
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.exception.html
 * @since       1.0.0
 */
class Exception extends \Exception {
	const ERROR_SYSTEM_ERROR = 0;
	
	const ERROR_HTTP_401 = 401;
	const ERROR_HTTP_403 = 403;
	const ERROR_HTTP_404 = 404;
	
	const ERROR_CLASS_TYPE_ERROR        = 1101;
	const ERROR_CLASS_NOT_EXIST         = 1102;
	const ERROR_CLASS_METHOD_NOT_EXIST  = 1103;
	const ERROR_OBJECT_NOT_EXIST        = 1201;
	const ERROR_PARAMETER_TYPE_ERROR    = 1301;
	const ERROR_RETURN_TYPE_ERROR       = 1302;

	/**
	 * 异常构造函数
	 *
	 * @param $message 异常信息
	 * @param $code	        异常码 默认为0
	 */
	public function __construct($message, $code = 0) {
		$message = sprintf($this->messageMapper($code), $message);
		parent::__construct($message, $code);
	}
	
	/**
	 * 自定义异常号的对应异常信息
	 *
	 * @param int $code 异常号
	 * @return string 返回异常号对应的异常组装信息原型
	 */
	protected function messageMapper($code) {
		$messages = array(
			self::ERROR_SYSTEM_ERROR            => 'System error "%s".',
			self::ERROR_CLASS_TYPE_ERROR        => 'Incorrect class type "%s".',
			self::ERROR_CLASS_NOT_EXIST         => 'Unable to create instance for "%s" , class is not exist.',
			self::ERROR_CLASS_METHOD_NOT_EXIST  => 'Unable to access the method "%s" in current class , the method is not exist or is protected.',
			self::ERROR_OBJECT_NOT_EXIST        => 'Unable to access the object in current class "%s" ',
			self::ERROR_PARAMETER_TYPE_ERROR    => 'Incorrect parameter type "%s".',
			self::ERROR_RETURN_TYPE_ERROR       => 'Incorrect return type for "%s".',
		);
		
		return isset($messages[$code]) ? $messages[$code] : '%s';
	}
}

