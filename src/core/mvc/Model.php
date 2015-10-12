<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\mvc;

use core\adapter\db\SqlBuilder;
/**
 * 模型基类
 * 
 * 基于数据库的领域模型（由业务逻辑+数据访问组成）
 * 
 * 模型对应表字段的值映射(保存)在Model::$attr中和Model::$fieldMap设置的字段对应的属性中。
 * 可以通过Model->setPkv($id)，Model->getPkv()或$this->__primary_key_values 设置和访问模型对应表主键的值。
 * 
 * @package     core.mvc
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.mvc.model.html
 * @since       1.0.0
 */
abstract class Model extends \core\Object {	
	/**
	 * 模型对应数据表名
	 * 
	 * @var string = ''
	 */
	protected $table = '';
	
	/**
	 * 请不要覆盖此属性，生成对象后自动给该变量赋值
	 * 为减少出错的可能，将表结构，主键、主键值、表字段、表信息合并到该数组
	 * @var array = array()
	 */
	protected $internal = array(
		'fields' => '', // 字段列表
		'pk'     => '', // 主键名，如果是多个字段构成的主键，则使用数组表示，如: array('pk1', 'pk2', ...)
		'ai'     => false, // 主键是否是自动增长
	);
	
	/**
	 * 属性和表字段的绑定
	 * array(
	 *     '表字段1' => '属性1',
	 *     '表字段2' => '属性2',
	 *     ...
	 * )
	 * @var array = array()
	 */
	protected $fieldMap = array();
		
	/**
	 * 模型是否已加载
	 * @var bool = false
	 */
	protected $loaded = false;
	
	/**
	 * 锁定字段不允许设置值
	 * @var array = array()
	 */
	protected $lockedFields = array();
		
	/**
	 * 初始化表对象实例
	 * 如果集成模型基类后重写构造函数，必须在构造函数中调用父类的构造函数 parent::__construct();
	 * @throws \core\mvc\Exception
	 */
	public function __construct() {
		if (!$this->table) {
			throw new Exception(get_class($this).'::$table must not be empty!');
		}
		
		$tableInfo = static::db()->getTableInfo($this->table);
		
		$this->internal['fields'] = array_keys($tableInfo['fields']); // 表字段名列表
		$this->internal['pk']     = $tableInfo['pk']; // 表主键名，如果是多个字段的主键，则为array('主键1', '主键2')
		$this->internal['ai']     = $tableInfo['ai'];
	}
	
	/**
	 * 获取一个属性的值
	 * @param string $field
	 * @return mixed
	 */
	protected function &getFieldVal($field) {
	    if($this->fieldMap && key_exists($field, $this->fieldMap)) {			
			$attr = $this->fieldMap[$field];
			$getMethod = "get".ucfirst($attr);
			if(method_exists($this, $getMethod)) {
				return $this->$getMethod();
			} else {
				return $this->$attr;			
			}
		} else {
			return $this->attrs[$field];
		}
	}

	/**
	 * 获取属性
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function &__get($name) {
		// 不允许通过__get访问已定义的私有成员
		if (is_string($name) && property_exists($this, $name)) {
			$getFn = 'get' . ucfirst($name);
			throw new \core\mvc\Exception("Property '{$name}' access denied,you can access it by '{$getFn}()' if it's possible");
		}
		
		$name = strtolower($name);
		$name == '__primary_key_values' && $name = $this->getPk(); // $this->__primary_key_values 为获取主键值
		if (is_array($name)) {
			// 多字段主键值
		    $rVal = array();
			foreach ($name as $field) {
				$rVal[$field] = $this->getFieldVal($field);
			}
			return $rVal;
		} else {
		    return $this->getFieldVal($name);
		}
	}
	
	/**
	 * 设置一个属性的值
	 * @param string $k
	 * @param mixed $v
	 */
	protected function setFieldVal($k, $v) {
		// 表字段有对应已定义模型类属性
		if($this->fieldMap && array_key_exists($k, $this->fieldMap)) {			
			$attr = $this->fieldMap[$k];
			$setMethod = "set".ucfirst($attr);
			if(method_exists($this, $setMethod)) {
				$this->$setMethod($v);			
			} else {
				$this->$attr = $v;
			}
		} else {
			$this->attrs[$k] = $v;
		}
		
		return $this;
	}
	
