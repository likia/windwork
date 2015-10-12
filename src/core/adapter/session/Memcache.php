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
 * Memcache存贮Session
 *
 * @package     core.adapter.session
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.session.html
 * @since       1.0.0
 */
final class Memcache extends ASession implements ISession, \core\adapter\IFactoryAble {
	private $cfg = array();
	
	public function __construct($cfg) {
		$this->cfg = $cfg;
		if (ini_get('session.auto_start')) {
			session_destroy();
		}
		
		@ini_set("session.save_handler", "memcache");
		@ini_set("session.save_path",    "tcp://{$this->cfg['mm_memcache_host']}:{$this->cfg['mm_memcache_port']}");
	}
	
	public function open($savePath, $name){}
	public function read($sid){}
	public function write($sid, $sessData){}
	public function close(){}
	public function gc($maxLifeTime){}
	public function destroy($sid){}
}