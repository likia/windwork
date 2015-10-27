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

use module\system\model\BannedIPModel;

/**
 * 禁止IP过滤器
 * 
 * @package     module.system.hook
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class BannedIPHook implements \core\IHook {
	
	public function execute($params = array()) {
		$bannedIpObj = new BannedIPModel();
		$mca = \core\App::getInstance()->getRequest()->getMCA();
		$mca = strtolower($mca);
		if ($mca != 'system.default.error' && $bannedIpObj->isBanned(\core\App::getInstance()->getRequest()->getClientIp())) {
			throw new \core\Exception('你的IP被拒绝访问！', \core\Exception::ERROR_HTTP_403);
		}
	}
}
