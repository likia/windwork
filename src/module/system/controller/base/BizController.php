<?php
/**
 * Windwork web framework
 *
 * @link        http://www.windwork.org
 * @copyright   Copyright (c) 2008-2015 Windwork Team.
 * @license     NewBSD
 */
namespace module\system\controller\base;


use core\mvc\Message;
/**
 * 面向公众号管理人员/使用平台的商家的管理后台控制器基类
 * 
 * @package     module.system.controller.base
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     NewBSD
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
abstract class BizController extends \core\mvc\Controller {
	/**
	 * 
	 * @var \module\wx\model\SettingModel
	 */
	protected $wx;
	
	/**
	 * 构造函数继承父类，初始化视图，加载后台语言包
	 */
	public function __construct() {
		parent::__construct();
		$this->initView();
		
		$this->view->buid = $_SESSION['uid'];
		$this->wx = new \module\wx\model\SettingModel();
		$this->wx->setPkv($_SESSION['uid'])->load();
		
		if(empty($_SESSION['isextvalid'])) {
			Message::setWarn('亲，您的账号尚未通过审核，请联系管理员审核！');
			$this->app->dispatch('system.misc.message');
		}
		
	}
}
