<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\mvc;

use core\Config;
use core\Common;
use core\mvc\Message;
use core\util\UserAgent;

/**
 * 控制器基础类 
 * 
 * @package     core.mvc
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.mvc.controller.html
 * @since       1.0.0
 */
abstract class Controller {
	/**
	 * App对象实例
	 * @var \core\App
	 */
	protected $app   = null;
	
	/**
	 * 当前访问的模块
	 * @var string
	 */
	protected $mod   = null;
	
	/**
	 * 当前访问的控制器
	 * @var string
	 */
	protected $ctl   = null;

	/**
	 * 当前访问的功能
	 * @var string
	 */
	protected $act   = null;
	
	/**
	 * 是否已初始化
	 * @var bool
	 */
	protected $inited = false;
	
	/**
	 * 视图实例，需要手动初始化 $this->initView();
	 * @var \core\mvc\Template
	 */
	protected $view  = null;
	
	/**
	 * @var \core\Request
	 */
	protected $request = null;

	/**
	 * @var \core\Response
	 */
	protected $response = null;
		
	/**
	 * 初始化视图对象
	 *
	 * @var \core\mvc\Template
	 */
	protected function initView() {
		if ($this->view) {
			return $this->view;
		}
		
		$basePath  = Config::get('base_path');
		$staticSiteUrl = Config::get('static_site_url'); // 静态文件域名，可静态服务器分离
		$staticSiteUrl || $staticSiteUrl = rtrim($this->request->getHostInfo(), '/');
		
		if (!empty($_REQUEST['ui_tpl'])) {
			$_SESSION['tpl'] = htmlspecialchars($_REQUEST['ui_tpl']);
		}
		
		// UI
		$theme = empty($_SESSION['theme']) ? Config::get('ui_theme') : $_SESSION['theme'];
		$tpl   = empty($_SESSION['tpl']) ? Config::get('ui_tpl') : $_SESSION['tpl'];

		$this->view = new \core\mvc\Template();
				
		$this->view
		  // 设置模板配置参数
		  ->setForceCompile(Config::get('tpl_compiled_force'))
		  ->setMergeCompile(Config::get('tpl_compiled_merge'))
		  ->setCompiledDir(Config::get('tpl_compiled_dir'))
		  ->setCompileId(\core\Lang::getLocale() . '^' . $tpl)
		  ->setTplDir('template/' . $tpl)
		  // 初始化模板变量
		  ->assign('mod',         strtolower($this->mod))
		  ->assign('ctl',         strtolower($this->ctl))
		  ->assign('act',         strtolower($this->act))
		  ->assign('pageId',      strtolower(str_replace('.', '-', "{$this->mod}-{$this->ctl}-{$this->act}")))
		  ->assign('tpl',         $tpl)
		  ->assign('hash',        csrfToken())
		  ->assign('logo',        Config::get('ui_logo'))
		  ->assign('nopic',       Config::get('ui_nopic'))
		  ->assign('referer',     $this->request->getRefererUrl())
		  ->assign('appUrl',      $this->request->getHostInfo() . ltrim($this->request->getBasePath(), '/'))
		  ->assign('domain',      Config::get('host_info'))
		  ->assign('storageSite', Config::get('storage_site_url'))
		  ->assign('basePath',    $basePath)
		  ->assign('baseUrl',     Config::get('base_url'))
		  ->assign('pageUrl',     $this->request->getRequestUrl())
		  ->assign('staticSite',  $staticSiteUrl) // 静态文件网站网址（http://xx.xx.com/）
		  ->assign('staticPath',  $staticSiteUrl . $basePath.'static/')  // 静态文件访问路径
		  ->assign('stylePath',   $staticSiteUrl . "{$basePath}theme/{$theme}/")
		  ->assign('siteName',    Config::get('site_name')) // 网站名称
		  ->assign('slogan',      Config::get('site_slogan')) // 
		  ->assign('seo_title',   Config::get('seo_title')) // 
		  ->assign('welcomeWord', Config::get('site_welcome')) //
		  ->assign('keyword',     Config::get('site_keyword'))
		  ->assign('description', Config::get('site_description'))
		  ->assign('siteDesc',    Config::get('site_description'))
		  ->assign('captchaOpt',  Config::get('captcha_enabled_opt'))
		  ->assign('embedHeader', Config::get('embed_header'))
		  ->assign('isAjax',      $this->request->isAjaxRequest())
		  ->assign('embedFooter', Config::get('embed_footer'));

		// 手机参数
		$this->view->assign('mlogo', Config::get('ui_mlogo')); // 手机版LOGO
		
		return $this->view;
	}
	
