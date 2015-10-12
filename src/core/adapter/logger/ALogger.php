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
 * 日志读写
 * 
 * @package     core.adapter.logger
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.logger.html
 * @since       1.0.0
 */ 
abstract class ALogger {
	protected $logDir;
	protected $enabled;
	
	public function __construct($cfg) {
		$this->enabled = $cfg['log_enabled'];
		$this->setLogDir($cfg['log_dir']);
	}
		
	/**
	 * 设置日志目录，支持wrapper
	 * 
	 * @param string $dir
	 */
	public function setLogDir($dir) {
		$dir = str_replace("\\", "/", $dir);
		$dir = rtrim($dir, '/');
		
		$this->logDir = $dir;
	}
	
	/**
	 * 检查日志级别
	 * @param string $level info|debug|exception|error
	 * @throws \core\adapter\logger\Exception
	 */
	public function checkLevel($level) {
		if(!in_array($level, array('info', 'debug', 'exception', 'error'))) {
			throw new \core\adapter\logger\Exception('日志级别应该为：info|debug|exception|error');
		}
	}
}

