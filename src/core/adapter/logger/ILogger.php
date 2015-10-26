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
 * 遵循PSR-3-logger-interface规范
 * 
 * @package     core.adapter.logger
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.logger.html
 * @link        https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * @since       1.0.0
 */ 
interface ILogger {
	
    /**
     * 系统不可用
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array());

    /**
     * 功能必须马上修复
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array());

    /**
     * 危险的环境.
     *
     * Example: 应用组件不可用, 不可预知的异常.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array());

    /**
     * 不需要立即处理的运行时错误，但通常应该被记录和监测。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array());

    /**
     * 运行时警告 (非致命错误)。仅给出提示信息，但是脚本不会终止运行。
     *
     * Example: 使用不赞成的接口, 不好的东西但不一定是错误
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array());

    /**
     * 运行时通知。表示脚本遇到可能会表现为错误的情况，但是在可以正常运行的脚本里面也可能会有类似的通知。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array());

    /**
     * 有意义的事件
     *
     * Example: 用户登录，sql日志等
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array());

    /**
     * 详细的调试信息
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array());

    /**
	 * 写入日志
	 * 
	 * 可以在config/config.php中启用日志，所有日志按类别保存
	 *
	 * @param string $level 日志级别  emergency|alert|critical|error|warning|notice|info|debug
	 * @param string $message  日志内容
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array());
}