	/**
	 * 控制器构造函数
	 * 设置app、request、response、mod、ctl、act、view属性
	 */
	public function __construct() {
		$app    = \core\App::getInstance();

		$this->app      = $app;
		$this->request  = $app->getRequest();
		$this->response = $app->getResponse();

		$this->mod = &$_GET['mod'];
		$this->ctl = &$_GET['ctl'];
		$this->act = &$_GET['act'];

		$this->inited = true;
		
		//$this->initView();
	}
	
	
	/**
	 * 执行控制器方法
	 * 
	 * @param array $params
	 * @throws \core\mvc\Exception
	 */
	public function execute(array $params) {
		if (!$this->inited) {
			throw new Exception('请在'.get_called_class().'::__construct()调用parent::__construct()');
		}
		
		$action = $this->request->getGet('act') . 'Action';
		
		if(!method_exists($this, $action)) {
			throw new Exception('Page (' . $this->request->getRequestUrl() . ') not found', \core\Exception::ERROR_HTTP_404);
		}
		
		// 执行方法
		//call_user_func_array(array($this, $action), $params);
		$method = new \ReflectionMethod($this, $action);
		if (!$method->isPublic()){
			// note：ReflectionException为“Fatal error”，不可捕获，不在这里抛出，也不直接在上一级异常中捕获而不用这个异常抛出
			// 否则不能在上一级异常处理中捕获，而直接到顶级异常处理中
			throw new Exception('Page (' . $this->request->getRequestUrl() . ') not found', \core\Exception::ERROR_HTTP_404);
		}
		
	    $method->invokeArgs($this, $params); // 在这里加断点，下一步将进入当前请求的控制器业务逻辑中
	}
	
	/**
	 * 请求的方法不存在是显示404错误
	 * 
	 * @param string $fun
	 * @param mixed $param
	 */
	public function __call($fun, $param) {
		throw new Exception('[' . get_class($this) . "::{$fun}()]", \core\Exception::ERROR_CLASS_METHOD_NOT_EXIST);
	}
	
	/**
	 * 404（页面不存在）错误提示
	 *
	 */
	protected function err404() {
		if (!Message::hasErr()) {
			Message::setErr('404警告！ 很不幸，您探索了一个未知领域！', 404);
		}
		
		if($this->request->isAjaxRequest()) {
			$this->showMessage();
		} else {
			$this->initView()
			->assign('title', '该页面不存在！')
			->render('404.html');
		}
	}
	
	/**
	 * 403（无权访问）错误提示
	 *
	 */
	protected function err403() {
		if (!Message::hasErr()) {
		    Message::setErr('错误：您无权访问该页面！', 403);			
		}
		
		if($this->request->isAjaxRequest()) {
			$this->showMessage();
		} else {
			$this->initView()
			->assign('title', '禁止访问！')
			->render('403.html');
		}
	}
	
	/**
	 * 401（需权限认证）错误提示
	 *
	 */
	protected function err401() {
		$forward = $this->request->getGet('forward');
		
		if($this->request->isAjaxRequest()) {
		    $forward || $forward = $this->request->getRefererUrl();
		    $loginUrl = url("user.account.login/forward:" . paramEncode($forward));
			Message::setErr('<a href="' . $loginUrl . '">马上去登录</a>');
			$this->showMessage();
		} else {
			$forward || $forward = $this->request->getRequestUrl();
		    $loginUrl = url("user.account.login/forward:" . paramEncode($forward));
			$this->app->dispatch($loginUrl);
		}
	}
	
	/**
	 * 显示提示信息
	 * 
	 * @param string|array $message 提示信息内容
	 */
	public function showMessage($message = '', $mod = NULL) {				
		if($this->request->isAjaxRequest()) {
			Common::showJson(array(
			    'err'     => Message::getErrs(), 
			    'ok'      => Message::getOKs(),
			    'warn'    => Message::getWarns(),
			    'message' => $message,
			));
		} else {
			$this->initView()
			->assign('message', $message)
			->assign('title', '提示信息')
			->render(($mod ? $mod . '/' : '') . 'message.html');
		}
	}
	
	/**
	 * 错误页面
	 * @param int $code
	 */
	public function errorAction($code = 200) {
		if (!$this->request->isAjaxRequest()) {
			$this->response->setStatus($code);
		}
		
		if (UserAgent::checkMobile()) {
			$this->initView();
			$this->view->isMobileView = true;
		}
		
		switch ($code) {
			case '401':
				$this->err401();
				break;
			case '403':
				$this->err403();
				break;
			case '404':
				$this->err404();
				break;
			default:
				$this->showMessage();
				break;
		}
		
	}
	
	/**
	 * Controller::showMessage() 的别名
	 */
	public function messageAction(){
		$this->showMessage(); 
	}
}

