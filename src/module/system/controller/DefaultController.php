<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\controller;

use core\Common;
	
/**
 * 系统默认页面
 * 
 * @package     module.system.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
 class DefaultController extends \core\mvc\Controller {
 	public function __construct() {
 		parent::__construct();
 		$this->initView();
 		
 		if (Common::checkMobile() || $this->request->getGet('ismobile')) {
 			$this->view->isMobileView = 1;
 		}
 	}
	
	public function indexAction(){
		
		$this->view->render();
	}
	
}