	/**
	 * 设置属性
	 *
	 * @param string $name
	 * @param mixed $val
	 * @return \core\Object
	 */
	public function __set($name, $val) {
		if (property_exists($this, $name)) {
			throw new Exception("Property '{$name}' access denied");
		}

		$name = strtolower($name);
		$name == '__primary_key_values' &&  $name = $this->getPk(); // $this->__primary_key_values = $val 为设置主键值
		
		if (is_array($name)) {
			// 多字段主键值必须设置全部主键字段值
			if (!is_array($val) || array_diff($name, array_keys($val))) {
				throw new Exception('The primary value must be contain all Primary keys');
			}
		    foreach ($val as $field => $v) {
				in_array($field, $name) && $this->setFieldVal($field, $v);
			}
		} else {
		    $this->setFieldVal($name, $val);
		}
		
		return $this;
	}
		
	/**
	 * 该属性是否已经设置
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name) {
		$name = strtolower($name);
		
		if ($name == '__primary_key_values') {
			return true;
		} else {
			return array_key_exists($name, $this->attrs);
		}
	}
	
	/**
	 * 释放属性
	 *
	 * @param string $name
	 */
	public function __unset($name) {
		$name = strtolower($name);
		$name == '__primary_key_values' && $name = $this->getPk();
		
		if (is_array($name)) {
			foreach ($name as $field) {
				unset($this->attrs[$field]);
			}
		} else {
			unset($this->attrs[$name]);
		}
		
		return $this;
	}
	
	/**
	 * 设置模型主键值
	 * 
	 * @param mixed $pkv 主键值，如果是多个字段构成的主键，则使用关联数组结构，如: $pkv = array('pk1' => 123, 'pk2' => 'value', ...)
	 * @throws \core\mvc\Exception
	 * @return \core\mvc\Model
	 */
	public function setPkv($pkv) {
		if (!(is_scalar($pkv) || is_array($pkv))) {
			throw new Exception('object or resource is not allow for param $id of '.get_class($this).'::->setPkv($pkv)');
		}
		$this->__primary_key_values = $pkv;
		
		return $this;
	}
			
	/**
	 * 从持久层加载模型数据,根据主键加载
	 * @throws \core\mvc\Exception
	 * @return bool
	 */
	public function load() {
		return $this->loadBy($this->pkvWhere());
	}
	
	/**
	 * 根据条件加载实例
	 * @param array $whereArr
	 * @throws \core\mvc\Exception
	 * @return boolean
	 */
	public function loadBy(array $whereArr = array()) {
		$where = SqlBuilder::whereArr($whereArr);
		if (empty($where)) {
			throw new Exception('The $whereArr param format error in '.get_class($this).'::loadBy($whereArr)!');
		}
		
		$array = static::db()->getRow("SELECT * FROM %t WHERE %x", array($this->table, $where));
		
		if(false !== $array) {
			$this->fromArray($array);
		} else {
			return false;
		}
		
		$this->loaded = true;
	}
	
	/**
	 * 从数组加载实例数据
	 * @param array $array
	 * @return \core\mvc\Model
	 */
	public function fromArray($array) {
		foreach ($array as $field => $value) {
			$field = strtolower($field);
			$this->setFieldVal($field, $value);		
		}
		
		$this->loaded = true;
		
		return $this;
	}
	
	/**
	 * 更新当前模型实例数据持久层信息
	 * 
	 * @return \core\mvc\Model
	 */
	public function update() {
		$data = $this->toArray();
		
		unset($data['uuid']); // 坚决不允许修改uuid
		
		// 不允许修改主键值
		foreach ((array)($this->getPk()) as $pk) {
			unset($data[$pk]);
		}
		
		return $this->updateBy($data, $this->pkvWhere());
	}

	/**
	 * 将当前模型实例添加到数据持久层
	 *
	 * @return bool
	 */
	public function create() {
		$data = $this->toArray();
		
		// 新增记录自动增长主键不允许设置值
		if($this->internal['ai']) {
			unset($data[$this->getPk()]);
		}

		$sql = "INSERT INTO %t SET %x";
		$arg = array($this->table, $this->fieldSet($data));
		self::db()->exec($sql); // 如果出错会抛出异常，无需判断false
		
		$insertId = null;
		if ($this->internal['ai']) {
			$insertId = self::db()->lastInsertId();
		} else if (is_array($this->getPk())) {
			$insertId = array();
			foreach ($this->getPk() as $pk) {
				if (isset($data[$pk])) {
					$insertId[$pk] = $data[$pk];
				}
			}
		} else if (!empty($this->getPk())) {
			$insertId = $data[$this->getPk()];;
		}
	
		$this->setPkv($insertId);
			
		return true;
	}
	
	/**
	 * 替换内容，不做是否存在的判断
	 * @return boolean
	 */
	public function replace() {
		$arg = array($this->table, $this->fieldSet($this->toArray()));
		return self::db()->exec("REPLACE INTO %t SET %x", $arg);
	}
		
