<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\model;

use core\util\Tree;

/**
 * 前台导航
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class NavModel extends \core\mvc\Model {
	protected $table = 'wk_nav';

	public function create() {
		$this->url = trim($this->url);
		if (empty($this->name) || empty($this->url)) {
			$this->setErr('名称和链接必填');
			return false;
		}

		if (empty($this->upid)) {
			$this->setErr('请选择上级导航');
			return false;
		}
		
		$this->parseUrl();
		
		static::clearCache();
		return parent::create();		
	}
	
	public function update() {
		$this->url = trim($this->url);
		if ($this->id <= 3) {			
			$this->setErr('不允许修改该导航');
			return false;
		}

		if (empty($this->name) || empty($this->url)) {
			$this->setErr('名称和链接必填');
			return false;
		}
		
		if (empty($this->upid)) {
			$this->setErr('请选择上级导航');
			return false;
		}

		$this->parseUrl();
		
		static::clearCache();
		
		return parent::update();		
	}
	
	protected function parseUrl() {
		$router = new \core\Router();
		$router->parseUrl($this->url);
		
		$this->mod = $router->params['mod'];
		$this->ctl = $router->params['ctl'];
		$this->act = $router->params['act'];
		$this->dot = implode('/', $router->params['...']);
	}
	
	/**
	 * 获取导航
	 *
	 * @return array
	 */
	public function getTree() {
		$rs = $this->select(array('order' => 'displayorder ASC, id ASC'), 0, 9999);
		
        $nav = array();
		$tree = new Tree();
		$tree->set($rs, 'id', 'upid');
		$nav = $tree->get();
		
		return $nav;
	}
	
	/**
	 * 获取导航树形结构
	 * @param int $id
	 * @param bool $getSelf
	 * @return array
	 */
	public function getEnabledTree($id = 0, $getSelf = false) {
		$rs = $this->select(array('where' => array('enabled', 1), 'order' => 'displayorder ASC, id ASC'), 0, 9999);
		
        $nav = array();
		$tree = new Tree();
		$tree->set($rs, 'id', 'upid');
		$nav = $tree->get($id, $getSelf);
		
		return $nav;
	}
	

	/**
	 * 根据类型获取导航列表
	 * @param int $type 1:顶部；2：中间；3：底部
	 * @return array
	 */
	public static function getNav($type = 0) {
		if(!$navs = \core\Factory::cache()->read('system/nav-'.$type)) {
			$navObj = new self();
			$navs = $navObj->getEnabledTree($type);
			\core\Factory::cache()->write('system/nav-'.$type, $navs);
		}
		
		return $navs;
	}
	
	public function delete() {
		if ($this->id <= 3) {
			$this->setErr('不允许删除该导航');
			return false;
		}
		
		static::clearCache();
		return  parent::delete();
	}
	
	/**
	 * 检查是否为当前导航
	 * @param array $navItem
	 * @param string $mod
	 * @param string $ctl
	 * @param string $act
	 * @param array $params
	 * @return boolean
	 */
	public static function isCurrentNav($navItem, $mod, $ctl, $act, $params = array()) {
		if (strtolower("{$navItem['mod']}.{$navItem['ctl']}.{$navItem['act']}") != strtolower("{$mod}.{$ctl}.{$act}")) {
			return false;
		}
		
		if ($params) {
			$params = (array)$params;
			if (!in_array($navItem['dot'], $params)) {
				return false;
			}
		}
				
		return true;
	}
	
	/**
	 * 
	 */
	public static function clearCache() {
		\core\Factory::cache()->clear('nav');
	}
}
