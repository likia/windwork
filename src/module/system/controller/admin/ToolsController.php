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

use core\mvc\Message;
use core\Factory;

/**
 * 前台模板和主题设置
 *
 * @package     module.system.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ToolsController extends \module\system\controller\base\AdminController {
	public function __construct() {
		parent::__construct();		
		$this->initView();
	}
	
	/**
	 * 清除缓存
	 */
	public function clearcacheAction() {
		if($this->request->isPost()) {
			$type = $this->request->getRequest('type');
			if (!$type) {
				Message::setErr('请选择要清除的缓存！');
			} else {
				empty($type['data']) || Factory::cache()->clear();
				empty($type['thumb']) || Factory::storage()->clearThumb();
				
				$compiledDir = $this->view->compiledDir;
				if(!empty($type['tpl']) && is_writeable($compiledDir)) {
					\core\File::clearDir($compiledDir);
				}
				
				Message::setOK('清除缓存成功！');
			}
			$this->showMessage();
		} else {
			$this->view->render();
		}
	}
	
}