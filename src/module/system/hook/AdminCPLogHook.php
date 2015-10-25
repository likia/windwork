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

use core\App;
use core\Factory;

/**
 * 后台管理日志
 * 
 * @package     module.system.hook
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class AdminCPLogHook implements \core\IHook {
	/**
	 * 保存请求变量
	 */
	public function execute($params = array()) {
		$ctlObj = App::getInstance()->getCtlObj();
		
		if(!$ctlObj instanceof \module\system\controller\admin\AdminBase) {
			return;
		}
		
		$log = array('uri' => REQUEST_URI, 'p' => $_POST, 'c' => $_COOKIE);
		
		$log = \core\Common::userBrowser();
		$log .= ' '.\core\App::getInstance()->getRequest()->getClientIp();
		foreach ($log as $item) {
			$log .= " {$item['uri']}";
			$log .= " p:" . preg_replace("/\\n|\\s+/", '', var_export($item['p'], 1));
			$log .= " c:" . preg_replace("/\\n|\\s+/", '', var_export($item['c'], 1));
		}
		
		logging('info', $log);
	}
}