	/**
	 * 是否存在该实例的持久信息
	 * 
	 * @throws \core\mvc\Exception
	 * @return bool
	 */
	public function isExist() {
		static $isExist = array();
		
		$key = md5(serialize($this->__primary_key_values));
		
		if(!isset($isExist[$this->tableClass][$key])) {
			$this->checkPkId();
			isset($isExist[$this->tableClass]) || $isExist[$this->tableClass] = array();
			$isExist[$this->tableClass][$key] = (bool)$this->count(array('where' => $this->pkvWhere()));		
		}
		
		return $isExist[$this->tableClass][$key];
	}
	
	/**
	 * 获取对象实例的主键值
	 * @return mixed 如果是多个字段构成的主键，将返回数组结构的值，如: $pkv = array('pk1' => 123, 'pk2' => 'y', ...)
	 */
	public function getPkv() {
		return $this->__primary_key_values;
	}
	
	public function getPk() {
		return $this->internal['pk'];
	}
	
	/**
	 * 将实体对象转成数组型供调用属性数据
	 * @return array
	 */
	public function toArray() {
		$arr = array();
		// 从未指定的字段中读取字段kv
		foreach ($this->attrs as $field => $value) {
			$arr[strtolower($field)] = $value;
		}
		
		// 从指定的属性中读取字段kv
		foreach ($this->fieldMap as $field => $attr) {
			if (!isset($this->$attr)) {
				unset($arr[strtolower($field)]);
			} else {
				$arr[strtolower($field)] = $this->$attr;
			}
		}
		
		return $arr;
	}
	
	/**
	 * 删除一个持久化实体记录
	 *
	 * @return bool|int
	 */
	public function delete() {
		return $this->deleteBy($this->pkvWhere());
	}
	
	/**
	 * 根据条件删除实例
	 * @param array $whArr
	 * @throws Exception
	 * @return boolean
	 */
	public function deleteBy($whArr = array()) {
		$where = SqlBuilder::whereArr($whArr ? $whArr : $this->pkvWhere());
		if(!trim($where)) {
			throw new Exception('请传入删除记录的条件'); 
		}
		
		$exe = self::db()->exec("DELETE FROM %t WHERE %x", array($this->table, $where));
		
		return $exe;
	}
	
	/**
	 * 保存记录
	 * @return bool
	 */
	public function save() {
		if($this->__primary_key_values && $this->isExist()) {
			return $this->update();
		} else {
			return $this->create();
		}
	}
	
	/**
	 * 根据主键作为条件/传递给数据访问层（进行删改读操作）的默认条件
	 * @throws \core\mvc\Exception
	 * @return array
	 */
	protected function pkvWhere() {
		$this->checkPkId();
		if (is_array($this->getPk())) {
			if (is_scalar($this->getPkv())) {
				throw new Exception('Error type of '.__CLASS__.'::$id, it mast be array');
			}
			
			$whereArr = array();
			foreach ((array)($this->getPkv()) as $pk => $pv) {
				$whereArr[] = array($pk, $pv, '=');
			}
		} else {
		    $whereArr = array($this->getPk(), $this->getPkv(), '=');
		}
		
		return $whereArr;
	}

	/**
	 * 获取数据库操作对象实例
	 *
	 * @return \core\adapter\db\IDB
	 */
	protected static function db() {
		static $instance = null;
		if(!$instance) {
			$instance = \core\Factory::db();
		}
		
		return $instance;
	}
	
	/**
	 * 模型符合条件的记录数
	 * 
	 * 
	 * @see \core\adapter\db\SqlBuilder::buildQueryOptions();
	 * 
	 * @param array $option = <pre>array(
	 *     'table'  => 'table_a, table_b AS b', // 查询的表名，可以是多个表，默认是当前模型的表
	 *     'join'   => array(array('table_name', 'field_a', 'field_b'), arrray(), ...), // => LEFT JOIN `table_name` ON `field_a` = `field_b`
	 *     'where'  => array() // 查询条件 array('and|or', array('字段1', '值', '=,+,-,|,&,^,like,in,notin,>,<,<>,>=,<=,!='), array('字段1', '值', '逻辑'), ...)
	 *     'group'  => '', // 将对其进行SQL注入过滤并且在前面加上GROUP BY 
	 *     'having' => '', // 将对其进行SQL注入过滤并且在前面加上 HAVING
	 * )</pre>
	 * @return number
	 */
	public function count($options = array(), $field = '*') {
		$options['fields'] = $field;
		empty($options['table']) && $options['table'] = $this->table;
		$options = SqlBuilder::buildQueryOptions($options);
		
		$sql = "SELECT COUNT({$options['fields']})
		        FROM {$options['table']} {$options['join']} 
		        {$options['where']} 
		        {$options['group']} 
		        {$options['having']}";
		$num = static::db()->getOne($sql);
		
		return $num;
	}
	
