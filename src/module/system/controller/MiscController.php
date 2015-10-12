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

use core\Factory;

use \core\Config;

/**
 * 系统默认页面
 * 
 * @package     module.system.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
 class MiscController extends \core\mvc\Controller {
	
	public function captchaAction(){
		$entry    = $this->request->getRequest('entry');
		$fontSize = (int)$this->request->getRequest('font_size');
		
		$entry || $entry = 'sec';
		
		$capt = Factory::captcha();
		$capt->useNoise  = 0;
		$capt->useCurve  = 0;
		$capt->useImgBg  = 0;
		$capt->fontSize  = ($fontSize < 10 || $fontSize > 32) ? 14 : $fontSize;
		$capt->gradient  = 15;
		$capt->length    = 4;
		$capt->width     = Config::get('captcha_width');
		$capt->height    = Config::get('captcha_height');
		
		// 验证码等级
		$level = Config::get('captcha_level'); // 1-
		$level >= 2 && $capt->useNoise = 1;
		$level >= 3 && $capt->useImgBg = 1;
		
		($level >= 4 || $fontSize > 20) && $capt->useCurve = 1;
		
		$this->response->setResponseType('image/png');
		
		$capt->render($entry);
	}
	
	/**
	 * 
	 * @param string $uri
	 */
	public function statsAction($uri = '') {
		print \module\system\model\StatsModel::stats($uri);
	}
}
