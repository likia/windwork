<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\model;

use core\Common;
use core\Config;
use core\util\UserAgent;

/**
 * 访问统计
 *
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class StatsModel extends \core\mvc\Model {
	protected $table = 'wk_stats';
	
	/**
	 * 当前请求是否只统计一次
	 * @var bool
	 */
	public static $statsOnlyOneTime = true;
	
	protected static $statsed = false;
	
	/**
	 * 访问统计
	 * @param int $getCount 是否返回页面访问次数
	 * @return Ambigous <\core\mvc\number, number>
	 */
	public static function stats($getCount = false) {
		$obj = new self();
		$obj->create();
		
		if ($getCount) {
			return $obj->countByUri();
		}		
	}
	
	public function create() {
		// 重复统计检查
		if (static::$statsed && static::$statsOnlyOneTime) {
			return;
		}
		
		$request = \core\App::getInstance()->getRequest();
		$robot = \core\util\UserAgent::checkRobot();
		
		$this->uri = $request->getRequestUri();

		list($ip1, $ip2, $ip3, $ip4) = explode('.', $request->getClientIp());
		
		$this->ip1 = $ip1;
		$this->ip2 = $ip2;
		$this->ip3 = $ip3;
		$this->ip4 = $ip4;

		$this->uid = $_SESSION['uid'];
		$this->dateline = time();
		$this->isrobot  = $robot ? 1 : 0;
		$this->agent    = $robot ? $robot : UserAgent::getUserBrowser();
		$this->os       = UserAgent::getUserOS();
		$this->referer  = @$_SERVER['HTTP_REFERER'];
		
		if($this->agent == 'Unknow Browser' || $robot == 'UnknowSpider') {
			$this->agentLog = @$_SERVER['HTTP_USER_AGENT'];
		}
		
		return parent::create();
	}
	
	/**
	 * 是否是刷新页面访问
	 * @return boolean
	 */
	public function isRefresh() {
		if (!Config::get('stats_allowrefresh')) {
			return false;
		}

		list($ip1, $ip2, $ip3, $ip4) = explode('.', \core\App::getInstance()->getRequest()->getClientIp());
				
		$whArr = array();
		$whArr[] = array('uri', $this->uri);
		$whArr[] = array('ip1',  $ip1);
		$whArr[] = array('ip2',  $ip2);
		$whArr[] = array('ip3',  $ip3);
		$whArr[] = array('ip4',  $ip4);
		$whArr[] = array('dateline', time() - Config::get('stats_rerecordtime'), '>');
		
		$isRefresh = $this->count(array('where' => $whArr));
		
		return (bool)$isRefresh;
	}
	
	/**
	 * 获取URI访问次数
	 * @return \core\mvc\number
	 */
	public function countByUri() {
		return $this->count(array('where' => array('uri', $this->uri)));
	}
}
