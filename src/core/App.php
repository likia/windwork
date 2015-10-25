<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core;

// 系统使用UTF-8字符集
ini_set('default_charset', 'UTF-8');

/**
 * 程序源码文件夹
 * @var string
 */
define('SRC_PATH',    str_replace('\\', '/', dirname(__DIR__)) . '/');

// 源码目录设为当前工作目录
chdir(SRC_PATH);

require_once SRC_PATH.'compat.php';

// 开始记录程序运行时间、内存使用情况
benchmark();

/*
// interface and abstract
require_once SRC_PATH.'core/adapter/IFactoryAble.php';
require_once SRC_PATH.'core/adapter/db/IDB.php';
require_once SRC_PATH.'core/adapter/db/ADB.php';
require_once SRC_PATH.'core/adapter/logger/ILogger.php';
require_once SRC_PATH.'core/adapter/cache/ICache.php';
require_once SRC_PATH.'core/adapter/cache/ACache.php';
require_once SRC_PATH.'core/adapter/session/ISession.php';
require_once SRC_PATH.'core/adapter/session/ASession.php';
require_once SRC_PATH.'core/IHook.php';

require_once SRC_PATH.'core/Exception.php';
require_once SRC_PATH.'core/Config.php';
require_once SRC_PATH.'core/Object.php';
require_once SRC_PATH.'core/Hook.php';
require_once SRC_PATH.'core/Common.php';
require_once SRC_PATH.'core/Request.php';
require_once SRC_PATH.'core/Response.php';
require_once SRC_PATH.'core/Storage.php';
require_once SRC_PATH.'core/Factory.php';
require_once SRC_PATH.'core/Lang.php';
require_once SRC_PATH.'core/Router.php';

// 加载框架所耗资源
benchmark('end_require_core');
//*/

/**
 * 应用容器类
 * 
 * 负责初始化系统运行环境并执行应用程序，类自动加载实现，获取请求、响应对象，跳转分发处理用户请求等工作。
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.app.html
 * @since       1.0.0
 */
final class App {
	/**
	 * 请求对象
	 * @var \core\Request
	 */
	private $request = null;
	
	/**
	 * 响应对象
	 * @var \core\Response
	 */
	private $response = null;
		
	/**
	 * app实例
	 * @var \core\App
	 */
	private static $instance = null;
	
	/**
	 * 控制器实例
	 * @var \core\mvc\Controller
	 */
	private $ctlObj = null;
	
	/**
	 * 是否已初始化作为Web运行应用
	 * @var bool
	 */
	private $isInitWeb = false;
	
	/**
	 * 获取请求对象
	 * @return \core\Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * 获取响应对象
	 * @return \core\Response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * 加载类文件
	 * @param string $className
	 */
	public static function import($className) {
		$src = strtr($className, '\\', '//');
		$src = trim($src, '/');
		$src = SRC_PATH."{$src}.php";
		
		if(is_file($src)) {
			include_once $src;
			return true;
		} else {
			logging('error', "Class '{$className}' not found.");
			return false;
		}
	}
		
	/**
	 * 只允许单实例，从 App::getInstance()获取实例
	 */
	private function __construct() {	
	}
	
	/**
	 * 取得前端控制器实例，只允许实例化一次
	 * 
	 * @return \core\App
	 */
	public static function getInstance(array $configs = array()) {
		if (!self::$instance){
			// 自动加载类设置
			spl_autoload_register(array('\\core\\App', 'import'));
			
			// 自定义异常处理
			set_exception_handler(array('\\core\\Common', 'exceptionHandler'));
			
			// 配置信息
			Config::setConfigs($configs);
			
			// 初始化hook，加载hook配置
			Hook::init();
			
			// 创建App实例前触发的钩子，整个请求只执行一次
			Hook::call('start_new_app');
			
			self::$instance = new self();
			
			self::$instance->initRuntime();

			// 创建App实例后触发的钩子，整个请求只执行一次
			Hook::call('end_new_app');

			benchmark('end_new_instance');
		}
		
		return self::$instance;
	}
	
	/**
	 * PHP运行时设置
	 */
	private function initRuntime() {
		// 加载系统配置信息后，初始化运行时触发的钩子，目的是增加修改系统配置信息
		Hook::call('start_init_runtime');
		
		// 系统调试模式设置
		if (!Config::get('debug')) {
			// 非调试模式
			ini_set('error_reporting', E_ALL ^ (E_NOTICE | E_WARNING | E_STRICT));
			ini_set('display_errors',  0);
		} else {
			ini_set('error_reporting', E_ALL|E_STRICT);
			ini_set('display_errors',  1);
		}
		
		// 运行时设置
		ini_set('date.timezone',           'Asia/Shanghai');  // 默认时区, TODO：自动选择时区
		ini_set('error_log',               Config::get('log_dir') .'/phperror.log');
		ini_set('log_errors',              1);
		ini_set('track_errors',            1);
		ini_set('memory_limit',            '128M');
		ini_set('magic_quotes_runtime',    0);
		
		// session运行时设置
		ini_set('session.name',            'WKSID');
		ini_set('session.save_path',       SRC_PATH . 'data/session');
		ini_set('session.use_cookies',     1);
		ini_set('session.use_trans_sid',   0);
		ini_set('session.cache_expire',    180);
		ini_set('session.cookie_path',     Config::get('session_cookie_path'));
		ini_set('session.cookie_domain',   Config::get('session_cookie_domain'));
		ini_set('session.cookie_lifetime', Config::get('session_cookie_lifetime'));
	}
		
