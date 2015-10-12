<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\wx\model;

use core\util\Tree;
use core\wx\ResponseCode;

/**
 * 自定义菜单模型（官方微信菜单）
 *
 *
 * @package     module.wx.model
 * @copyright   © 2010-2015 恒辉科技版权所有
 * @author      恒辉科技 <cmpan@qq.com>
 */
class DiyMenuModel extends \core\mvc\Model {
	/**
	 * 模型对应的数据表
	 * @var string
	 */
	protected $table = 'wx_diymenu';

	/**
	 * 获取（树形）菜单列表
	 * @return multitype:
	 */
	public function getList() {
		$cdt = array(
			'order' => 'displayorder ASC, id ASC',
		);

		$rs = $this->select($cdt, 0, 99);

		$list = array();
		if($rs) {
			$treeObj = new Tree();
			$treeObj->set($rs, 'id', 'parentid');
			$list = $treeObj->get();
		}

		return $list;
	}

	/**
	 * 添加菜单
	 */
	public function create() {
		$this->name = trim($this->name);

		if (!$this->name) {
			$this->setErr('请输入菜单名！');
			return false;
		}
		 
		$this->url = trim($this->url);
		 
		return parent::create();
	}

	/**
	 * 根据id修改自定义菜单
	 * @param int $id
	 */
	public function updateById($id) {
		$this->setObjId($id);
		
		$this->name = trim($this->name);
		$this->url = trim($this->url);
		 
		if (!$this->name) {
			$this->setErr('请输入菜单名！');
			return false;
		}
		
		return $this->update();
	}

	/**
	 * 根据id加载菜单
	 * @param int $id
	 * @return boolean
	 */
	public function loadById($id) {
		$this->setObjId($id);
		return $this->load();
	}

	/**
	 * 根据id删除菜单
	 * @param int $id
	 * @return boolean
	 */
	public function deleteById($id) {
		// 有子菜单不允许删除
		$list = $this->getList();
		if (isset($list[$id]) && $list[$id]['chileArr']) {
			$this->setErr('该菜单下还有子菜单，不能删除！');
			return false;
		}
		
		$this->setObjId($id);
		return $this->delete();
	}

	/**
	 * 生成微信公众服务号的自定义菜单
	 */
	public function buildWXMenuByAccessToken($accessToken) {
		if(!$accessToken) {
			return false;
		}

		$list = $this->getList();
		$menu = DiyMenuEncoder::encode($list);
		
		$client = new \core\util\Client();
		 
		$client->get("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$accessToken}");
		$r = $client->post("https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$accessToken}", $menu);
		 
		$json = json_decode($r);
		if ($json->errcode != 0) {
			$this->setErr(ResponseCode::getMessage($json->errcode));
			return false;
		}

		return true;
	}

	public function updateDisplayorderById($sortValue, $id) {
		$whArr = array(
			array('id', $id),
		);
		 
		return $this->updateBy(array('displayorder' => $sortValue), $whArr);
	}
}
