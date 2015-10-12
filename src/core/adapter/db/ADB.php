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

use core\Factory;

/**
 * 数据库操作抽象类
 *  
 * @package     core.adapter.db
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.adapter.db.html
 * @since       1.0.0
 */
abstract class ADB {
	/**
	 * 是否启用调试模式
	 * @var bool
	 */
	public $debug = false;
	
	/**
	 * 数据库连接配置
	 * @var array
	 */
	protected $cfg = array(
		'db_host' => 'localhost',
		'db_port' => 3306,
		'db_user' => 'root',
		'db_pass' => '',
		'db_name' => '',
		'db_table_prefix' => 'wk_',
	);

	/**
	 * 开启事务的次数，记录次数解决嵌套事务的问题
	 * @var int
	 */
	protected $transactions = 0;
	
	/**
	 * 日志内容
	 * @var string
	 */
	protected $log = array();
		
	/**
	 * 数据库当前页面连接次数,每次实行SQL语句的时候 ++
	 * 
	 * @var int
	 */
	public $execTimes = 0;
	
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
	public function getTableInfo($table) {
		static $tableInfoList = array();
		empty($tableInfoList) && $tableInfoList = Factory::cache()->read('db/tableInfoList');

		if((!$tableInfoList || empty($tableInfoList[$table]))) {
			//"SHOW FULL COLUMNS FROM {$table}"
			$rows = $this->getAll("SHOW COLUMNS FROM {$table}");
			$tableInfo = array(
				'pk'      => '', 
				'ai'      => false, 
				'fields'  => array()
			);
			foreach ($rows as $row) {
				$tableInfo['fields'][$row['Field']] = $row;
				
				if ($row['Key'] == 'PRI') {
					if($tableInfo['pk']) {
						$tableInfo['pk'] = (array)$tableInfo['pk'];
						$tableInfo['pk'][] = $row['Field'];
					} else {
						$tableInfo['ai'] = $row['Extra'] == 'auto_increment';
						$tableInfo['pk'] = $row['Field'];
					}
				}
			}
			
			$tableInfoList[$table] = $tableInfo;
			Factory::cache()->write('db/tableInfoList', $tableInfoList);
		}
		
		return $tableInfoList[$table];
	}
			
	
	/**
	 * 构筑函数设置配置信息
	 * @param array $cfg
	 */
	public function __construct($cfg) {
		$this->cfg = array_intersect_key($this->cfg, $cfg);
		$this->debug = $cfg['db_debug'];
	}
	
	/**
	 * 如果启用调试并启用数据库日志，则保存sql日志
	 */
	public function __destruct(){
		if ($this->debug && $this->log) {
			$log = '';
			
			foreach ($this->log as $sql) {
				$log .= "\r\n-- explain\r\n" . $sql;
			}
			
			if (!$log) {
				return;
			}

			$log = "\r\n\r\n-- ------------------------------------------ "
				 . "\r\n-- {$_SERVER['REQUEST_URI']}"
				 . "\r\n-- " . date('Y-m-d H:i:s') . $log;			
			@file_put_contents(SRC_PATH."data/log/trace.sql", $log, FILE_APPEND);
		}
	}
}


