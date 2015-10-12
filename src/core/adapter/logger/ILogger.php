<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\logger;

/**
 * 日志读写接口
 * 
 * @package     core.adapter.logger
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.logger.html
 * @since       1.0.0
 */ 
interface ILogger {		
	/**
	 * 写入日志
	 * 
	 * 可以在config/config.php中启用日志，所有日志按类别保存
	 *
	 * @param string $level 日志类别 info|debug|exception|error
	 * @param string $message  日志内容
	 * @throws \core\adapter\log\Exception
	 */
	public function write($level, $message);
}

