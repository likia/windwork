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
 * 日志读写，使用文件存贮
 * 
 * @package     core.adapter.logger
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.logger.html
 * @since       1.0.0
 */ 
class File extends ALogger implements ILogger, \core\adapter\IFactoryAble {
	/**
	 * 每个日志文件的大小
	 * @var int
	 */
	const LOG_SIZE = 2056000;
		
	/**
	 * 写日志
	 * 
	 * <pre>
	 * 如果日志文件大于当前设置($logSize)日志大小，则把日志文件重命名存档，再创建新的日志文件
	 * </pre>
	 *
	 * @param string $level 日志级别
	 * @param string $message  日志内容
	 * @throws \core\log\Exception
	 */
	public function write($level, $message) {
		if (!$this->enabled) {
			return;
		}
		
		$this->checkLevel($level);
		
		$time = time();
		$yearMonth = date('Y-m', $time);
		$logFile = $this->logDir . "/{$level}.log.php";
	
		// 日志文件超过限定大小将分文件保存
		if(@is_file($logFile) && filesize($logFile) > self::LOG_SIZE) {
			$archiveTime = date('YmdHis', $time);
			$logFileArchive = $this->logDir . "/{$level}-{$archiveTime}.log.php";
			// 文件是否正在保存，如果正在保存，其他请求就不再保存
			if(!is_file($logFileArchive)) {
				@rename($logFile, $logFileArchive);
			}
		}
	
		// 新添加的文件前添加浏览保护信息
		$pre = "<?php exit?>";
		$pre .= date('Y-m-d H:i:s');
		$message = trim($message);
		
		if(!file_put_contents($logFile, "{$pre} {$level} {$message}\n", FILE_APPEND)) {
			throw new Exception($logFile. ' can\'t write.');
		}
	}
	
}