	/**
	 * 分页获取模型多条记录
	 * 
	 * @see \core\db\ADB::whereArr()
	 * @see \core\adapter\db\SqlBuilder::buildQueryOptions()
	 * 
	 * @param array $option 查询选项(详看See Also)
	 * @param int $offset 获取记录开始下标
	 * @param int $rows 获取记录数
	 * @param bool $isCache 是否缓存查询结果
	 * @return array
	 */
	public function select($options, $offset = 0, $rows = 20, $isCache = false) {
		empty($options['table']) && $options['table'] = $this->table;
		$options = SqlBuilder::buildQueryOptions($options);
		
		$sql = "SELECT {$options['fields']} 
		        FROM {$options['table']} {$options['join']} 
		        {$options['where']} 
		        {$options['group']} 
		        {$options['having']} 
		        {$options['order']} 
		        LIMIT {$offset}, {$rows}";
		return static::db()->getAll($sql, array(), $isCache);
	}
	
	/**
	 * 根据条件更新表数据
	 * @param array $data kv数组
	 * @param array $whArr 条件数组
	 * @return number
	 */
	public function updateBy($data, $whArr) {
		$where = SqlBuilder::whereArr($whArr);
		
		if (empty($where)) {
			throw new Exception('The $whereArr param format error!');
		}
		
		$arg = array($this->table, $this->fieldSet($data), $where);
		$ret = static::db()->exec("UPDATE %t SET %x WHERE %x", $arg);
		
		return $ret;
	}
		
	/**
	 * 获取一条记录
	 * 
	 * @see \core\db\Adb::whereArr()
	 * 
	 * @param array $option 查询条件，参看 \core\mvc\Mocel::select()
	 * @param bool $isCache 是否缓存查询结果
	 * @return array
	 */
	public function fetchRow($options, $isCache = false) {
		$rows = $this->select($options, 0, 1, $isCache);
		
		return $rows ? $rows[0] : array();
	}

	/**
	 * 获取一个字段的值
	 *
	 * @see \core\adapter\db\ADB::whereArr()
	 * 
	 * @param string $field 查询的字段
	 * @param array $option 查询条件，参看 \core\mvc\Mocel::select()
	 * @param string $order 显示排序
	 * @param bool $isCache 是否缓存查询结果
	 * @return scalar
	 */
	public function fetchField($field = '', $options = array(), $isCache = false) {
		$field && $options['fields'] = $field;
		$row = $this->fetchRow($options, $isCache);
	
		return $row ? $row[$field] : null;
	}
	
	/**
	 * 修改字段值
	 * @param array $kv
	 * @return boolean
	 */
	public function alterField($kv) {
		$arg = array($this->table, $this->fieldSet($kv), SqlBuilder::whereArr($this->pkvWhere()));
		$ret = static::db()->exec("UPDATE %t SET %x WHERE %x", $arg);	    
		return $ret;
	}
	
	/**
	 * 检查主键及主键值是否已设置
	 * @throws \core\mvc\Exception
	 */
	protected function checkPkId() {
		if (!$this->getPk() || null === $this->getPkv()) {
			throw new Exception('Please set the model\'s pk and id');
		}
		
		return true;
	}
	
	/**
	 * 获取模型实例
	 * @return \core\mvc\Model
	 */
	public static function getInstance() {
		return new static();
	}
	

	/**
	 * 增改的字段信息 SET key => val
	 * @param array $data
	 * @throws Exception
	 * @return Ambigous <string, string>
	 */
	protected function fieldSet($data) {
		$set = array();
		$arg = array();
	
		// 取表中存在的字段
		foreach($data as $k => $v) {
			if (in_array($k, $this->lockedFields) || !@in_array($k, $this->internal['fields'])) {
				continue;
			}
				
			if (is_array($v)) {
				$v = serialize($v);
			} if ($v === null) {
				$v = 'null';;
			}
	
			$set[] = " %a = %s ";
			$arg[] = $k;
			$arg[] = $v;
		}
	
		if (!$set || !$arg) {
			throw new Exception('请传入正确的数据');
		}
	
		$sets  = join(',', $set);
	
		return SqlBuilder::format($sets, $arg);
	}

	/**
	 * 添加锁定字段，锁定字段后，不保添加/更新字段的值到数据库。
	 * @param string|array $fields
	 * @return \core\mvc\Model
	 */
	public function addLockFields($fields) {
		$fields = (array)$fields;
		$this->lockedFields = array_merge($this->lockedFields, $fields);
		return $this;
	}
}
