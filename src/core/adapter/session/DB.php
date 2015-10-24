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
 * @TODO 未完成,待修改 
 */

/**
 * 使用数据库存贮session
 *
 * @package     core.adapter.session
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.session.html
 * @since       1.0.0
 */
final class DB extends ASession implements ISession, \core\adapter\IFactoryAble {
	/**
	 * 
	 * @var \core\adapter\db\IDB
	 */
	protected $db = null;

	public function __construct(array $cfg) {
		$this->db = \core\Factory::db();
		
		if (ini_get('session.auto_start')) {
			session_destroy();
		    unset($_SESSION);
		}
		
		session_set_save_handler(
			array(&$this, 'open'),
			array(&$this, 'close'),
			array(&$this, 'read'),
			array(&$this, 'write'),
			array(&$this, 'destroy'),
			array(&$this, 'gc')
		);
		
	}

	/**
	 * 运行到 session_start()后程序就进入这个函数
	 *
	 * @param string $savePath
	 * @param string $name
	 * @return bool
	 */
	public function open($savePath, $name){
		$this->savePath = $savePath;
		$this->name = $name;
		return true;
	}

	/**
	 * 程序运行完UE_Session_DB::open()后接着进入这里
	 *
	 * @param string $id
	 * @return bool
	 */
	public function read($sid){
		$sess = $this->db->getRow("SELECT session_data, session_user_ip FROM wk_session WHERE session_id = %s", array($sid));
		if(!$sess) {
			return '';
		}

		$sessData = $sess['session_data'];

		if (strlen($sessData) == 255) {
			$field = $this->db->getOne("SELECT session_data FROM wk_session_data WHERE session_id = %s", array($sid));
			if ($field) {
				$sessData = $field;
			}
		}
		
		return $sessData;
	}

	/**
	 * 当所有析构函数运行完以后程序进入这里
	 *
	 * @param string $id
	 * @param string $sessData
	 * @return bool
	 */
	public function write($sid, $sessData){
		$sid  = addslashes($sid);
		$time = time();
		if (strlen($sessData) > 255) {
			$sql = "REPLACE INTO %t SET session_id = %s, lastactivity = %f, sessdata = %s;";
			$this->db->query($sql, array('ue_session_data', $sid, $time, $sessData));
			$sessData = substr($sessData, 255);
		}
		
		$this->db->query("
			REPLACE INTO %t (
				sid, mod, ctl, act, lastactivity, sessdata, ip,
				uid, username, password, gid, invisible
	 		) VALUE (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);",
		    array(
		    	'wk_session', 
		    	$sid, 
		    	$_GET['mod'], 
		    	$_GET['ctl'], 
		    	$_GET['act'], 
		    	$time,
		    	$sessData,
		    	$_SESSION['ip'],
		    	$_SESSION['uid'],
		        $_SESSION['uname'],
		    	$_SESSION['password'],
		    	$_SESSION['gid'],
		    	$_SESSION['invisible']
		    ));

		return true;
	}

	/**
	 *
	 * @return bool
	 */
	public function close(){
		return true;
	}

	public function destroy($sid){
		$this->db->exec("DELETE FROM `wk_session` WHERE `sid` = %s", array($sid));
		$this->db->exec("DELETE FROM `wk_session_data` WHERE `sid` = %s", array($sid));
		
		return true;
	}

	public function gc($maxLifeTime){
		$expire = time() - $maxLifeTime;
		$this->db->exec("DELETE FROM wk_session WHERE lastactivity < %i", array($expire));
		$this->db->exec("DELETE FROM wk_session_data WHERE lastactivity < %i", array($expire));
		return true;
	}
}