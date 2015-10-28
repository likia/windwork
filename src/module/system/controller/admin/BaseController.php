<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\controller\admin;


use core\Common;
use core\Lang;
use core\Config;

/**
 * 管理员后台控制器基类
 * 
 * @package     core.mvc
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
abstract class BaseController extends \core\mvc\Controller {
	/**
	 * 构造函数继承父类，初始化视图，加载后台语言包
	 */
	public function __construct() {
		parent::__construct();
		$this->initView();
		
		Lang::add('admin');
		Lang::add('system.option');
	}
	
	/**
	 * 视图对象
	 *
	 * @return \core\mvc\Template
	 */
	protected function initView() {
		if($this->view) {
			return $this->view;
		}
	
		parent::initView();
		$this->view->isMobileView = 0;
		$this->view
		->setCompiledDir(Config::get('tpl_compiled_dir'))
		->setCompileId('admincp')
		->setTplDir('template/default/admincp')
		->assign('uuid',        Common::guid())
		->assign('stylePath',   Config::get('static_site_url').Config::get('base_path').'theme/default/admincp/');
	
		return $this->view;
	}
}
