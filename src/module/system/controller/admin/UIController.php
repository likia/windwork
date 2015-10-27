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


use core\Config;
use module\system\model\UIModel;
use core\mvc\Message;
use module\system\model\OptionModel;

/**
 * 前台模板和主题设置
 *
 * @package     module.system.controller.admin
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class UIController extends \module\system\controller\admin\BaseController {
	public function __construct() {
		parent::__construct();
		$this->initView();
	}
	/**
	 * 设置样式
	 * 
	 * @param string $style
	 * @return boolean
	 */
	public function setStyleAction($tpl = '', $style = '') {
		if(empty($tpl) || empty($style)) {
			print 'Error Param!';
			return false;
		}

		OptionModel::alterValue('ui_tpl', $tpl);
		OptionModel::alterValue('ui_theme', $style);
		
		Config::set('ui_theme', $style);
		
		OptionModel::clearCache();
		
		Message::setOK('选择主题成功！');
	
		$this->app->dispatch('system.admin.ui.list');
	}
	
	/**
	 * 列出所有模板下的主题，点击后自动切换模板和主题
	 */
	public function listAction() {
		$styleList = UIModel::getStyles();

		$this->view->assign('styleId',   Config::get('ui_theme'));
		$this->view->assign('styleList', $styleList);
		$this->view->render();
	}
	
}