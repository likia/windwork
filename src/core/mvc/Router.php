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

use \core\Common;

/**
 * 路由类
 * 
 * 路由的职责是一个把请求URL映射到控制器的action上的工具，以确定用户的请求该执行那个功能（或者反过来，某个功能应该用哪个URL来访问到）。
 * 路由从站内URL中解释并提取出URL的参数: 模块(module，简写mod)控制器(controller，简写ctl), 动作(action，简写act)以及其他请求参数。
 * 
 * 如果是在命令行下使用，请确定正确设置如下选项：
 * 'host_info'       => '', // http://www.yoursite.com
 * 'base_path'       => '', // /ctx/
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.router.html
 * @since       1.0.0
 */
class Router {	
	/**
	 * action 简短化映射规则
	 *
	 * @var array
	 */
	public static $rules = array(
		'register' => 'user.account.register',
		'login'    => 'user.account.login',
		'logout'   => 'user.account.logout',
		'profile'  => 'user.account.profile',
		'admin'    => 'system.admin.admincp.index',
		'secode'   => 'system.misc.captcha',
		'storage'  => 'system.uploader.load',
		'upload'   => 'system.uploader.create',
		'exhibit'  => 'ad.api.get',
	);
	
	/**
	 * 配置信息，部分框架在src/config/config.php设置，非web入口需详细设置
	 * @var array
	*/
	public static $options = array(
		// 命令行需设置固定值
		'host_info'       => '', // 如：http://www.yoursite.com
		'base_path'       => '', // 如：/ctx/

		// 完全动态设置
		'locale'          => '', // empty as zh_CN
		'url_encode'      => 0, // URL是否进行编码
		'url_full'        => 0, // 是否使用完整URL
		'url_rewrite'     => 0, // 是否使用URL重写
		'url_rewrite_ext' => '.html', // URL重写后缀
		'default_mod'     => 'system', // 默认模块
		'default_ctl'     => 'default',  // 默认控制器
		'default_act'     => 'index',  // 默认action
	);
	
	/**
	 * GET请求变量
	 * @var array
	 */
	public $params = array();
	
	/**
	 * 设置默认mca
	 */
	public function __construct() {
		$this->params['mod'] = static::$options['default_mod'];
		$this->params['ctl'] = static::$options['default_ctl'];
		$this->params['act'] = static::$options['default_act'];
		$this->params['...'] = array();
		$this->params['#']   = '';
	}
	
	/**
	 * 将路由对象转换成URL
	 * 根据路由对象的参数生成URL
	 * @param bool $fullUrl = false 是否获取完整路径
	 */
	public function toUrl($fullUrl = false) {
		$args = $this->params;

		$pathInfo = strtolower("{$args['mod']}.{$args['ctl']}.{$args['act']}/") . join('/', $args['...']);
		$pathInfo = trim($pathInfo, '/');
		
		// 去掉 $pathInfo 参数
		unset($args['...'], $args['mod'], $args['ctl'], $args['act']);

		return static::buildUrlByUriArgs($pathInfo, $args, $fullUrl);
	}

	/**
	 * 分析站内URL
	 * 
	 * 取得链接应该映射到哪个模块、控制器、动作，并且传哪些参数到动作方法中。
	 * 
	 * @param string $uri
	 * @return \core\mvc\Router
	 */
	public function parseUrl($uri) {
		$opts = &static::$options;
		
		// 取得index.php?及后面部分
		$uri = preg_replace("/((http|https)\\:\\/\\/.*?\\/)/", '', $uri);
		$uri = Common::ltrimStr(trim($uri, '/'), trim($opts['base_path'], '/'));  // 去掉站点的子文件夹名. TODO解决模块名跟上下文有一样时
		$uri = trim($uri, '/');
		
		// 去掉index.php?
		$uri = Common::ltrimStr($uri, 'index.php');
		$uri = trim($uri, './?');

		// 提取锚
		if(false !== $pos = strpos($uri, '#')) {
			$this->params['#'] = substr($uri, $pos + 1);
			$uri = substr($uri, 0, $pos);
		}
		
		// 查询字符串变量（URL中的常规$_GET变量）
		$args = array(); 

		if($uri){	
			$uri = str_replace('?', '&', $uri);
			// 提取常规查询串参数
			if (false !== $pos = strpos($uri, '&')) {
				parse_str(substr($uri, $pos + 1), $args);
				$uri = substr($uri, 0, $pos);
			}
			
			// 去掉伪静态后缀
			if($opts['url_rewrite_ext']) {
				$uri = Common::rtrimStr($uri, $opts['url_rewrite_ext']);
			}
			
		    // 解码
			if ($opts['url_encode'] && preg_match("/q_(.+)/", $uri, $mat)) {
				$uri = base64_decode(strtr($mat[1], '-_', '+/'));
			}
				
			// 简短url还原
			$mapKey = preg_replace("/\/.*/", '', $uri);
			if(array_key_exists($mapKey, static::$rules)) {
			    $uri = static::$rules[$mapKey] . substr($uri, strlen($mapKey));
			    $uri = rtrim($uri, '/');
			}
			
			$arr = explode("/", $uri);
			
			// 提取mod/ctl/act
			if (!preg_match("/[^a-z0-9_\\.]+/i", $arr[0])) {
				$mcaArr = explode('.', $arr[0]);
				
				// 取最后一个点后面的action名
				if(isset($mcaArr[2])) {
					$this->params['act'] = array_pop($mcaArr); 
				}
				
				// 取得第一个点前面的模块名
				$this->params['mod'] = array_shift($mcaArr);
				
				// 取得第一个点和最后一个点之间是控制器类识别
				if ($mcaArr) {
					$this->params['ctl'] = join('.', $mcaArr); 
				}
			    
				unset($arr[0]);
			}
			
			// 请求参数
			foreach ($arr as $val) {
				if(false !== strpos($val, ":")) {
				    list($name, $value) = explode(":", $val);
				    $this->params[$name] = $value;					
			    } else {
			    	$this->params['...'][] = $val;
			    }
	    	}
		}

		// 常规请求变量(?$key=$val&$key=$val)加到$this->params尾部，有重复则覆盖
		$args && $this->params = array_merge($this->params, $args);
		
		return $this;
	}

