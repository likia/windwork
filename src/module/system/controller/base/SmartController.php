<?php
/**
 * Windwork web framework
 *
 * @link        http://www.windwork.org
 * @copyright   Copyright (c) 2008-2015 Windwork Team.
 * @license     NewBSD
 */
namespace module\system\controller\base;

/**
 * 面向普通用户的手机/微信端前台控制器基类
 * 
 * @package     module.system.controller.base
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     NewBSD
 * @author      cmm <cmm@windwork.org>
 * @since       1.0
 */
abstract class SmartController extends \core\mvc\Controller {
	/**
	 * 商家信息模型，控制器实例创建时初始化
	 * @var \module\user\model\BizModel
	 */
	protected $biz;
	
	
	/**
	 * 
	 * @var \module\wx\model\SettingModel
	 */
	protected $wx;
	
	public function __construct() {
		parent::__construct();
		
		$this->initView();
		$this->view->isMobileView = 1;
	
		$buid= (int)$this->request->getRequest('buid');		
		if(!$buid) {
			// 如果pubid丢失，从来源页面获取pubid
			$referer = $this->request->getRefererUrl();
			if (preg_match("/(\/|&|\\?)buid:(\\d+)/i", $referer, $mat)) {
				$buid = $_GET['buid'] = $_REQUEST['buid'] = $mat[2];
			}
			 
			if(!$buid) {
				throw new \core\mvc\Exception('错误的请求参数(商家ID错误)', \core\Exception::ERROR_HTTP_403);
			}
		}
		
		$this->biz = new \module\user\model\BizModel();
		$this->biz->setPkv($buid);
		if(!$this->biz->load()) {
			throw new \core\mvc\Exception('商家不存在', \core\Exception::ERROR_HTTP_404);
		}
		
		$this->wx = new \module\wx\model\SettingModel();
		if(!$this->wx->setPkv($buid)->load()) {
			$this->wx = null;
		}

		$this->view->assign('buid', $buid);
	}
}

