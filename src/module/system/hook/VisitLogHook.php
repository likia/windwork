<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\hook;

use core\util\UserAgent;

/**
 * 访问日志（不建议使用，web服务器已经记录访问日志。）
 * 
 * @package     module.system.hook
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ExecLogHook implements \core\IHook {
	/**
	 * 保存请求变量
	 */
	public function execute($params = array()) {
		$ctlObj = \core\App::getInstance()->getCtlObj();
		$request = \core\App::getInstance()->getRequest();
		$log = array('uri' => $request->getRequestUri(), 'p' => $_POST, 'c' => $_COOKIE);
		
		$log = UserAgent::getUserBrowser();
		$log .= ' '. $request->getClientIp();
		
		foreach ($log as $item) {
			$log .= " {$item['uri']}";
			$log .= " p:" . preg_replace("/\\n|\\s+/", '', var_export($item['p'], 1));
			$log .= " c:" . preg_replace("/\\n|\\s+/", '', var_export($item['c'], 1));
		}
		
		logging('info', $log);
	}
}
