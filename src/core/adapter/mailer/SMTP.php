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

use core\Factory;

/**
 * 使用SMTP发邮件
 *
 * @package     core.adapter.mail
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.mailer.html
 * @since       1.0.0
 */
class SMTP implements IMailer, \core\adapter\IFactoryAble {
	protected $cfg = array();
	
	public function __construct(array $cfg) {
		$this->cfg = $cfg;
	}

	public function send($to, $subject, $message, $from = '', $siteName = '', $userName = '') {
		$logger = Factory::logger();
		$from || $from = $this->cfg['mail_from'];
				
		// 收件人地址中包含用户名
		$userName = $userName ? $userName : 1;
		
		// 端口
		$this->cfg['mail_port'] = empty($this->cfg['mail_port']) ? 25 : $this->cfg['mail_port'];
	
		// 发信者
		$emailFrom = $from == '' ? '=?utf-8?B?'.base64_encode($siteName)."?= <".$from.">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
	
		$emailTo = preg_match('/^(.+?) \<(.+?)\>$/',$to, $mats) ? ($userName ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $to;;
	
		$emailSubject = '=?utf-8?B?'.base64_encode(preg_replace("/[\r|\n]/", '', '['.$siteName.'] '.$subject)).'?=';
		$emailMessage = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
	
		$headers = "From: {$emailFrom}\r\n"
				 . "X-Priority: 3\r\n"
				 . "X-Mailer: Windwork-" . \core\Version::VERSION . "\r\n"
				 . "MIME-Version: 1.0\r\n"
				 . "Content-type: text/html; charset=utf-8\r\n"
				 . "Content-Transfer-Encoding: base64\r\n";
	
		if(!$fp = fsockopen($this->cfg['mail_host'], $this->cfg['mail_port'], $errno, $errstr, 30)) {
			$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) CONNECT - Unable to connect to the SMTP server");
			return false;
		}
		stream_set_blocking($fp, true);
	
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != '220') {
			$logger->write('SMTP', "{$this->cfg['mail_host']}:{$this->cfg['mail_port']} CONNECT - $lastMessage");
			return false;
		}
	
		fputs($fp, ($this->cfg['mail_auth'] ? 'EHLO' : 'HELO')." windwork\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 220 && substr($lastMessage, 0, 3) != 250) {
			$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) HELO/EHLO - $lastMessage", 0);
			return false;
		}
	
		while(1) {
			if(substr($lastMessage, 3, 1) != '-' || empty($lastMessage)) {
				break;
			}
			$lastMessage = fgets($fp, 512);
		}
	
		if($this->cfg['mail_auth']) {
			fputs($fp, "AUTH LOGIN\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 334) {
				$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) AUTH LOGIN - $lastMessage", 0);
				return false;
			}
	
			fputs($fp, base64_encode($this->cfg['mail_user'])."\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 334) {
				$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) USERNAME - $lastMessage", 0);
				return false;
			}
	
			fputs($fp, base64_encode($this->cfg['mail_pass'])."\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 235) {
				$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) PASSWORD - $lastMessage", 0);
				return false;
			}
	
			$emailFrom = $from;
		}
	
		fputs($fp, "MAIL FROM: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $emailFrom).">\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 250) {
			fputs($fp, "MAIL FROM: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $emailFrom).">\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 250) {
				$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) MAIL FROM - $lastMessage", 0);
				return false;
			}
		}
	
		fputs($fp, "RCPT TO: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $to).">\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 250) {
			fputs($fp, "RCPT TO: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $to).">\r\n");
			$lastMessage = fgets($fp, 512);
			$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) RCPT TO - $lastMessage", 0);
			return false;
		}
	
		fputs($fp, "DATA\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 354) {
			$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) DATA - $lastMessage", 0);
			return false;
		}
	
		$headers .= 'Message-ID: <'.date('YmdHs').'.'.substr(md5($emailMessage.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">\r\n";
	
		fputs($fp, "Date: ".date('r')."\r\n");
		fputs($fp, "To: {$emailTo}\r\n");
		fputs($fp, "Subject: {$emailSubject}\r\n");
		fputs($fp, "{$headers}\r\n");
		fputs($fp, "\r\n\r\n");
		fputs($fp, "{$emailMessage}\r\n.\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 250) {
			$logger->write('SMTP', "({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) END - {$lastMessage}", 0);
		}
		fputs($fp, "QUIT\r\n");
		
		return true;
	}
}

