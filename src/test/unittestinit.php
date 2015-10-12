<?php
require_once 'src/core/app.php';
require_once 'PHPUnit\Framework\TestCase.php';

use core\Config;

!defined('IS_IN') && define('IS_IN', 1);
!defined('IS_UNIT_TEST') && define('IS_UNIT_TEST', 1);


chdir(SRC_PATH);

$app = \core\App::getInstance(include SRC_PATH . 'config/config.php');

// 测试不启用缓存
Config::set('cache_enabled', 0);

// 系统调试模式设置

error_reporting(E_ALL);
ini_set('display_errors',  1);
ini_set('log_errors',      1);


function unset_debugger_var($var) {
	unset(
	    $var['start_debug'],
	    $var['debug_fastfile'],
		$var['debug_coverage'],
		$var['use_remote'],
		$var['send_sess_end'],
		$var['debug_session_id'],
		$var['debug_start_session'],
		$var['debug_port'],
		$var['debug_host']
	);
	
	return $var;
}
