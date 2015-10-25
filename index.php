<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

/**
 * 入口
 * Windwork框架为单入口模式，所有mvc业务流程均通过index.php进入。
 * （不使用框架的mvc架构时可从其他入口进入）
 */

/**
 * 入口常量，声明该常量后才是有效的程序入口
 * @var bool
 */
define('IS_IN', true);

require_once 'src/core/App.php';

//try {
	$configs = include SRC_PATH.'config/config.php';
	/*
	if(defined('SAE_SECRETKEY') && defined('SAE_ACCESSKEY')) {
		// 新浪云平台配置文件路径
		$configs = include SRC_PATH.'config/config.sae.php';
	}
	*/
    $app = \core\App::getInstance($configs);
	$app->dispatch();
// } catch (\core\Exception $e) {
// 	\core\Common::exceptionHandler($e);
// }
//print_r(get_included_files());
//print_r(benchmark());