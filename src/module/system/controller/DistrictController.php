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
 * 地区控制器
 *
 * 
 * 
 * @package     module.system.controller
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @author      cmm <cmm@windwork.org>
 */
class DistrictController extends \core\mvc\Controller {
	/**
	 * 
	 * @var \module\system\model\DistrictModel
	 */
	private $district;
	
    public function __construct() {
        parent::__construct();
        $this->district = new \module\system\model\DistrictModel();
    }
    
    /**
     * 
     */
    public function getListByUpidAction($upid = 0) {
    	$upid = (int)$upid;
    	$list = $this->district->getListByUpid($upid);
    	Common::showJson($list);
    }
}
