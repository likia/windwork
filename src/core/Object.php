<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core;

/**
 * 基础类，支持动态增删读属性，错误信息管理。
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.object.html
 * @since       1.0.0
 */
abstract class Object {
	/**
	 * 错误信息
	 * @var array
	 */
	protected $errs = array();
	
	/**
	 * 用来保存动态属性
	 * @var array
	 */
	protected $attrs = array();
	
	/**
	 * 获取属性
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function &__get($name) {
		if (property_exists($this, $name)) {
			throw new Exception(get_called_class() . "::{$name} access denied");
		}
		
		$name = strtolower($name);
		if(isset($this->attrs[$name])) {
			return $this->attrs[$name];
		} else {
			$tmp = null;
			return $tmp;
		}
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
			throw new Exception(get_called_class() . "::{$name} access denied");
		}
		
		$name = strtolower($name);
		$this->attrs[$name] = $val;
		
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
		
		return array_key_exists($name, $this->attrs);
	}
	
	/**
	 * 释放属性
	 *
	 * @param string $name
	 */
	public function __unset($name) {
		$name = strtolower($name);
		$this->attrs[$name] = null;
		
		return $this;
	}
	
	/**
	 * 动态属性访问
	 * @param string $name
	 * @param mixed $args
	 * @throws \BadMethodCallException
	 * @return \core\Object|multitype:
	 */
	public function __call($name, $args = array()) {
		// set
		if (substr($name, 0, 3) == 'set') {
			$attr = substr(strtolower($name), 3);
			$this->attrs[$attr] = $args[0];
			
			return $this;
		}
		// get
		else if (substr($name, 0, 3) == 'get') {
			$attr = substr(strtolower($name), 3);
			if (key_exists($attr, $this->attrs)) {
				return $this->attrs[$attr];
			}
		}
	
		$message = 'Not exists method called: ' . get_called_class() . '::'.$name.'()';
		throw new \BadMethodCallException($message);		
	}
	
	/**
	 * 获取错误信息
	 * 
	 * @return array
	 */
	public function getErrs() {
		return $this->errs;
	}
	
	/**
	 * 获取最后一个错误的内容
	 * 
	 * @return string
	 */
	public function getLastErr() {
		$err = end($this->errs);
		reset($this->errs);
		return $err;
	}	
		
	/**
	 * 是否有错误
	 *
	 * @return bool
	 */
	public function hasErr() {
		return empty($this->errs) ? false : true;
	}
	
	
	/**
	 * 设置错误信息
	 *
	 * @param mixed $msg
	 * @return \core\Object
	 */
	public function setErr($msg) {
		if(is_array($msg)) {
			foreach($msg as $err) {
			    $this->errs[] = $err;
			}
		} else {
		    $this->errs[] = $msg;
		}
		
		return $this;
	}
}
