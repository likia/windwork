<?php

require_once 'src\core\app.php';
require_once 'src\core\mailer\imailer.php';
require_once 'src\core\mailer\smtp.php';

require_once 'PHPUnit\Framework\TestCase.php';

use core\App;
use core\mailer\SMTP;

/**
 * SMTP test case.
 */
class SMTPTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var SMTP
	 */
	private $SMTP;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		chdir(SRC_PATH);
		App::getInstance();
		
		$cfg = array(
			'mail_auth' => 1,
			'mail_host' => 'smtp.126.com',
			'mail_from' => 'panchunmeng@126.com',
			'mail_name' => '朔游记.',
			'mail_port' => 25,
			'mail_user' => 'panchunmeng@126.com',
			'mail_pass' => 'chunmeng7788',
		);
		
		$this->SMTP = new SMTP($cfg);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated SMTPTest::tearDown()
		$this->SMTP = null;
		
		parent::tearDown ();
	}
	
	
	/**
	 * Tests SMTP->send()
	 */
	public function testSend() {
		$to = '121169238@qq.com';
		$subject = '测试发送邮件.';
		$message = 'Windwork测试发送邮件.这是一封测试邮件，不必回复。';
		$from = 'panchunmeng@126.com';
		$siteName = 'windwork框架.';
		$userName = '小潘潘.';
		$send = $this->SMTP->send($to, $subject, $message, $from, $siteName, $userName);
		$this->assertTrue($send);
	}
}

