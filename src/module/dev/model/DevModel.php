<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\dev\model;

use core\Object;
use core\util\Validator;
use module\system\model\UIModel;
use module\system\model\ModuleModel;

/**
 * 
 * @package     module.dev.model
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class DevModel extends Object {
	public function createModule($mod) {
		foreach ($mod as $k => $v) {
			$mod[$k] = str_replace("'", "\\'", $v);
		}
		
		// 输入验证
		$rules = array(
			'dir'       => array('notEmpty' => '请输入模块文件夹'),
			'name'      => array('notEmpty' => '请输入模块名称'),
			'version'   => array('notEmpty' => '请输入模块版本号'),
			'author'    => array('notEmpty' => '请输入作者'),
			'email'     => array('email' => '请输入正确的邮箱格式'),
			'siteurl'   => array('url' => '请输入正确的模块官网网址'),
			'desc'      => array('notEmpty' => '请输入模块介绍'),
			'level'     => array('notEmpty' => '请选择模块类型'),
		);
		
		$validErrs = array();
		$valid = Validator::Validate($mod, $rules, $validErrs);
		
		$mod['dir'] = strtolower($mod['dir']);
		if (!preg_match("/^[a-z][a-z0-9]+/", $mod['dir'])) {
			$validErrs[] = '模块文件夹名错误，文件夹名只允许使用字母和数字，以字母开头。';
		}

		// 模块是否已存在
		$modPath = SRC_PATH . "module/{$mod['dir']}/";
		if (is_dir($modPath)) {
			$validErrs[] = '该模块已存在，不能重复创建。';
		}
		
		if (!is_writeable(dirname($modPath))) {
			$validErrs[] = '模块文件夹不可写，不能创建模块。';
		}
		
		if ($validErrs) {
			$this->setErr($validErrs);
			return false;
		}
		
		// 创建文件夹
		mkdir($modPath.'controller', 0755, true);
		mkdir($modPath.'model', 0755);
		mkdir($modPath.'hook', 0755);
		mkdir($modPath.'install', 0755);
		mkdir($modPath.'uninstall', 0755);
		
		$tplDir = UIModel::getCurrentTplDir();
		mkdir($tplDir . "pc/{$mod['dir']}", 0755);
		mkdir($tplDir . "mobile/{$mod['dir']}", 0755);
		mkdir($tplDir . "admincp/{$mod['dir']}", 0755);

		// 写入模块文件
		file_put_contents($modPath . "install/{$mod['dir']}.sql", '');
		file_put_contents($modPath . "uninstall/{$mod['dir']}.sql", '');
		
		// 获取模块配置信息模板
		$info = file_get_contents(dirname(__DIR__).'/tpl/info.tpl');
		$info = str_replace(
			array('{name}', '{version}', '{author}', '{email}', '{siteurl}', '{copyright}', '{desc}', '{level}'), 
			array($mod['name'], $mod['version'], $mod['author'], $mod['email'], $mod['siteurl'], $mod['copyright'], $mod['desc'], $mod['level']), 
		$info);
		file_put_contents($modPath.'info.php', $info);
		
		// cfg.php
		file_put_contents($modPath.'install/cfg.php', file_get_contents(dirname(__DIR__).'/tpl/cfg.tpl'));
		
		return true;
	}
	/**
	 * 添加控制器
	 * @param array $opt = array(
	 *   'mod' => '',
	 *   'ctl' => '',
	 *   'name' => '',
	 *   'desc' => '',
	 * )
	 * @return boolean
	 */
	public function addCtl($opt) {
		$opt['mod']  = preg_replace("/[^a-z0-9]/", '', strtolower($opt['mod']));
		$opt['ctl']  = preg_replace("/[^a-z0-9]/i", '', ucfirst($opt['ctl']));
		$opt['name'] = str_replace("*/", "* /", @$opt['name']);
		$opt['desc'] = str_replace("*/", "* /", @$opt['desc']);
		$opt['desc'] = str_replace(array("\r\n", "\n"),array("\n * ", "\n * "), $opt['desc']);
		
		$namespace = "module\\{$opt['mod']}\\controller";
		$subDir = '';
		if (!empty($opt['subdir'])) {
			$subDir = preg_replace("/(\\\\|\\.)+/", '/', $opt['subdir']);
			$subDir = trim($subDir, '/') . '/';
			$namespace .= "\\" . strtr(trim($subDir, '/'), '/', '\\');
		}
		
		$package = str_replace('\\', '.', $namespace);
		
		// 输入验证
		$rules = array(
			'mod'  => array('notEmpty' => '请选择模块'),
			'ctl'  => array('notEmpty' => '请输入控制器类名'),
		);
		
		$validErrs = array();
		$valid = Validator::Validate($opt, $rules, $validErrs);

		if ($validErrs) {
			$this->setErr($validErrs);
			return false;
		}
		
		$ctlFile = SRC_PATH . "module/{$opt['mod']}/controller/{$subDir}" . $opt['ctl'] . "Controller.php";
		if(is_file($ctlFile)) {
			$this->setErr('该控制器已存在');
			return false;
		} elseif(!is_dir(dirname($ctlFile))) {
			@mkdir(dirname($ctlFile), 0666, true);
		}
		
		if (!is_writeable(dirname($ctlFile))) {
			$validErrs[] = '控制器文件夹不可写，不能创建控制器文件。';
		}
		
		$modObj = new ModuleModel();		
		$modInfo = $modObj->setPkv($opt['mod'])->getInfo();
		
		$parent = $opt['parent'];
		
		$ctlTplName = (stripos($parent, 'AdminBase')) ? 'managectl.tpl' : 'ctl.tpl';
		
		$tpl = file_get_contents(dirname(__DIR__) . '/tpl/' . $ctlTplName);
		$tpl = str_replace(
			array('{siteurl}', '{copyright}', '{mod}', '{name}', '{desc}', '{author}', '{email}', '{class}', '{parent}', '{namespace}', '{level}'), 
			array(
			    $modInfo['siteurl'],
			    $modInfo['copyright'],
			    $opt['mod'],
			    $opt['name'],
			    $opt['desc'],
			    $modInfo['author'],
			    $modInfo['email'],
			    $opt['ctl'],
			    $parent,
				$namespace,
				$package
		    ),
			$tpl
		);
		
		if(file_put_contents($ctlFile, $tpl)) {
			return $ctlFile;
		}
		
		return false;
	}
	
	/**
	 * 添加模型
	 * @param array $opt
	 */
	public function addModel($opt) {
		$opt['mod']      = preg_replace("/[^a-z0-9]/", '', strtolower($opt['mod']));
		$opt['model']    = preg_replace("/[^a-z0-9]/i", '', ucfirst(trim($opt['model'])));
		$opt['db_table'] = preg_replace("/[^a-z0-9_]/i", '', @$opt['db_table']);
		$opt['name']     = str_replace("*/", "* /", @$opt['name']);
		$opt['desc']     = str_replace("*/", "* /", @$opt['desc']);
		$opt['desc']     = str_replace(array("\r\n", "\n"),array("\n * ", "\n * "), $opt['desc']);
		
		// 输入验证
		$rules = array(
			'mod'  => array('notEmpty' => '请选择模块'),
			'model'  => array('notEmpty' => '请输入模型类名'),
		);
		
		$validErrs = array();
		$valid = Validator::Validate($opt, $rules, $validErrs);

		if ($validErrs) {
			$this->setErr($validErrs);
			return false;
		}
		
		$modelFile = SRC_PATH . "module/{$opt['mod']}/model/" . $opt['model'] . "Model.php";
		if(is_file($modelFile)) {
			$this->setErr('该模型已存在');
			return false;
		}
		if (!is_writeable(dirname($modelFile))) {
			$validErrs[] = '模型文件夹不可写，不能创建模型文件。';
		}
		
		$modObj = new ModuleModel();		
		$modInfo = $modObj->setPkv($opt['mod'])->getInfo();
		
		$tpl = file_get_contents(dirname(__DIR__).'/tpl/model.tpl');
		$tpl = str_replace(
			array('{siteurl}', '{copyright}', '{mod}', '{name}', '{desc}', '{author}', '{email}', '{model_class}', '{db_table}'), 
			array(
			    $modInfo['siteurl'],
			    $modInfo['copyright'],
			    $opt['mod'],
			    $opt['name'],
			    $opt['desc'],
			    $modInfo['author'],
			    $modInfo['email'],
			    $opt['model'],
			    $opt['db_table'],
		    ),
			$tpl
		);
		
		file_put_contents($modelFile, $tpl);
				
		return true;		
	}
}