	/**
	 * 生成URL
	 * @param string|\core\mvc\Router $uri
	 * @param bool $fullUrl = false 是否生成完整URL
	 * @return string
	 */
	public static function buildUrl($uri, $fullUrl = false) {
		return static::buildUrlByUriArgs($uri, array(), $fullUrl);
	}

	/**
	 * 生成URL
	 * @param string|\core\mvc\Router $uri
	 * @param array $args = array() 添加URL参数
	 * @param bool $fullUrl = false 是否生成完整URL
	 * @return string
	 */
	private static function buildUrlByUriArgs($uri, array $args = array(), $fullUrl = false) {
		if ($uri instanceof \core\mvc\Router) {
			$obj = &$uri;
			if($args) {
				$obj->params = array_merge($obj->params, $args);
			}
			
			return $obj->toUrl($fullUrl);
		}
		
		// URL缓存
		static $urlList = array();
		$listKey = md5("{$uri}-{$fullUrl}-" . serialize($args) . "-" . serialize(static::$options));
		if(isset($urlList[$listKey])) {
			return $urlList[$listKey];
		}
		
		$url = trim($uri, '/');
		
		$anchor = ''; // 锚点

		// 提取锚，把url中的锚去掉，构造好url后再添加上
		if(false !== $pos = strpos($url, '#')) {
			$anchor = substr($url, $pos);
			$url = substr($url, 0, $pos);
		}
		
		// 提取查询串参数
		if(false !== $pos = strpos($url, '?')) {
			parse_str(substr($url, $pos + 1), $queryArgs);
			$args = array_merge($args, $queryArgs);
			$url = substr($url, 0, $pos);
		}
		
		// 添加参数到URL
		if ($args) {
			$argStr = '';
			foreach ($args as $name => $value) {
				if ($name == '...') {
					$value && $url .= '/' . implode('/', $value);
				} elseif ($name == '#') {
					$value && $anchor = '#'.$value;
				} else {
					$argStr .= "/" . urlencode(urlencode($name)) . ":" . urlencode(urlencode($value));
				}
			}
			
			$argStr && $url .= $argStr;
		}

		// URL简短化
		foreach (static::$rules as $shortTag => $mapUri) {
			if ($url == $mapUri) {
				$url = $shortTag;
				break;
			} elseif (false !== stripos($url, $mapUri . '/') && 0 == stripos($url, $mapUri . '/')) {
				$url = $shortTag . substr($url, strlen($mapUri));
				break;
			}
		}
		
		// 本地化语言变量/locale:zh_CN|zh_TW|en_US|en_UK...，默认不加上则使用配置文件的语言
		if (static::$options['locale'] && false === strpos($url, '/locale:') && \core\Lang::getLocale() !== \core\Config::get('locale') && false === strpos($url, '&locale=')) {
			$url .= '/locale:'.static::$options['locale'];
		}
		
		// 对URL进行编码
		if (static::$options['url_encode']) {
			$url = 'q_' . strtr(base64_encode($url), '+/', '-_');
			$url = rtrim($url, '=');
		}
		
		// 加上伪静态后缀（不论是否启用URL Rewrite）
		$url .= static::$options['url_rewrite_ext'];
		
		// 还原锚
		$url .= $anchor;
		
		
		// 补充base url
		if(!static::$options['url_rewrite']) {
			$url = 'index.php?' . $url;
		}
		
		// 带域名的完整URL
		if ($fullUrl || static::$options['url_full']) {
			$url = rtrim(static::$options['base_path'], '/') . '/' . trim($url, '/');
			$url = rtrim(static::$options['host_info'], '/') . '/' . trim($url, '/');
		}
		
		$urlList[$listKey] = $url;
		
		return $url;
	}
}
