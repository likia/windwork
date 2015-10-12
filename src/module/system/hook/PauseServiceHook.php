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

/**
 * 网站暂停服务过滤器
 * 如果后台设置暂停后，非管理员只允许进入登录页面
 * 
 * @package     module.system.hook
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class PauseServiceHook implements \core\IHook {
	
	public function execute($params = array()) {
		$app = App::getInstance();
		
		if ($app->getRequest()->getRequest('act') != 'login' 
		  && $app->getRequest()->getRequest('ctl') != 'captcha'
		  && empty($_SESSION['isadmin']) 
		  && \core\Config::get('system.service.pause')
	    ) {
			\core\mvc\Message::setWarn('网站维护中');
			$app->dispatch('system.misc.message');
		}		
	}
}
