<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\ad\controller;

use core\Factory;

use core\Common;

/**
 * 广告显示
 *
 * @package     module.ad.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
class ApiController extends \core\mvc\Controller {
	
	/**
	 * 获取广告位js数据
	 * 
	 * @param int $id
	 */
	public function getAction($id = 0) {
		$cacheKey = "ad/$id";
		if(null === $ad = Factory::cache()->read($cacheKey)) {
		    $ad = '';		
			$m = new \module\ad\model\AdPlaceModel();
			if($id && $m->setObjId($id)->load()) {
				$ad = Common::jsWrite($m->getPlaceHtml());
			}
			
			//Factory::cache()->write($cacheKey, $ad);
		}
		
		print $ad;
	}
	
}
