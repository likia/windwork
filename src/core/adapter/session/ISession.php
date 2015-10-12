<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\session;

/**
 * Session 接口
 *
 * @package     core.adapter.session
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.session.html
 * @since       1.0.0
 */
interface ISession {	
	/**
	 * 打开session
	 * @param string $savePath
	 * @param string $name
	 */
	public function open($savePath, $name);
	
	/**
	 * 关闭session
	 */
	public function close();
	
	/**
	 * 读取
	 * @param string $id
	 */
	public function read($id);
	
	/**
	 * 写入
	 * @param string $id
	 * @param array $sessData
	 */
	public function write($id, $sessData);
	
	/**
	 * 销毁，用于注销时
	 * @param string $id
	 */
	public function destroy($id);
	
	/**
	 * 垃圾回收的周期
	 * @param int $maxlifetime 秒
	 */
	public function gc($maxlifetime);

}
