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

use core\Router;
use core\util\Tree;

/**
 * 后台菜单
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class MenuModel extends \core\mvc\Model {
	protected $table = 'wk_menu';

	public function create() {
		$this->url = trim($this->url);
		$this->name = trim($this->name);
		
		if(!$this->name || !$this->url) {
			$this->setErr('菜单名称和地址不能为空');
			return false;
		}
		
		if($this->url == '#') {
			$this->mod = 'system';
			$this->ctl = 'default';
			$this->act = 'index';
		} else {
			$router = new Router();
			$router->parseUrl($this->url);
			$params = $router->params;
			
			$this->mod = $params['mod'];
			$this->ctl = $params['ctl'];
			$this->act = $params['act'];
		}
				 
		return parent::create();		
	}
	
	public function update() {	
		if(empty($this->name)) {
			$this->setErr('菜单名称不能为空');
			return false;
		}
		
		if($this->url == '#') {
			$this->mod = 'system';
			$this->ctl = 'default';
			$this->act = 'index';
		} else {
			$router = new Router();
			$router->parseUrl($this->url);
			$params = $router->params;
				
			$this->mod = $params['mod'];
			$this->ctl = $params['ctl'];
			$this->act = $params['act'];
		}
		
		return parent::update();		
	}
	
	/**
	 * 获取菜单
	 *
	 * @return array
	 */
	public function getTree() {
		$rs = $this->select(array('order' => 'displayorder ASC, id ASC'), 0, 9999);

		$tree = new Tree();
		$tree->set($rs, 'id', 'upid');
		$menu = $tree->get();
		
		return $menu;
	}
	
	public function getEnabledTree() {
		$rs = $this->select(array('where' => array('enabled', 1), 'order' => 'displayorder ASC, id ASC'), 0, 9999);
		
		$menu = array();
		$tree = new Tree();
		$tree->set($rs, 'id', 'upid');
		$menu = $tree->get();
		
		return $menu;
	}
		
	public function delete() {
		$menu = $this->getTree();
			
		if(!isset($menu[$this->id])) {
			$this->setErr('该菜单不存在');
			return false;
		}
	
		$item = $menu[$this->id];
		if ($item['chileArr']) {
			$this->setErr('该菜单还有子菜单，需要先删除子菜单才能删除。');
			return false;
		}
	
		return  parent::delete();
	}

	/**
	 * 移除模块后台菜单
	 *
	 * @param string $mod
	 * @throws \core\mvc\Exception
	 */
	public function removeByMod($mod) {
		return $this->deleteBy(array(array('mod', $mod), array('upid', 0, '>')));
	}
	
	/**
	 * 卸载模块的时候删除模块后台菜单接口
	 * @param string $name
	 * @param string $uri
	 * @return bool
	 */
	public function removeByNameUri($name, $uri) {
		$whereArr = array();
		$whereArr[] = array('name', $name);
		$whereArr[] = array('url', $uri);
		
		return $this->deleteBy($whereArr);
	}
}
