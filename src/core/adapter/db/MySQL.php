<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\adapter\db;

/**
 * 使用 MYSQL扩展连接数据库进行操作
 * 如果是自己写sql语句的时候，请不要忘了防注入，只是在您不输入sql的情况下帮您过滤MySQL注入了
 *
 * @package     core.adapter.db
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.db.html
 * @since       1.0.0
 */
class MySQL extends ADB implements IDB, \core\adapter\IFactoryAble {
	/**
	 * MySQL连接资源
	 * @var resource
	 */
	protected $link = null;
	
	/**
	 * 数据库连接
	 *
	 * @param array $cfg
	 * @throws \core\adapter\db\Exception
	 */
	public function __construct(array $cfg) {
		if (!function_exists('mysql_connect')) {
			throw new Exception('extension not installed. 连接数据库时出错：PHP的MYSQL模块(php_mysql.dll/php_mysql.so)未启用。');
		}

		parent::__construct($cfg);
		
		$this->link = @mysql_connect("{$cfg['db_host']}:{$cfg['db_port']}", $cfg['db_user'], $cfg['db_pass']);
		if(!$this->link) {
			throw new Exception('connect error. 无法连接到数据库，请确保数据库已经启动并且数据库配置正确。');
		}
					
		if(!mysql_select_db($cfg['db_name'], $this->link)){
			throw new Exception('db not exists. 无法使用数据库'.$cfg['db_name']);
		}
		
		mysql_query("SET NAMES utf8, sql_mode='', AUTOCOMMIT=1;", $this->link);
	}
	
	/**
	 * 关闭数据库连接
	 */
	public function __destruct() {
		parent::__destruct();
		mysql_close($this->link);
	}
	
	/**
	 * 开始事务，启用的时候事务才有效
	 * 
	 * <code>
	 * useage:
	 *   try{
	 *       Factory::db()->beginTransaction();
	 *       $q1 = Factory::db()->query($sql);
	 *       $q2 = Factory::db()->query($sql);
	 *       $q3 = Factory::db()->query($sql);
	 *       Factory::db()->commit();
	 *   } catch(\core\Exception $e) {
	 *       Factory::db()->rollBack();
	 *   }
	 * </code>
	 * @return \core\adapter\db\IDB
	 */
	public function beginTransaction() {		
		if (!$this->transactions) {
		    $this->exec("SET AUTOCOMMIT=0");
		    $this->exec("START TRANSACTION");
		}
		
		++$this->transactions;
		
		return $this;
	}
	
	/**
	 * 回滚
	 *
	 * @return \core\adapter\db\IDB
	 */
	public function rollBack(){
		--$this->transactions;
			
		if ($this->transactions <= 0) {
			$this->exec("ROLLBACK");
			$this->exec("SET AUTOCOMMIT=1");
		} else {			
			throw new \core\adapter\db\Exception($this->getLastErr());
		}
	}
	
	/**
	 * 提交事务
	 */
	public function commit() {
		--$this->transactions;
		
		if($this->transactions == 0) {		
			if(!$this->exec("COMMIT")) {
				throw new \core\adapter\db\Exception($this->getLastErr());
			}
			
			$this->exec("SET AUTOCOMMIT=1");
		}
	}
	
	/**
	 * 设置是否自动提交事务，启用事务的时候有效
	 *
	 * @param bool $isAutoCommit = false
	 * @return \core\adapter\db\IDB
	 */
	public function setAutoCommit($isAutoCommit = false) {
		$this->exec("SET AUTOCOMMIT=" . ($isAutoCommit ? 1 : 0));
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::lastInsertId()
	 */
	public function lastInsertId() {
		return mysql_insert_id();
	}
	
	/**
	 * 简单查询
	 *
	 * @param String $sql
	 * @param array $args
	 * @return \core\Object
	 */
	public function query($sql, array $args = array()) {
		if ($args) {
			$sql = SqlBuilder::format($sql, $args);
		}
		$sql = SqlBuilder::tablePrefix($sql);	
		
		$this->execTimes ++; // 记录数据库查询次数
		$this->log[] = $sql;
		
		$query = mysql_query($sql, $this->link);
		
		if(false === $query) {
			$this->log[] = $this->getLastErr();
			// 抛出异常以便事务回滚并监控到异常信息
			throw new Exception($this->getLastErr());
		}
		
    	return $query;
	}
	
	/**
	 * 执行SQL、针对没有结果集合返回的操作，比如INSERT、UPDATE、DELETE等操作，它返回的结果是当前操作影响的列数。
	 * 
	 * @param string $sql
	 * @param array $args
	 * @return bool 
	 */
	public function exec($sql, array $args = array()) {
		return $this->query($sql, $args);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::getAll()
	 */
	public function getAll($sql, array $args = array(), $allowCache = false) {
		$result = $this->query($sql, $args);
		
		$rs = array();		
		while (false !== ($row = mysql_fetch_assoc($result))) {
			$rs[] = $row;
		}
		
		return $rs;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::getRow()
	 */
	public function getRow($sql, array $args = array(), $allowCache = false) {
		$result = $this->query($sql, $args);
		$row = mysql_fetch_assoc($result);
		return $row;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::getOne()
	 */
	public function getOne($sql, array $args = array(), $allowCache = false) {
		$result = $this->query($sql, $args);				
		$rs = mysql_fetch_row($result);		
		return $rs[0];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\Object::getLastErr()
	 */
	public function getLastErr() {
		return mysql_error();
	}
}

