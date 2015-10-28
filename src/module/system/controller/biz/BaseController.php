<?php
/**
 * Windwork web framework
 *
 * @link        http://www.windwork.org
 * @copyright   Copyright (c) 2008-2015 Windwork Team.
 * @license     NewBSD
 */
namespace module\system\controller\biz;


/**
 * 管理员后台控制器基类
 * 
 * @package     core.mvc
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     NewBSD
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
abstract class BaseController extends \core\mvc\Controller {
	/**
	 * 构造函数继承父类，初始化视图，加载后台语言包
	 */
	public function __construct() {
		parent::__construct();
		$this->initView();
		
	}
}
