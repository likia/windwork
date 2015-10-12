<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core;

/**
 * 钩子接口
 * 
 * 所有钩子必须实现该接口，钩子管理器通过调用接口的execute方法执行钩子业务逻辑
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.hook.html
 * @since       1.0.0
 */
interface IHook {
	/**
	 * 执行Hook
	 * @param array $params = array()
	 */
	public function execute($params = array());
}