	/**
	 * 初始化实例为web开发
	 */
	public function initWeb() {
		$this->isInitWeb = true;
		
		$this->request  = new Request();
		$this->response = new Response();
		
		// Web请求路径相关配置
		Config::set('host_info',   $this->request->getHostInfo()); // 获得主机信息，包含协议信息，主机名，访问端口信息
		Config::set('base_path',   $this->request->getBasePath());
		Config::set('base_url',    Config::get('url_rewrite') ? '' : 'index.php?'); // 网站请求url查询串之前的部分
		Config::set('request_url', $this->request->getRequestUrl());
		
		// 如果系统没有安装并且不是安装页面则进入安装页面
		if (!is_file(Config::get('install_lock'))) {
			header('Location:'.Config::get('base_path').'install/');
			exit;
		}
		
		// 初始化路由设置，生成URL依赖于Config::set('host_info'),Config::set('base_path')
		\core\mvc\Router::$options = array_merge(\core\mvc\Router::$options, array_intersect_key(Config::getConfigs(), \core\mvc\Router::$options));
		
		// SESSION初始化
		\core\Factory::session()->start();
		
		// 选择客用户语言
		$locale = 'zh_CN';
 		if (!empty($_GET['locale'])) {
 			$locale = $_GET['locale'];
 		} elseif (!empty($_SESSION['locale'])) {
 			$locale = $_SESSION['locale'];
 		} else {
 			$locale = Config::get('locale'); // 在config/config.php中设置
 		}
 		
 		// 设置并加载基本语言包
		Lang::setLocale($locale);	
		Lang::add('system');
		Lang::add('user');
		
		// 输出缓冲设置
		while(!Config::get('debug') && ob_get_level()) @ob_end_clean();
		
		// 启用压缩，服务器端支持压缩并且客户端支持解压缩则启用压缩
		ob_start(Common::isGzEnabled() ? 'ob_gzhandler' : null);
		
		// 响应头信息设置
		header('Content-Type: text/html; Charset=utf-8');
		header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}
		
	/**
	 * 应用程序跳转分发
	 * 
	 * 站内页面请求转移到其它的控制器Acton（调用站内其他控制器的动作）
	 * 
	 * <pre>
	 * 使用：
	 *   $this->dispatch("$mod.$ctl.$act/$id/$other");
	 *   或
	 *   $this->dispatch(array(
	 *       'mod' => $mod,
	 *       'ctl' => $ctl,
	 *       'act' => $act,
	 *       'params' => array('a', 'b', 'c'),
	 *       'args' => array(),
	 *   ));
	 * </pre>
	 * @param string|\core\mvc\Router $routeVars
	 * @param bool $isCleanGPR 是否清空 $_GET/$_POST/$_REQUEST变量内容
	 */
	public function dispatch($routeVars = '', $isCleanGPR = false) {
		if (!$this->isInitWeb) {
			$this->initWeb();
		}
		
		$uri = !$routeVars || is_string($routeVars) ? $routeVars : $routeVars->toUrl();
		
		// 防dispatch死循环
		static $count = 0;
		$count ++;
		if ($count > 10) {
			throw new Exception("\"{$uri}\" 请求错误，dispatch死循环了！");
		}
		
		// 发送响应内容一次后结束程序
		if ($this->response->isSendedHeader()) {
			logging('error', "'{$uri}' 发生多次响应");
			return;
		}
		
		if($isCleanGPR) {
			$_GET = $_REQUEST = $_POST = array();
		}
		
		// 初始化控制器实例前触发的钩子
		Hook::call('start_new_controller');

		// 初始化控制器实例
		$this->initController($routeVars);

		// 控制器继承约束
		if(!$this->ctlObj instanceof \core\mvc\Controller) {
			throw new Exception(get_class($this->ctlObj), Exception::ERROR_CLASS_TYPE_ERROR);
		}
		
		// 初始化控制器实例后触发的钩子
		Hook::call('end_new_controller');
		
		// 执行action前触发的钩子
		Hook::call('start_action');
		
		if($this->response->isSendedHeader()) {
			logging('error', "'{$uri}' 发生多次响应！");
			return;
		}
		
		// 执行 action
		$this->ctlObj->execute($this->request->getGet('...'));

		// 执行action后触发的钩子
		Hook::call('end_action');
		
		// 响应内容未发送则发送
		if(!$this->response->isSendedHeader()) {
			// 内容输出前触发的钩子，可对输出内容进行处理过滤
			Hook::call('start_output');
			
			$this->response->setBody(ob_get_clean());
			$this->response->send();
		}

		// 程序执行完后触发的钩子
		Hook::call('end_app');
	}
	
