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
 * 使用 PDO扩展对MySQL数据库进行操作
 * 如果是自己写sql语句的时候，请不要忘了防注入，只是在您不输入sql的情况下帮您过滤MySQL注入了
 *
 * @todo 事务链
 * @package     core.adapter.db
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.db.html
 * @since       1.0.0
 */
class PDOMySQL extends ADB implements IDB, \core\adapter\IFactoryAble {
	/**
	 * 数据库操作对象
	 * 
	 * @var \PDO
	 */
	private $dbh = null;
	
	/**
	 * 数据库连接
	 *
	 * @param array $cfg
	 * @throws \core\adapter\db\Exception
	 */
	public function __construct($cfg) {
		if (!class_exists('\\PDO')) {
			throw new Exception('连接数据库时出错：请联系空间提供者启用PHP的PDO_MYSQL模块以支持连接MYSQL数据库。');
		}
	
		parent::__construct($cfg);
		
		try {		
			$dsn = "mysql:host={$cfg['db_host']};port={$cfg['db_port']};dbname={$cfg['db_name']}";
			$this->dbh = new \PDO($dsn, $cfg['db_user'], $cfg['db_pass']);
		} catch (\PDOException $e) {
			throw new Exception('连接数据库时出错：'.$e->getMessage());
		}
		
		$this->dbh->exec("SET NAMES utf8,sql_mode=''");
		$this->dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::beginTransaction()
	 */
	public function beginTransaction() {
		if (!$this->transactions) {
			$this->dbh->beginTransaction();
		}

		++$this->transactions;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::commit()
	 */
	public function commit() {
		--$this->transactions;
	
		if($this->transactions == 0 && false === $this->dbh->commit()) {
		    throw new \core\adapter\db\Exception($this->getLastErr());
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::lastInsertId()
	 */
	public function lastInsertId() {
		return $this->dbh->lastInsertId();
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
							
		// 记录数据库查询次数
		$this->execTimes ++;
		$this->log[] = $sql;
		
		$query = $this->dbh->query($sql);		
        if(false === $query) {
        	$this->log[] = $this->getLastErr();
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
		if ($args) {
			$sql = SqlBuilder::format($sql, $args);
		}
		
	    $sql = SqlBuilder::tablePrefix($sql);	
							
		// 记录数据库查询次数
		$this->execTimes ++;
		$this->log[] = $sql;
		
		$result = $this->dbh->exec($sql);

		if(false === $result) {
		    $this->log[] = $this->getLastErr();
			throw new Exception($this->getLastErr());
		}
		    	    	    	
		return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::getAll()
	 */
	public function getAll($sql, array $args = array(), $allowCache = false) {
		$query = $this->query($sql, $args);
		if (!$query) {
			return  array();
		}
		
		$rs = $query->fetchAll(\PDO::FETCH_ASSOC);
		return $rs;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::getRow()
	 */
	public function getRow($sql, array $args = array(), $allowCache = false) {
		$query = $this->query($sql, $args);
		if (!$query) {
			return  array();
		}
		
		$rs = $query->fetch(\PDO::FETCH_ASSOC);
		return $rs;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DB::getOne()
	 */
	public function getOne($sql, array $args = array(), $allowCache = false) {
		$query = $this->query($sql, $args);
		if (!$query) {
			return  null;
		}
		
		$rs = $query->fetchColumn();
		return $rs;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \core\Object::getLastErr()
	 */
	public function getLastErr() {
		return implode(' ', $this->dbh->errorInfo());
	}
		
	/**
	 * 设置是否自动提交事务，启用事务的时候有效
	 * 
	 * @return \core\adapter\db\IDB
	 */
	public function setAutoCommit($isAutoCommit = false) {
		$this->dbh->setAttribute(\PDO::ATTR_AUTOCOMMIT, $isAutoCommit);
		
		return $this;
	}
	
	public function rollBack() {
		--$this->transactions;
			
		if ($this->transactions <= 0) {
			$this->dbh->rollback();
		} else {			
			throw new \core\adapter\db\Exception($this->getLastErr());
		}
	}
}

