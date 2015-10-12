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
 * 数据库操作接口
 * 
 * @package     core.adapter.db
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.db.html
 * @since       1.0.0
 */
interface IDB {
	/**
	 * 获取模型数据表信息
	 * 
	 * <pre>
	 * arry(
	 *     'pk' => '主键',
	 *     'ai' => true/false, //主键是否是自动增长
	 *     'fields' => array(
	 *         '字段1名' => array(
	 *				'field'   => '字段1名',
	 *				'type'    => '字段类型',
	 *				'key'     => '索引类型', //PKI/MUL/UNI
	 *				'default' => '默认值',
	 *				'ai'      => '是否是自动增长的',
	 *         ),
	 *         '字段2' => array(
	 *				'field'   => $row['Field'],
	 *				'type'    => $row['Type'],
	 *				'key'     => $row['Key'],
	 *				'default' => $row['Default'],
	 *				'ai'      => $row['Extra'] == 'auto_increment',
	 *         ),
	 *         ...
	 *     )
	 * )
	 * </pre>
	 * @param string $table  表名
	 * @return array
	 */
	public function getTableInfo($table);
	
	/**
	 * 开始事务，数据库支持事务并启用的时候事务才有效，
	 * 默认没有启用自动提交，需要调用ADB::commit()提交
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
	public function beginTransaction();
	
	/**
	 * 提交事务
	 * 
	 * @return bool
	 */
	public function commit();
		
	/**
	 * 针对没有结果集合返回的操作
	 * 比如INSERT、UPDATE、DELETE等操作，它返回的结果是当前操作影响的列数。
	 * 当增删改SQL中有变量时使用，如果SQL中有变量，请使用prepare()，
	 * 
	 * @param string $sql
	 * @param array $args
	 * @throws \core\adapter\db\Exception
	 * @return int
	 */
	public function exec($sql, array $args = array());
	
	/**
	 * 取得上一步 INSERT 操作产生的 ID
	 *
	 * @return string 
	 */
	public function lastInsertId();
		
	/**
	 * 执行SQL查询语句
	 * 
	 * <pre>
	 * useage:
	 * $rs = $dbh->query($sql)->fetchColumn();
	 * $rs = $dbh->query($sql)->fetch();
	 * $rs = $dbh->query($sql)->fetchAll();
	 * </pre>
	 *
	 * @param String $sql
	 * @throws \core\adapter\db\Exception
	 * @return \PDOStatement
	 */
	public function query($sql, array $args = array());
	
	/**
	 * 事务回滚
	 * 
	 * @return bool
	 */
	public function rollBack();
	
	/**
	 * 设置是否自动提交事务，启用事务的时候有效
	 * 
	 * @param bool $isAutoCommit
	 * @return \core\adapter\db\IDB
	 */
	public function setAutoCommit($isAutoCommit = false);
	
	/**
	 * 获取最后错误的信息
	 * 
	 * @return string
	 */
	public function getLastErr();
			
	/**
	 * 获取第一列第一个字段
	 * 
	 * @param string $sql
	 * @param array $args
	 * @param bool $allowCache
	 */
	public function getOne($sql, array $args = array(), $allowCache = false);
	
	/**
	 * 获取所有记录
	 * 
	 * @param string $sql
	 * @param array $args
	 * @param bool $allowCache
	 */
	public function getAll($sql, array $args = array(), $allowCache = false);
	
	/**
	 * 获取第一列
	 * 
	 * @param string $sql
	 * @param array $args
	 * @param bool $allowCache
	 */
	public function getRow($sql, array $args = array(), $allowCache = false);
	
}

