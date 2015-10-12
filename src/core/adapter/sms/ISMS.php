<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\sms;

/**
 * 短信发送接口
 *
 * @package     core.adapter.sms
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.sms.html
 * @since       1.0.0
 */
interface ISMS {
	/**
	 * 发送短信内容到指定的手机号码号码
	 * @param string $mobile
	 * @param string $content
	 * @return boolean
	 */
	public function send($mobile, $content);

	/**
	 * 发送手机短信
	 * 内容：验证码 短信文本信息
	 * @param number $mobile
	 * @param string $msg 
	 * @param string $type 短信发送类型(自己定义，如注册：reg；绑定手机：wxbind)
	 * @return boolean
	 */
	public function sendSmsCode($mobile, $msg, $type = 'reg');
	
	/**
	 * 验证短信是否正确
	 * @param string $code 验证码
	 * @param number $mobile 
	 * @param string $type 短信发送类型(自己定义，如注册：reg；绑定手机：wxbind)
	 * @return boolean
	 */
	public function checkSmsCode($code, $mobile, $type = 'reg');
}
