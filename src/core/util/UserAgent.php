<?php

/**
 * Windwork
 *
 * 一个开源的PHP轻量级高效Web开发框架
 *
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\util;

/**
 * 访问用户客户端信息
 *
 * @package     core.util
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class UserAgent {
	
	/**
	 * 客户端是否是搜索引擎的爬虫 ，如果是已知爬虫，则返回爬虫类型
	 *
	 * @return bool|string
	 */
	public static function checkRobot() {
		static $robot = null;
		if(null !== $robot) {
			return $robot;
		}
		
		$spiders  = 'bot|crawl|spider|slurp|sohu-search|lycos|robozilla';
		$browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';		
		$agent    = @$_SERVER['HTTP_USER_AGENT'];
	
		if(strpos($agent, 'http://') === false && preg_match("/($browsers)/i", $agent)) {
			$robot = false;
		} elseif(preg_match("/($spiders)/i", $agent)) {
			$robot = 'UnknowSpider';
			
			$botList = array(
				'GoogleBot',
				'mediapartners-google',
				'BaiduSpider',
				'360Spider',
				'msnbot',
				'bingbot',
				'yodaobot',
				'yahoo! slurp',
				'yahoo! slurp china',
				'iaskspider',
				'Sogou web spider',
				'Sogou push spider',
				'YisouSpider'
			);
			
			foreach ($botList as $bot) {
				if(false !== stripos($agent, $bot)) {
					$robot = $bot;
					break;
				}
			}
		} else {
			$robot = false;
		}
		
		return $robot;
	}
	
	/**
	 * 获得浏览器名称和版本
	 *
	 * @return string
	 */
	public static function getUserBrowser() {
		static $userBrowser = '';
	    if($userBrowser) {
			return $userBrowser;
		}
		
		$agent    = @$_SERVER['HTTP_USER_AGENT'];
		$browser  = '';
		$version  = '';
	
		if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
			$browser = 'Internet Explorer';
			$version = $regs[1];
		} elseif (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'Chrome';
			$version = $regs[1];
		} elseif (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'FireFox';
			$version = $regs[1];
		} elseif (preg_match('/Maxthon/i', $agent, $regs)) {
			$browser = '(Internet Explorer ' .$version. ') Maxthon';
			$version = '';
		} elseif (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
			$browser = 'Opera';
			$version = $regs[1];
		} elseif (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
			$browser = 'OmniWeb';
			$version = $regs[2];
		} elseif (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'Netscape';
			$version = $regs[2];
		} elseif (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'Safari';
			$version = $regs[1];
		} elseif (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
			$browser = '(Internet Explorer ' .$version. ') NetCaptor';
			$version = $regs[1];
		} elseif (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'Lynx';
			$version = $regs[1];
		}
	
		if (!empty($browser)) {
		   return $userBrowser = addslashes($browser . ' ' . $version);
		} else {
			return $userBrowser = 'Unknow Browser';
		}
	}
	
	
	/**
	 * 获得客户端的操作系统
	 *
	 * @return string
	 */
	public static function getUserOS() {
		static $os = '';
		if ($os) {
		    return $os;
		}
		
		if (empty($_SERVER['HTTP_USER_AGENT'])) {
			return 'Unknown';
		}
	
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$os    = '';
		
		if (strpos($agent, 'win') !== false) {
			$os = 'Windows';
		} elseif (strpos($agent, 'android') !== false) {
			$os = 'Android';
		} elseif(preg_match("/(iPhone|iPad)/i", $agent)) {
			$os = 'iOS'; 
		} elseif (strpos($agent, 'blackberry') !== false) {
			$os = 'BlackBerry';
		} elseif (strpos($agent, 'hpwos') !== false) {
			$os = 'WebOS';
		} elseif (strpos($agent, 'symbian') !== false) {
			$os = 'Symbian';
		} elseif (strpos($agent, 'linux') !== false) {
			$os = 'Linux';
		} elseif (strpos($agent, 'unix') !== false) {
			$os = 'Unix';
		} elseif (strpos($agent, 'sunos') !== false) {
			$os = 'SunOS';
		} elseif (strpos($agent, 'os/2') !== false) {
			$os = 'IBM OS/2';
		} elseif (strpos($agent, 'mac') !== false) {
			$os = 'Mac';
		} elseif (strpos($agent, 'powerpc') !== false) {
			$os = 'PowerPC';
		} elseif (strpos($agent, 'aix') !== false) {
			$os = 'AIX';
		} elseif (strpos($agent, 'hpux') !== false) {
			$os = 'HPUX';
		} elseif (strpos($agent, 'netbsd') !== false) {
			$os = 'NetBSD';
		} elseif (strpos($agent, 'bsd') !== false) {
			$os = 'BSD';
		} elseif (strpos($agent, 'osf1') !== false) {
			$os = 'OSF1';
		} elseif (strpos($agent, 'irix') !== false) {
			$os = 'IRIX';
		} elseif (strpos($agent, 'freebsd') !== false) {
			$os = 'FreeBSD';
		} elseif (strpos($agent, 'teleport') !== false) {
			$os = 'teleport';
		} elseif (strpos($agent, 'flashget') !== false) {
			$os = 'flashget';
		} elseif (strpos($agent, 'webzip') !== false) {
			$os = 'webzip';
		} elseif (strpos($agent, 'offline') !== false) {
			$os = 'offline';
		} elseif(preg_match("/(Bot|Crawl|Spider)/i", $agent)) {
			$os = 'Spiders'; // 爬虫
		} else {
			$os = 'Other';
		}
	
		return $os;
	}
		
	/**
	 * 客户端手机型号
	 * 
	 * @return string|bool
	 */
	public static function checkMobile() {
		static $mobile = null;
		if($mobile !== null) {
			return $mobile;
		}

		$mobile = false;		
		if(empty($_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}
		
		$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
			
		if((strpos($userAgent, 'pad') && false === strpos($userAgent, 'coolpad')) || strpos($userAgent, 'gt-p1000')) {		
			$mobile = false;
		} else {
			$mobileBrowserList = array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
				'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung', 'palmsource',
				'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
				'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
				'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
				'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
				'benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'coolpad', 'MicroMessenger');
			
			foreach($mobileBrowserList as $browser) {
				if (false !== stripos($userAgent, $browser, true)){
					$mobile = $browser;
					break;
				}
			}			
		}
		
		return $mobile;	
	}
	
	/**
	 * 获得用户操作系统的换行符
	 * @return string
	 */
	public static function getUserCrlf() {
		static $crlf = null;
		if ($crlf !== null) {
			return $crlf;
		}
		
		if(empty($_SERVER['HTTP_USER_AGENT'])) {
			return $crlf = "\n";
		}
		
		if (stristr($_SERVER['HTTP_USER_AGENT'], 'Win')) {
		    $crlf = "\r\n";
		} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
		    $crlf = "\r"; // for old MAC OS
		} else {
		    $crlf = "\n";
		}
		
		return $crlf;
	}
}

