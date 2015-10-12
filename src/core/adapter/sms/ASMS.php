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

use core\Object;
use core\util\Validator;

/**
 * 短信发送抽象类
 * 
 * @package     core.adapter.sms
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.sms.html
 * @since       1.0.0
 */
abstract class ASMS extends Object {
		
	/**
	 * 验证短信是否正确
	 * @param string $code 验证码
	 * @param number $mobile 
	 * @param string $type 短信发送类型(自己定义，如注册：reg；绑定手机：wxbind)
	 * @return boolean
	 */
	public function checkSmsCode($code, $mobile, $type = 'reg') {
		$sendSmsKey = $type . '-sendSms';
		
		// 去掉过期短信
		foreach (@$_SESSION[$sendSmsKey] as $smsKey => $smsTime) {
			if ($smsTime + 3600 < time()) {
				unset($_SESSION[$sendSmsKey][$smsKey]);
			}
		}
		
		// 短信验证码
		if (empty($code) || empty($_SESSION[$sendSmsKey][$mobile.$code])) {
			$this->setErr('验证码错误！');
			return false;
		}
				
		return true;
	}
	
	/**
	 * 生成6位短信验证码
	 * @param number $mobile
	 * @param string $type
	 * @return boolean|number
	 */
	protected function generateSmsCode($mobile, $type = 'reg') {
		$sendSmsKey = $type . '-sendSms';
		
		// 1分钟后可重发短信
		if (!empty($_SESSION[$sendSmsKey]) && max($_SESSION[$sendSmsKey]) + 60 > time()) {
			$remain = max($_SESSION[$sendSmsKey]) + 60 - time();
			$this->setErr('需要' . $remain . '秒钟后才能再次发送短信！');
			return false;
		}
		
		isset($_SESSION[$sendSmsKey]) || $_SESSION[$sendSmsKey] = array();
		
		// 去掉过期短信
		foreach ($_SESSION[$sendSmsKey] as $smsKey => $smsTime) {
			if ($smsTime + 3600 < time()) {
				unset($_SESSION[$sendSmsKey][$smsKey]);
			}
		}
		
		if (count($_SESSION[$sendSmsKey]) > 10) {
			$this->setErr('你发送密码太多了，过两个小时后再来吧。');
			return false;
		}
		
		// 保存短信
		$code = mt_rand(100000, 999999);
		$_SESSION[$sendSmsKey][$mobile.$code] = time();
		
		return $code;
	}
	

	/**
	 * 发送手机短信验证码
	 * 内容：验证码 短信文本信息
	 * @param string $mobile
	 * @param string $type 短信发送类型(自己定义，如注册：reg；绑定手机：wxbind)
	 */
	public function sendSmsCode($mobile, $msg, $type = 'reg') {
		if (!$mobile || !Validator::isMobile($mobile)) {
			$this->setErr('手机号码错误！');
			return false;
		}
		
		if (!$code = $this->generateSmsCode($mobile, $type)) {
			return false;
		}
	
		// 发送短信
		$content = "{$code} $msg";
		if(!$this->send($mobile, $content)) {
			$this->setErr($this->getErrs());
			return false;
		}
	
		return true;
	}
}
