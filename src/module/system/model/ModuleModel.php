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

use core\mvc\Exception;
use module\user\model\RoleModel;
use core\Factory;
use core\util\Validator;
use core\Config;
use core\mvc\Message;
use module\user\model\AclModel;

/**
 * 模块模型
 * 
 * @package     module.system.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ModuleModel extends \core\mvc\Model {
	protected $table = 'wk_module';
	
	public function loadModuleFromCache($mod) {
		$cacheKey = 'mod/'.$mod;
		$cache = Factory::cache()->read($cacheKey);
		if($cache) {
			$this->fromArray($cache);
			return true;
		} elseif ($this->setPkv($mod)->load()) {
			Factory::cache()->write($cacheKey, $this->toArray());
			return true;
		}
		
		return false;
	}
		
	/**
	 * 安装模块
	 * 
	 * DO:
	 * 添加模块信息到表  wk_module
	 * 执行安装sql脚本
	 * 添加功能到表 wk_act
	 * 添加权限控制到表 wk_user_acl
	 * 添加后台菜单到到表 wk_menu
	 * 添加栏目可选模块到系统选项中
	 * 添加过滤器到系统选项中
	 * 将前台视图复制到模板文件夹中
	 *
	 * @param string $modId
	 * @return bool
	 */
	public function install() {
		$this->id = strtolower($this->id);
		
		$modInfo = $this->getInfo();
		$modList = $this->getInstalledMods();
		$modCfg  = $this->getInstallCfg($this->id);
		
		$actObj  = new ActModel();
		$aclObj  = new AclModel();
		$roleObj = new RoleModel();
		
		if (isset($modList[$this->id])) {
			$this->setErr('该模块（'.$this->id.'）已经安装过，不能重复安装模块。');
			return false;
		}
	
		if (isset($modInfo['err'])) {
			$this->setErr($modInfo['err']);
			return false;
		}
	
		empty($modInfo['level']) && $modInfo['level'] = 'other';
		
		$modInfo['installtime'] = time();
		$modInfo['installuid']  = $_SESSION['uid'];		
		$modInfo['activated']   = 1;
		$modInfo['id']          = $this->id;
		$modInfo['usesetting']  = !empty($modCfg['usesetting']);
		$modList[$this->id]     = $modInfo;
		
		// 读取actions文件信息
		empty($modCfg['acts'])    && $modCfg['acts']     = array();
		empty($modCfg['acls'])    && $modCfg['acls']     = array();
		
		// 把当前安装模块的信息添加到 wk_module
		$this->fromArray($modInfo);
		if(false === $this->create()) {
			return false;
		}
		
		// 执行安装sql文件
		$installSql = SRC_PATH."module/{$this->id}/install/{$this->id}.sql";
		if (is_file($installSql)) {
			$sqls = file_get_contents($installSql);			
			$sqls && self::db()->execs($sqls);
		}
		
		// 运行自定义安装脚本
		$customInstall = SRC_PATH."module/{$this->id}/install/{$this->id}.php";
		if (is_file($customInstall)) {
			include $customInstall;
		}
		
		// 将模块actions添加到actions列表中
		foreach ($modCfg['acts'] as $actCtl => $actList) {
			foreach ($actList as $actId => $actName) {
				$actObj->add($actName, $this->id, $actCtl, $actId);
			}
		}

		$roles = $roleObj->getEnabledRoles();
		
		// 将模块权限控制设置添加到权限控制列表中
		foreach ($modCfg['acls'] as $aclCtl => $actList) {		
			foreach ($actList as $actId => $aclList) {
				foreach ($roles as $roleItem) {
					if (is_string($aclList) && trim($aclList) == '*') {
						$access = 1;
					} elseif ($roleItem['type'] == 'admin') {
						$access = is_numeric($aclList) ? $aclList >= 1 : $aclList[0];
					} elseif ($roleItem['type'] == 'ext') {
						$access = is_numeric($aclList) ? $aclList >= 2 : $aclList[1];
					} elseif ($roleItem['type'] == 'member') {
						$access = is_numeric($aclList) ? $aclList >= 3 : $aclList[2];
					} else {
						$access = is_numeric($aclList) ? $aclList >= 4 : $aclList[3];
					}
					
					if(!$access) {
						continue;
					}

					// 把有权限访问的配置添加到权限控制列表
					if(false === $aclObj->add($this->id, $aclCtl, $actId, $roleItem['roid'])) {
						Message::setErr($aclObj->getErrs());
					}					
				}
				
				unset($roleItem);
			
			}
		}
		
		$this->installTpl($this->id);
		
		// 清除缓存
		Factory::cache()->clear();
	
		return true;
	}

	/**
	 * 卸载模块
	 *
	 * DO:
	 * 将模块信息移除出表  wk_module
	 * 执行卸载sql脚本
	 * 将模块功能移除出表 wk_act
	 * 将模块权限控制移除出表 wk_user_acl
	 * 将模块后台菜单移除出表 wk_menu
	 * 将栏目可选当前模块从系统选项移除
	 * 将过滤器从系统选项中移除
	 *
	 * @param string $modId
	 * @return bool
	 */
	public function uninstall() {
		$load = $this->load();
		if(!$load) {
			$this->setErr('该模块没有安装！');
			return false;
		}
		
		// 不允许卸载核心模块
		if($this->package == 'core') {
			$this->setErr('不允许卸载核心模块！');
			return false;
		}
		
		// 从wk_module 中移除
		if(false === $this->delete()) {
			return false;
		}
		
		// 从 wk_act 移除
		$act = new ActModel();
		$act->removeByMod($this->id);

		// 从 wk_user_acl 移除
		$acl = new AclModel();
		$acl->removeByMod($this->id);
		
		// 执行卸载sql脚本
		$uninstallSql = SRC_PATH."module/{$this->id}/uninstall/{$this->id}.sql";
		if (is_file($uninstallSql)) {
			$sqls = file_get_contents($uninstallSql);
			$sqls && self::db()->execs($sqls);
		}
		
		// 运行自定义卸载脚本
		$customUninstall = SRC_PATH."module/{$this->id}/uninstall/{$this->id}.php";
		if (is_file($customUninstall)) {
			include $customUninstall;
		}
		
		Factory::cache()->clear();
		
		return true;
	}
	
	/**
	 * 模块是否已启用
	 */
	public function isActivate() {
		$this->loaded || $this->load();
		
		return $this->activated;
	}
	
	/**
	 * 获取已安装的模块列表
	 * @return array
	 */
	public function getInstalledMods() {		
		if (!$modList = Factory::cache()->read('mods')) {
			$modList = array();
			$rs = $this->select(array(), 0, 999);
			if ($rs) {
				foreach ($rs as $mod) {
					$mod['installed'] = 1;
					$modList[$mod['id']] = $mod;
				}
			}
			
			Factory::cache()->write('mods', $modList);
		}
		
		return $modList;
	}
	
	/**
	 * 模块是否已经安装
	 * 
	 * @return boolean
	 */
	public function isInstalled() {
		if(!isset($this->installed)) {
			$modList = $this->getInstalledMods();
			$this->installed = array_key_exists($this->id, $modList);
		}
		
		return $this->installed;
	}

	/**
	 * 获取已安装模块权限控制列表
	 * @throws Exception
	 */
	public function getAcls() {
		$acls = array();
		if($this->isInstalled()) {
			$aclObj = new AclModel();
			$acls = $aclObj->getAclsByMod($this->id);
		}
	
		return $acls;
	}

	/**
	 * 获取模块功能列表
	 * @throws Exception
	 */
	public function getActs() {
		$acts = array();
		if($this->isInstalled()) {
			$actObj = new ActModel();
			$acts = $actObj->getActsByMod($this->id);
		} else if(false != ($cfg = $this->getInstallCfg())) {
			$acts = $cfg['acts'];
		} else {
			throw new Exception('该模块不存在');
		}
	
		return $acts;
	}
	
	/**
	 * 模块的功能是否存在
	 * 
	 * @param string $ctl
	 * @param string $act
	 * @return bool
	 */
	public function isActionExists($ctl, $act) {
		$modActs = $this->getActs();
		$ctl = strtolower($ctl);
		$act = strtolower($act);
		
		return array_key_exists($ctl, $modActs) && array_key_exists($act, $modActs[$ctl]);
	}
	
	/**
	 * 获取info.php模块说明
	 * 
	 * @param string $modId
	 * @return array
	 */
	public function getInfo() {
		$modList = $this->getInstalledMods();
		
		if (!empty($modList[$this->id])) {
			return $modList[$this->id];
		}
		
		$infoFile = SRC_PATH . "module/{$this->id}/info.php";
		if (!is_file($infoFile)) {
			return array (
				'name'       => '',
				'version'    => '<span class="red">—</span> ',
				'author'     => '<span class="red">—</span> ',
				'email'      => '',
				'siteurl'    => '',
				'copyright'  => '',
				'desc'       => "<font color=red>错误：(./src/module/{$this->id}/info.php) 模块配置信息不存在</font>",
			    'level'      => 'other',
			    'err'        => '（'.$infoFile.'）模块配置文件不存在',
			    'installed'  => 0,
			    'activated'  => 0,
			);
		} else {
			$info = include $infoFile;
			// 验证$modInfo
			if(empty($info)) {
			    $info['err'] = '模块配置信息文件中没有配置信息';
			}

		    if(empty($info['name'])) {
				$info['name'] = '<b class="red">没有配置信息名</b>';
		    }
		    
		    if(empty($info['desc'])) {
				$info['desc'] = '<span class="red">' . (empty($info['err']) ? '配置文件格式错误' : $info['err']) . '</span>';
		    }
		    
		    if (empty($info['email']) || !Validator::isEmail($info['email'])) {
	    		$info['email'] = '';
	    	} 
	    	
	    	if (empty($info['author'])) {
	    		$info['author'] = ' <span class="red">—</span> ';
	    	}
	    	
	    	if (empty($info['copyright'])) {
	    		$info['copyright'] = ' <span class="red">—</span> ';
	    	}
	    	
	    	if (empty($info['version'])) {
	    		$info['version'] = ' <span class="red">—</span> ';
	    	}
	    	
		    $info['installed'] = 0;
		    $info['activated'] = 0;
		    
			return $info;
		}
		
	}
	
	/**
	 * 将模块前台视图复制到模板目录下
	 * 
	 * @todo 前台的复制到前台文件夹，后台的复制到后台文件夹 。
	 * @todo 如果不能复制文件到模板文件夹则提示用户手动复制过去。
	 * @param string $modId
	 */
	private function installTpl() {
		$modTplDir = SRC_PATH."module/{$this->id}/template/";
		$pubTplDir = 'template/'.Config::get('ui_tpl').'/'.$this->id.'/';
		if (is_dir($modTplDir) && !is_dir($pubTplDir)) {
			mkdir($pubTplDir, 0755);
			$d = dir($modTplDir);
		    while (false !== ($entry = @$d->read())) {
		        if($entry[0] == '.' || substr($entry, -5) != '.html') continue;
		        copy($modTplDir.$entry, $pubTplDir.$entry);		        
		    }
		    @$d->close();
		}
	}

	/**
	 * 获取模块安装配置信息
	 * 
	 * @param string $modId
	 * @return array
	 */
	public function getInstallCfg() {
		$actsCfgFile = SRC_PATH."module/{$this->id}/install/cfg.php";
		if (!is_file($actsCfgFile)) {
			return array ();
		} else {
			return include $actsCfgFile;
		}
	}

	/**
	 * 启用模块
	 *
	 * @param string $appId
	 */
	public function activate() {
		if(!$this->isInstalled()) {
			$this->setErr('该模块（'.$this->id.'尚未安装。');
			return false;
		}
		
		$activate = $this->alterField(array('activated' => 1));
		if(false === $activate) {
			return false;
		}
	
		self::clearCache();
	
		return true;
	}

	/**
	 * 停用模块
	 *
	 * @param string $appId
	 * @return bool
	 */
	public function deactivate() {
		$appInfo = $this->getInfo();
	
		if(!$this->isInstalled()) {
			$this->setErr('该模块（'.$this->id.'）尚未启用，不能被停用。');
			return false;
		}
	
		// TODO不允许停用核心模块
		if ($appInfo['level'] == 'core') {
			$this->setErr('核心模块（'.$this->id.'）不能被停用。');
			return false;
		}
		
		$deactivate = $this->alterField(array('activated' => 0));
		if(false === $deactivate) {
			return false;
		}
		
		self::clearCache();
		
		return true;
	}
	
	/**
	 * 获取模块列表
	 * 
	 * 包括已安装和未安装的模块，按core(核心)、option(可选)、other(其他)分类。
	 * 
	 * @return array
	 */
	public static function getList() {
		$thisObj = new self();

		$mods = array();
		$installedMods = $thisObj->getInstalledMods();
		
		$modDir = SRC_PATH.'module';
		$d = dir($modDir);
		while (false !== ($entry = $d->read())) {
			if ($entry[0] == '.' || !is_dir($modDir.'/'.$entry)) {
				continue;
			}
		
			$modInfo = $thisObj->setPkv($entry)->getInfo();
		
			//print_r($modInfo);
			empty($modInfo['level']) && $modInfo['level'] = 'other';
			// 取得未安装模块信息
			if (empty($installedMods[$entry])) {
				$modInfo['id'] = $entry;
				$modInfo['activated'] = false;
				$modInfo['installed'] = false;
				$mods[$modInfo['level']][$entry] = $modInfo;
			} else {
				// 取得已安装的模块的信息
				$mods[$modInfo['level']][$entry] = $installedMods[$entry];
			}
			 
		}
		
		$d->close();
		
		ksort($mods);
		
		return $mods;
	}
	
	public static function clearCache() {
		Factory::cache()->delete('mods');
		Factory::cache()->clear('acl');
	}
}