	/**
	 * 初始化控制器实例
	 * 模块名、控制器命名空间都为小写
	 * @param string|\core\mvc\Router $pathVars
	 * @throws \core\Exception
	 */
	private function initController($pathVars) {		
		// 请求变量设置
		if($pathVars instanceof \core\mvc\Router) {
			$router = $pathVars;
		} else {
	    	$router = new \core\mvc\Router();
			if($pathVars && is_string($pathVars)) {
				$router->parseUrl($pathVars);
			} else {
				$uri = $this->request->getRequestUri();
				$router->parseUrl($uri);
				if(false !== stripos($uri, 'index.php?') && key($_GET) && !current($_GET)) {
					array_shift($_GET);
				}
			}
		}
		
		$_GET = array_merge($_GET, $router->params);

		$mod = strtolower($_GET['mod']);
		$ctl = strtolower($_GET['ctl']);
		$act = strtolower($_GET['act']);
		
		$sub = '';// 控制器子文件夹
			
		// 处理控制器子文件夹
		if (strpos($ctl, '.')) {
			$sub = substr($ctl, 0, strrpos($ctl, '.') + 1); // 后面带.
		}
		
		$ctlKey = rtrim("controllers/{$mod}.{$sub}", '.');
		if(!$modCtls = \core\Factory::cache()->read($ctlKey)) {
			if(!is_dir(SRC_PATH . "module/{$mod}")) {
				throw new Exception("\"{$mod}\"目录不存在", \core\Exception::ERROR_HTTP_404);
			}
			
			// 控制器命名空间
			$nsp = "\\module\\{$mod}\\controller\\" . str_replace('.', '\\', $sub);
						
			// 控制器目录
			$ctlDir = str_replace("\\", '/', ltrim($nsp, '\\'));			
			if(!is_dir(SRC_PATH . $ctlDir)) {
				throw new Exception("\"{$ctlDir}\"目录不存在", \core\Exception::ERROR_HTTP_404);
			}

			// 获取模块下面所有控制器类和方法
			$ctlList = scandir(SRC_PATH . $ctlDir);			
			foreach ($ctlList as $fileName) {
				if (!stripos($fileName, 'controller.php')) {
					continue;
				}
				
				// 控制器类名，不包括命名空间
				$ctlName = substr($fileName, 0, -14);				
				$ctlFile = $ctlDir . $fileName;
				
				$ctx = file_get_contents($ctlFile);
				
				// $ctl(控制器类名)大小写修正
				if (preg_match("/class\s+({$ctlName})Controller\s+extends\s+/is", $ctx, $mat)) {
					$thisCtl = "{$sub}{$mat[1]}";
				} else {
					continue;
				}
				
				// action列表
				$actList = array();
				if(preg_match_all("/public\\s+function\\s+([a-z0-9_]+)Action/is", $ctx, $mat)) {
					$actList = array_combine(array_map('strtolower', $mat[1]), $mat[1]);
				}

				$modCtls[strtolower($thisCtl)] = array(
					'mod'     => $mod,
					'ctl'     => $thisCtl,
					'actList' => $actList,
					'file'    => $ctlFile,
					'class'   => str_replace('.', '\\', "\\module\\{$mod}\\controller\\{$thisCtl}Controller"),
				);
				
				\core\Factory::cache()->write($ctlKey, $modCtls);				
			}
		}
		
		if(empty($modCtls[$ctl])) {
			throw new Exception("404警告！ 很不幸，您探索了一个未知领域！", \core\Exception::ERROR_HTTP_404);
		}
		
		$ctlMapper = $modCtls[$ctl];

		// 修正 $ctl
		$ctl = $ctlMapper['ctl'];
			
		// 修正 $act
		if(isset($ctlMapper['actList'][$act])) {
			$act = $ctlMapper['actList'][$act];
		}
			
		$_REQUEST = array_merge($_REQUEST, $_GET);
		
		// 创建控制器实例
		if(!$this->ctlObj || !($this->ctlObj instanceof $ctlMapper['class'])) {
			require_once $ctlMapper['file'];
			$this->ctlObj = new $ctlMapper['class']();
		}
	}
		
	/**
	 * 获取控制器对象实例
	 * @return \core\mvc\controller\Controller
	 */
	public function getCtlObj() {
		return $this->ctlObj;
	}
	
	/**
	 * 删除创建的实例
	 */
	public static function destroy() {
		static::$instance = null;
	}
}
