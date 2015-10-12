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
 * 使用PHP默认Session存贮
 *
 * @package     core.adapter.session
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.session.html
 * @since       1.0.0
 */
final class Standard extends ASession implements ISession, \core\adapter\IFactoryAble {
	/**
	 * (non-PHPdoc)
	 * @see \core\adapter\session\ASession::open()
	 */
	public function open($savePath, $name){}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\adapter\session\ASession::read()
	 */
	public function read($sid){}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\adapter\session\ASession::write()
	 */
	public function write($sid, $sessData){}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\adapter\session\ASession::close()
	 */
	public function close(){}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\adapter\session\ASession::destroy()
	 */
	public function destroy($sid){}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\adapter\session\ASession::gc()
	 */
	public function gc($maxLifeTime){}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\adapter\IFactoryAble::__construct()
	 */
	public function __construct($cfg) {
	}

}
