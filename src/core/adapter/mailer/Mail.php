<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\mailer;


use core\adapter\mailer\IMailer;

/**
 * 使用mail函数发邮件 
 * 
 * @package     core.adapter.mail
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.mailer.html
 * @since       1.0.0
 */
class Mail implements IMailer, \core\adapter\IFactoryAble {
	protected $cfg;

	public function __construct(array $cfg) {
		$this->cfg = $cfg;
	}
	
	public function send($to, $subject, $message, $from = '', $siteName = '', $userName = '') {
		$from || $from = $this->cfg['mail_from'];
		
		// 收件人地址中包含用户名
		$userName = $userName ? $userName : 1;
		
		// 发信者
		$emailFrom = $from == '' ? '=?utf-8?B?'.base64_encode($siteName)."?= <".$from.">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
		
		$emailTo = preg_match('/^(.+?) \<(.+?)\>$/',$to, $mats) ? ($userName ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $to;;
		
		$emailSubject = '=?utf-8?B?'.base64_encode(preg_replace("/[\r|\n]/", '', '['.$siteName.'] '.$subject)).'?=';
		$emailMessage = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "X-Mailer: Windwork-" . \core\Version::VERSION . "\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "From: {$emailFrom}\r\n";
		$headers .= "Content-Transfer-Encoding: base64\r\n";
		
		if(!mail($emailTo, $emailSubject, $emailMessage, $headers)) {
			logging('error', "Mail failed: {$to} {$subject}");
			return false;
		}
		
		return true;
	}
}


