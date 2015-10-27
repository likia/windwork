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
 * 可以通过Model->setPkv($id)，Model->getPkv()或$this->__pkv 设置和访问模型对应表主键的值。
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
	 * 
	 * 设置表字段对应模型类的属性，以实现把类属性绑定到表字段，并且Model->toArray()方法可获取绑定属性的值。
	 * 表字段名不分大小写，属性名大小写敏感。
	 * array(
	 *     '表字段1' => '属性1',
	 *     '表字段2' => '属性2',
	 *     ...
	 * )
	 * @var array = array()
	 */
	protected $fieldMap = array();
		
	/**
	 * 模型是否已从数据库加载（通过Model->load()或Model->loadBy()）
	 * @var bool = false
	 */
	protected $loaded = false;
	
	/**
	 * 锁定字段不允许设置值
	 * @var array = array()
	 */
	private $lockedFields = array();
		
	/**
	 * 初始化表对象实例
	 * 如果集成模型基类后重写构造函数，必须在构造函数中调用父类的构造函数 parent::__construct();
	 * @throws \core\mvc\Exception
	 */
	public function __construct() {
		if (!$this->table) {
			throw new Exception(get_called_class().'::$table must not be empty!');
		}
		
		$tableInfo = static::db()->getTableInfo($this->table);
		
		$this->internal['fields'] = array_keys($tableInfo['fields']); // 表字段名列表，为支持不区分大小写，已转小写
		$this->internal['pk']     = $tableInfo['pk']; // 表主键名，已转为小写，如果是多个字段的主键，则为array('主键1', '主键2')
		$this->internal['ai']     = $tableInfo['ai'];
		
		// 使字段绑定属性的字段名不区分大小写
		if($this->fieldMap) {
			$this->fieldMap = array_combine(array_map('strtolower', array_keys($this->fieldMap)), array_values($this->fieldMap));
		}

		// 新增记录自动增长主键不允许设置值
		if($this->internal['ai']) {
			$this->addLockFields($this->internal['pk']);
		}
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
		$name == '__pkv' && $name = $this->getPk(); // $this->__pkv 为获取主键值
		
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
		$k = strtolower($k);
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
		// 是否存在该属性（属性大小写敏感，因此调用已定义属性时需注意大小写）
		if (property_exists($this, $name)) {
			throw new Exception("Property '{$name}' access denied");
		}

		$name = strtolower($name);
		$name == '__pkv' &&  $name = $this->getPk(); // $this->__pkv = $val 为设置主键值
		
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
		
		if ($name == '__pkv') {
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
		$name == '__pkv' && $name = $this->getPk();
		
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
			throw new Exception('object or resource is not allow for param $id of '.get_called_class().'::->setPkv($pkv)');
		}
		$this->__pkv = $pkv;
		
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
		if (empty($whereArr)) {
			throw new Exception('The $whereArr param format error in '.get_called_class().'::loadBy($whereArr)!');
		}

		$array = $this->fetchRow(array('where' => $whereArr));
		
		if($array) {
			$this->fromArray($array);
		    $this->loaded = true;
			return true;
		}
		
		return false;
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
		
		return $this;
	}
	
	/**
	 * 更新当前模型实例数据持久层信息
	 * 
	 * @return \core\mvc\Model
	 */
	public function update() {
		return $this->updateBy($this->toArray(), $this->pkvWhere());
	}

	/**
	 * 将当前模型实例添加到数据持久层
	 *
	 * @return bool
	 */
	public function create() {
		$data = $this->toArray();
		
		$arg = array($this->table, $this->fieldSet($data));
		self::db()->exec("INSERT INTO %t SET %x", $arg); // 如果出错会抛出异常，无需判断false
		
		// 插入数据库成功后设置主键值
		$insertId = null;
		if ($this->internal['ai']) {
			// 自增主键
			$insertId = self::db()->lastInsertId();
		} else if (is_array($this->internal['pk'])) {
			// 多个字段主键
			$insertId = array();
			foreach ($this->internal['pk'] as $pk) {
				if (isset($data[$pk])) {
					$insertId[$pk] = $data[$pk];
				}
			}
		} else if (!empty($this->internal['pk'])) {
			// 非自增单字段主键
			$insertId = $data[$this->internal['pk']];;
		}
	
		$this->setPkv($insertId);
			
		return true;
	}
	
	/**
	 * 插入多行数据
	 * 过滤掉没有的字段
	 * 
	 * @param array $rows
	 * @param string $replaceInto = false 是否使用 REPLACE INTO插入数据，false为使用 INSERT INTO
	 * @return PDOStatement
	 */
	public function addRows(array $rows, $replaceInto = false) {
		$type = $replaceInto ? 'REPLACE' : 'INSERT';
		
		// 数据中允许插入的字段
		$allowFields = array_keys(current($rows));
		$allowFields = array_map('strtolower', $allowFields);
		$allowFields = array_intersect($allowFields, $this->internal['fields']);
		$fields = SqlBuilder::quoteFields(implode(',', $allowFields));
		
		// 
		$valueArr = array();
		foreach ($rows as $row) {
			$rowStr = '';
			foreach ($row as $key => $val) {
				if (!in_array(strtolower($key), $allowFields)) {
					unset($row[$key]);
				}
			}
			$rowStr = implode(',', array_map('\core\adapter\db\SqlBuilder::quote', $row));
			$valueArr[] = "({$rowStr})";
		}
		$values = $rowStr = implode(',', $valueArr);
		
		return self::db()->exec("%x INTO %t (%x) VALUES %x", array($type, $this->table, $fields, $values));
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
		return (bool)$this->count(array('where' => $this->pkvWhere()));
	}
	
	/**
	 * 获取对象实例的主键值
	 * @return mixed 如果是多个字段构成的主键，将返回数组结构的值，如: $pkv = array('pk1' => 123, 'pk2' => 'y', ...)
	 */
	public function getPkv() {
		return $this->__pkv;
	}
	
	/**
	 * 获取主键名
	 * @return string|array
	 */
	public function getPk() {
		return $this->internal['pk'];
	}
	
	/**
	 * 将实体对象转成数组型供调用属性数据
	 * 建议直接用对象访问数据，尽可能少用转换成数组的方式获取数据。
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
		if($this->__pkv && $this->isExist()) {
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
				throw new Exception('Error type of '.get_called_class().'::$id, it mast be array');
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
	 * @param array $options = <pre>array(
	 *     'table'  => 'table_a, table_b AS b', // 查询的表名，可以是多个表，默认是当前模型的表
	 *     'join'   => array(array('table_name', 'field_a', 'field_b'), arrray(), ...), // => LEFT JOIN `table_name` ON `field_a` = `field_b`
	 *     'where'  => array() // 查询条件 array('and|or', array('字段1', '值', '=,+,-,|,&,^,like,in,notin,>,<,<>,>=,<=,!='), array('字段1', '值', '逻辑'), ...)
	 *     'group'  => '', // 将对其进行SQL注入过滤并且在前面加上GROUP BY 
	 *     'having' => '', // 将对其进行SQL注入过滤并且在前面加上 HAVING
	 * )</pre>
	 * @param string $field = '*'
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
	 * @param array $options 查询选项(详看\core\adapter\db\SqlBuilder::buildQueryOptions())
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

		// 坚决不允许修改uuid值
		unset($data['uuid']); 
		
		// 不允许修改主键值
		foreach ((array)($this->getPk()) as $pk) {
			unset($data[$pk]);
		}
		
		$arg = array($this->table, $this->fieldSet($data), $where);
		$ret = static::db()->exec("UPDATE %t SET %x WHERE %x", $arg);
		
		return $ret;
	}
		
	/**
	 * 获取一条记录
	 * 
	 * @see \core\adapter\db\SqlBuilder::buildQueryOptions()
	 * 
	 * @param array $options 查询条件，参看 \core\mvc\Mocel::select()
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
	 * @see \core\adapter\db\SqlBuilder::buildQueryOptions()
	 * 
	 * @param array $options 查询条件，参看 \core\mvc\Mocel::select()
	 * @param string $field 查询的字段
	 * @param bool $isCache 是否缓存查询结果
	 * @return scalar
	 */
	public function fetchField($options = array(), $field = '', $isCache = false) {
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
	 * 从数组的下标对应的值中获取SQL的"字段1=值1,字段2=值2"的结构
	 * @param array $data
	 * @throws Exception
	 * @return string 返回 "`f1` = 'xx', `f2` = 'xxx'"
	 */
	protected function fieldSet(array $data) {
		return SqlBuilder::buildSqlSet($data, $this->internal['fields'], $this->lockedFields);
	}

	/**
	 * 添加锁定字段，锁定字段后，不保添加/更新字段的值到数据库。
	 * @param string $fields 字段名，用半角逗号隔开
	 * @return \core\mvc\Model
	 */
	public function addLockFields($fields) {
		$fields = explode(',', str_replace(' ', '', strtolower($fields)));
		$this->lockedFields = array_merge($this->lockedFields, $fields);
		return $this;
	}
}
