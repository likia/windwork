<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\session;

use core\Config;

/**
 * Session 抽象管理类
 *
 * @package     core.adapter.session
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.session.html
 * @since       1.0.0
 */
abstract class ASession {
	/**
	 * session cookie ID
	 * @var string
	 */
	protected $name;
	
	/**
	 * session保存路径
	 * @var string
	 */
	protected $savePath;
	
	/**
	 * session开始
	 */
	public function start() {
		$sessionId = '';
		if (!empty($_GET[session_name()])) {
			// 解决Flash不传Cookie
			$sessionId = $_GET[session_name()];
		} else if (!empty($_COOKIE[session_name()])) {
			$sessionId = $_COOKIE[session_name()];
		}
		
		if ($sessionId) {
		    $sessionId = preg_replace("/[^0-9a-z_\\-\\,]/i", '', $sessionId);
		    $sessionId = substr(trim($sessionId), 0, 32);
		}
		
		$sessionId && session_id($sessionId);
		
		if(!isset($_SESSION)) {
			session_start();  // session.auto_start 不启用的时候
		}
		/*
		// session 防伪造，
		if(empty($_SESSION['sevalid'])) {
			$this->initValidCode();
		} elseif(!$this->isValid()) {			
			$_SESSION = array();
			$this->destroy(session_id());
			$this->start();
			return ;
		}
		//*/	
	}
	
	/**
	 * 仿伪造session效验码初始化
	 */
	public function initValidCode() {
		$code = base_convert(mt_rand(0x100000, 0xFFFFFF), 10, 16);
		$_SESSION['sevalid'] = $code;
		$_COOKIE['sevalid'] = $code;
		
		setcookie('sevalid', $code, time()+3600*24*30, Config::get('session_cookie_path'));
	}
	
	/**
	 * session是否有效，检查是否是伪造session
	 * 
	 * @param string $code
	 * @return bool 有效则返回true
	 */
	public function isValid() {
		return ((!empty($_COOKIE['sevalid']) && $_SESSION['sevalid'] == $_COOKIE['sevalid'])) || 
		  ((!empty($_REQUEST['sevalid']) && $_SESSION['sevalid'] == $_REQUEST['sevalid']));
	}
}
