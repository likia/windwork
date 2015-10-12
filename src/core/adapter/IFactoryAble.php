<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter;

/**
 * 可支持对象工厂的约束
 *
 * @package     core.adapter
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.factory.html
 * @since       1.0.0
 */
interface IFactoryAble {
	/**
	 * 对象工厂类\core\Factory创建组件实例时统一通过构造函数传参
	 * @param array $cfg
	 */
	public function __construct($cfg);
}

