<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\user\model;

use core\mvc\Model;
use core\mvc\Exception;
use core\Factory;

/**
 * 权限控制列表模型
 * 
 * wk_user_acl 只保存有权访问的信息(使用白名单)
 * 
 * @package     module.user.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class AclModel extends Model {
	/**
	 */
	protected $table = 'wk_user_acl';
	
	public function add($mod, $ctl, $act, $roleId = 0, $uid = 0) {
		$acl = array(
			'roid'   => $roleId,
			'uid'    => $uid,
			'mod'    => $mod,
			'ctl'    => $ctl,
			'act'    => $act,
		);
		
		$this->fromArray($acl);
		
		return $this->create();
	}
	
	public function create() {	
		if(!$this->act) {
			$this->setErr('功能不能为空');
			return false;
		}
		
		if(!$this->mod) {
			$this->setErr('所属模块不能为空');
			return false;
		}
		
		if(!$this->ctl) {
			$this->setErr('所属控制器不能为空');
			return false;
		}
		
		if(!$this->id && $this->uid) {
			$this->setErr('角色id或用户id都能同时空');
			return false;
		}

		$this->mod = strtolower($this->mod);
		$this->ctl = strtolower($this->ctl);
		$this->act = strtolower($this->act);
		
		$do = parent::create();
		
		self::clearCache();
		
		return $do;
	}
	
	public function update() {
		
	}
	
	/**
	 * 获取模块角色权限控制列表
	 * @param string $mod
	 */
	public function getRoleAclsByMod($mod) {
		$mod = strtolower($mod);
		
		$cdt = array(
		    'where' => array(array('mod', $mod), array('uid', 0)),
		);
		$acls = $this->select($cdt, 0, 9999);
		
		$r = array();
		foreach ($acls as $acl) {
			$r[$acl['ctl']][$acl['act']][$acl['roid']] = 1;
		}
		
		return $r;
	}
	
	/**
	 * 获取模块权限控制列表
	 * @param string $mod
	 * @return Ambigous <multitype:, number>
	 */
	public function getAclsByMod($mod) {
		$mod = strtolower($mod);
		
		$cacheKey = 'acl/getAclsByMod/' . $mod;
		$res = Factory::cache()->read($cacheKey);
		
		if (!$res) {
			$acls = $this->select(array('where' => array('mod', $mod)), 0, 9999);
			
			$res = array();
			foreach ($acls as $acl) {
				$res[$acl['ctl']][$acl['act']][$acl['roid']] = 1;
			}
			
			Factory::cache()->write($cacheKey, $res);
		}
		
		return $res;
	}
	
	/**
	 * 移除模块权限控制列表
	 *
	 * @param string $mod
	 * @throws \core\mvc\Exception
	 */
	public function removeByMod($mod) {
		return $this->deleteBy(array('mod', strtolower($mod)));
	}
	
	/**
	 * 设置模块权限列表
	 * 
	 * @param string $mod
	 * @param array $acls
	 */
	public function updateModAcl($mod, $acls) {
		$mod = strtolower($mod);
		
		// 删除模块角色权限列表，保留详细用户权限设置		
		$this->deleteBy(array(array('uid', 0), array('mod', $mod)));
		
		foreach ($acls as $ctl => $ctlAcl) {
			$array = array();
			foreach ($ctlAcl as $act => $actAcl) {
				$obj = $this;
				foreach ($actAcl as $roid => $_tmp) {
				    $obj->roid = $roid;
				    $obj->uid  = 0;
				    $obj->mod  = strtolower($mod);
				    $obj->ctl  = strtolower($ctl);
				    $obj->act  = strtolower($act);
				    
				    $obj->create();
				}
			}
			
		}
		
		Factory::db()->query("OPTIMIZE TABLE wk_user_acl");
		static::clearCache();
		
		return true;	
	}

	/**
	 * 当前访问网站的用户是否能访问功能
	 *
	 * @param string $mod
	 * @param string $ctl
	 * @param string $act
	 * @param bool $throw  是否抛出异常
	 * @return bool
	 */
	public static function isAccessable($mod, $ctl, $act, $throw = false) {
		if (empty($mod) || empty($ctl) || empty($act)) {
			throw new \core\Exception('错误的参数');
		}

		$mod = strtolower($mod);
		$ctl = strtolower($ctl);
		$act = strtolower($act);
				
		// 加载当前访问模块信息
		$modObj = new \module\system\model\ModuleModel();
		$modObj->loadModuleFromCache($mod);
	
		// 模块必须安装并且启用才能访问
		if (!$modObj->isInstalled() || !$modObj->isActivate()) {
			if ($throw) {
				throw new \core\Exception('该页面不存在！', \core\Exception::ERROR_HTTP_404);
			}
			return false;
		}
	
		// 超级管理员可以访问已安装模块所有功能
		if(\module\user\model\UserModel::isSuper($_SESSION['uid'])) {
			return true;
		}
			
		// 如果action不存在
		if (!$modObj->isActionExists($ctl, $act)) {
			if ($throw) {
				throw new Exception("Action not exists", \core\Exception::ERROR_HTTP_404);
			}
			
			return false;
		}
		
		$modAcls = static::getInstance()->getAclsByMod($mod);
		
		if (empty($modAcls[$ctl][$act])) {
			if ($throw) {
				if ($_SESSION['uid']) {
					throw new \core\Exception("你没有权限访问该页面！", \core\Exception::ERROR_HTTP_401);
				} else {
					throw new \core\Exception("请您先登录！", \core\Exception::ERROR_HTTP_403);
				}				
			}
			
			return false;
		}
		
		$actAcls = $modAcls[$ctl][$act];		
		$isAccessable = false;
		
		$role = (array)$_SESSION['role'];
	
		// 用户/会员所在用户组有权限访问
		if(array_intersect($role, array_keys($actAcls))) {
			// TODO 管理员|编辑 未审核，角色降级为普通会员
			
			// TODO 普通会员未审核，降级为游客
			$isAccessable = true;
		} else {		
			if ($throw) {
				if ($_SESSION['uid']) {
					throw new \core\Exception("你没有权限访问该页面！", \core\Exception::ERROR_HTTP_403);
				} else {
					throw new \core\Exception("请您先登录！", \core\Exception::ERROR_HTTP_401);
				}				
			}
			
			return false;
		}
	
		return $isAccessable;
	}
	
	/**
	 * 获取模块权限控制列表
	 *
	 * @param string $mod
	 */
	public function getModRolesAcls($mod) {
		$mod  = strtolower($mod);
		$acls = array();
		$cacheKey = "acl/$mod";
		if (!$acls = Factory::cache()->read($cacheKey)) {
			$res = $this->select(array('where' => array(array('mod', $mod), array('uid', 0))));
			foreach ($res as $row) {
				$acls[$row['ctl']][$row['act']][] = $row['roid'];
			}
				
			Factory::cache()->write($cacheKey, $acls);
		}
	
		return $acls;
	}

	/**
	 * 获取所有角色权限控制列表
	 *
	 * @param string $mod
	 */
	public function getRolesAcls() {
		$acls = array();
		$cacheKey = "acl/allroles";
		if (!$acls = Factory::cache()->read($cacheKey)) {
			$res = $this->select(array('where' => array('uid', 0)));
			foreach ($res as $row) {
				$acls[$row['mod']][$row['ctl']][$row['act']][] = $row['roid'];
			}
				
			Factory::cache()->write($cacheKey, $acls);
		}
	
		return $acls;
	}
	
	public static function clearCache() {
		Factory::cache()->clear('acl');	
	}
	
}
