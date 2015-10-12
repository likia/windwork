<?php
/**
 * 入口
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
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