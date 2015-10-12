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

/**
 * 发送邮件接口
 *
 * @package     core.adapter.mail
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.mailer.html
 * @since       1.0.0
 */
interface IMailer {
	
	/**
	 * 发送邮件
	 * 
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param string $from
	 * @param string $siteName
	 * @param string $userName 收件人地址中包含用户名
	 * @return bool
	 */
	public function send($to, $subject, $message, $from = '', $siteName = '', $userName = '');
}

