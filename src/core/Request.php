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

/**
 * 客户端向服务器发出请求类
 * 
 * 客户端请求相关信息，包括用户提交的信息以及客户端的一些信息。
 *
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.request.html
 * @since       1.0.0
 */
class Request {
	/**
	 * 访问的端口号
	 *
	 * @var int
	 */
	protected $port = null;
	
	/**
	 * 请求路径信息
	 *
	 * @var string
	 */
	protected $hostInfo = null;
	
	/**
	 * 客户端IP
	 *
	 * @var string
	 */
	protected $clientIp = null;
	
	/**
	 * 语言
	 *
	 * @var string
	 */
	protected $language = null;
		
	/**
	 * 请求参数信息
	 *
	 * @var array
	 */
	protected $attribute = array();
	
	/**
	 * 请求脚本url
	 * 
	 * @var string
	 */
	private $scriptUrl = null;
	
	/**
	 * 请求参数uri
	 * 
	 * @var string
	 */
	private $requestUri = null;
	
	/**
	 * 基础路径信息
	 * 
	 * @var string
	 */
	private $baseUrl = null;

	/**
	 * 初始化Request对象
	 *
	 */
	public function __construct() {
		$this->normalizeRequest();
	}

	/**
	 * 初始化request对象
	 *
	 * 对输入参数 magic quotes处理
	 */
	protected function normalizeRequest() {
		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			empty($_GET)     || $_GET     = array_map(array('\core\Request', 'unquotesGpc'), $_GET);
			empty($_POST)    || $_POST    = array_map(array('\core\Request', 'unquotesGpc'), $_POST);
			empty($_REQUEST) || $_REQUEST = array_map(array('\core\Request', 'unquotesGpc'), $_REQUEST);
			empty($_COOKIE)  || $_COOKIE  = array_map(array('\core\Request', 'unquotesGpc'), $_COOKIE);
		}
	}

	/**
	 * 获得用户请求的数据
	 * 
	 * 返回$_GET,$_POST的值,未设置则返回$defaultValue
	 * @param string $key 获取的参数name,默认为null将获得$_GET和$_POST两个数组的所有值
	 * @param mixed $defaultValue 当获取值失败的时候返回缺省值,默认值为null
	 * @return mixed
	 */
	public function getRequest($key = null, $defaultValue = null) {
		if (!$key) {
			return array_merge($_POST, $_GET);
		}
		
		if (isset($_GET[$key])) {
			return $_GET[$key];
		}
		
		if (isset($_POST[$key])) {
			return $_POST[$key];
		}
		
		if (isset($_REQUEST[$key])) {
			return $_REQUEST[$key];
		}
		
		if (isset($_COOKIE[$key])) {
			return $_COOKIE[$key];
		}
		
		return $defaultValue;
	}

	/**
	 * 获取请求的表单数据
	 * 
	 * 从$_POST获得值
	 * @param string $name 获取的变量名,默认为null,当为null的时候返回$_POST数组
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认为null
	 * @return mixed
	 */
	public function getPost($name = null, $defaultValue = null) {
		if ($name === null) {
			return $_POST;
		}
		
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}

	/**
	 * 获得$_GET值
	 * 
	 * @param string $name 待获取的变量名,默认为空字串,当该值为null的时候将返回$_GET数组
	 * @param string $defaultValue 当获取的变量不存在的时候返回该缺省值,默认值为null
	 * @return mixed
	 */
	public function getGet($name = null, $defaultValue = null) {
		if ($name === null) {
			return $_GET;
		}
		return (isset($_GET[$name])) ? $_GET[$name] : $defaultValue;
	}

	/**
	 * 返回cookie的值
	 * 
	 * 如果$name=null则返回所有Cookie值
	 * @param string $name 获取的变量名,如果该值为null则返回$_COOKIE数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public function getCookie($name = null, $defaultValue = null) {
		if ($name === null) {
			return $_COOKIE;
		}
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $defaultValue;
	}

	/**
	 * 返回session的值
	 * 
	 * 如果$name=null则返回所有SESSION值
	 * @param string $name 获取的变量名,如果该值为null则返回$_SESSION数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public function getSession($name = null, $defaultValue = null) {
		if ($name === null) {
			return $_SESSION;
		}
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : $defaultValue;
	}

	/**
	 * 返回Server的值
	 * 
	 * 如果$name为空则返回所有Server的值
	 * @param string $name 获取的变量名,如果该值为null则返回$_SERVER数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public function getServer($name = null, $defaultValue = null) {
		if ($name === null) {
			return $_SERVER;
		}
		
		$value = (isset($_SERVER[$name])) ? $_SERVER[$name] : $defaultValue;		
		return $value;
	}

	/**
	 * 返回ENV的值
	 * 
	 * 如果$name为null则返回所有$_ENV的值
	 * @param string $name 获取的变量名,如果该值为null则返回$_ENV数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public function getEnv($name = null, $defaultValue = null) {
		if ($name === null) {
			return $_ENV;
		}
		return (isset($_ENV[$name])) ? $_ENV[$name] : $defaultValue;
	}

	/**
	 * 获取请求链接协议
	 * 
	 * 如果是安全链接请求则返回https否则返回http
	 * @return string 
	 */
	public function getScheme() {
		return ($this->getServer('HTTPS') == 'on') ? 'https' : 'http';
	}

	/**
	 * 返回请求页面时通信协议的名称和版本
	 * 
	 * @return string
	 */
	public function getProtocol() {
		return $this->getServer('SERVER_PROTOCOL', 'HTTP/1.0');
	}

	/**
	 * 获得请求的方法
	 * 
	 * 将返回POST\GET\DELETE等HTTP请求方式
	 * @return string 
	 */
	public function getMethod() {
		return strtoupper($this->getServer('REQUEST_METHOD'));
	}

	/**
	 * 获得请求类型
	 * 
	 * 如果是web请求将返回web
	 * @return string  
	 */
	public function getRequestType() {
		return PHP_SAPI == 'cli' ? 'cli' : 'web';
	}

	/**
	 * 判断该请求是否是AJAX请求
	 * 
	 * @return bool
	 */
	public function isAjaxRequest() {
		return (
			(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
		    || !empty($_GET['ajax']) 
		    || !empty($_GET['ajax_callback'])
		    || !empty($_GET['iframe_callback'])
		);
	}
	
    /**
     * 是否是XMLHttpRequest请求
     *
     * @return bool
     */
    public function isXmlHttpRequest() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }
    
    /**
     * 是否是Flash客户端请求
     *
     * @return bool
     */
    public function isFlashRequest() {
        $agent = $this->getUserAgent();
        return $agent && false !== stripos($agent, ' flash');
    }

	/**
	 * 请求是否使用的是HTTPS安全链接
	 * 
	 * 如果是安全请求则返回true否则返回false
	 * @return bool
	 */
	public function isSecure() {
		return !strcasecmp($this->getServer('HTTPS'), 'on');
	}

	/**
	 * 返回请求是否为GET请求类型
	 * 
	 * 如果请求是GET方式请求则返回true，否则返回false
	 * @return bool 
	 */
	public function isGet() {
		return !strcasecmp($this->getMethod(), 'GET');
	}

	/**
	 * 返回请求是否为POST请求类型
	 * 
	 * 如果请求是POST方式请求则返回true,否则返回false
	 * 
	 * @return bool
	 */
	public function isPost() {
		return !strcasecmp($this->getMethod(), 'POST');
	}

	/**
	 * 返回请求是否为PUT请求类型
	 * 
	 * 如果请求是PUT方式请求则返回true,否则返回false
	 * 
	 * @return bool
	 */
	public function isPut() {
		return !strcasecmp($this->getMethod(), 'PUT');
	}

	/**
	 * 返回请求是否为DELETE请求类型
	 * 
	 * 如果请求是DELETE方式请求则返回true,否则返回false
	 * 
	 * @return bool
	 */
	public function isDelete() {
		return !strcasecmp($this->getMethod(), 'Delete');
	}

	/**
	 * 初始化请求的资源标识符
	 * 
	 * 这里的uri是去除协议名、主机名的
	 * <pre>Example:
	 * 请求： http://www.windwork.org/demo/index.php?mod=xx
	 * 则返回: /demo/index.php?mod=xx
	 * </pre>
	 * 
	 * @return string 
	 * @throws Exception 当获取失败的时候抛出异常
	 */
	public function getRequestUri() {
		if (!$this->requestUri) {
			$queryString = (PHP_SAPI == 'cli' && isset($_SERVER['argv'][1])) ? $_SERVER['argv'][1] : $this->getServer('QUERY_STRING');
			if (!$this->getServer('REQUEST_URI')) {
				$_SERVER['REQUEST_URI'] = $this->getScriptUrl() . ($queryString ? '?'  : '') . $queryString;
			} else if (strpos($this->getServer('REQUEST_URI'), '?') === false && $queryString) {
				$_SERVER['REQUEST_URI'] .= '?' . $queryString;
			}
			
			$this->requestUri = $_SERVER['REQUEST_URI'];	
		}
		
		return $this->requestUri;
	}
	
	/**
	 * 获取当前请求URL
	 * @return string
	 */
	public function getRequestUrl() {
		$url = rtrim($this->getHostInfo(), '/') . $this->getRequestUri();
		return $url;
	}

	/**
	 * 返回当前执行脚本的绝对路径
	 * 
	 * <pre>Example:
	 * 请求: http://www.windwork.org/demo/index.php?mod=xx
	 * 返回: /demo/index.php
	 * </pre>
	 * 
	 * @return string
	 */
	public function getScriptUrl() {
		if (!$this->scriptUrl) {	
			$scriptName = 'index.php';
			if(false != $scriptUrl = $this->getServer('PHP_SELF')) {
				$scriptName = $this->getServer('SCRIPT_FILENAME');
				if($scriptName && $scriptName == $this->getServer('PHP_SELF')) {
					$docRoot = $this->getServer('DOCUMENT_ROOT');
					if($docRoot && strpos($scriptName, $docRoot) === 0) {
						$scriptUrl = str_replace('\\','/', str_replace($docRoot, '', $scriptName));
					} else {
						$scriptUrl = '/'.basename($scriptName);
					}
				} elseif ($scriptName && strlen($scriptName) < strlen($this->getServer('PHP_SELF'))) {
					$scriptUrl = '/'.basename($scriptName);
				}
			} else {
				$scriptUrl = '/'.basename($scriptName);
			}
			
			$this->scriptUrl = htmlentities($scriptUrl);
		}
		
		return $this->scriptUrl;
	}
	
	/**
	 * 返回系统所在的文件夹相对于根目录的路径
	 * 
	 * <pre>
	 * Example:
	 * 请求: http://www.windwork.org/demo/index.php?mod=xx
	 * 返回: /demo/
	 * </pre>
	 * 
	 * @return string
	 */
	public function getBasePath() {
		return str_replace($this->getScript(), '', $this->getScriptUrl());
	}

	/**
	 * 返回执行脚本名称
	 * 
	 * <pre>
	 * Example:
	 * 请求: http://www.windwork.org/demo/index.php?mod=xx
	 * 返回: index.php
	 * </pre>
	 * 
	 * @return string
	 * @throws Exception 当获取失败的时候抛出异常
	 */
	public function getScript() {
		if (($pos = strrpos($this->getScriptUrl(), '/')) === false) {
			$pos = -1;
		}
		return substr($this->getScriptUrl(), $pos + 1);
	}

	/**
	 * 获取Http头信息
	 * 
	 * @param string $header 头部名称
	 * @param string $default 获取失败将返回该值,默认为null
	 * @return string
	 */
	public function getHeader($header, $default = null) {
		$name = strtoupper(str_replace('-', '_', $header));
		if (substr($name, 0, 5) != 'HTTP_') {
			$name = 'HTTP_' . $name;
		}
		
		if (($header = $this->getServer($name)) != null) {
			return $header;
		}
		
		return $default;
	}

	/**
	 * 获取基础URL
	 * 
	 * 这里是去除了脚本文件以及访问参数信息的URL地址信息:
	 * 
	 * <pre>Example:
	 * 请求: http://www.windwork.org/demo/index.php?mod=xx 
	 * 1.如果: $absolute = false：
	 * 返回： demo    
	 * 2.如果: $absolute = true:
	 * 返回： http://www.windwork.org/demo
	 * </pre>
	 * @param bool $absolute 是否返回主机信息
	 * @return string
	 * @throws Exception 当返回信息失败的时候抛出异常
	 */
	public function getBaseUrl($absolute = false) {
		if ($this->baseUrl === null) {
			$this->baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/.');
		}
		
		return $absolute ? $this->getHostInfo() . $this->baseUrl : $this->baseUrl;
	}

	/**
	 * 获得主机信息，包含协议信息，主机名，访问端口信息
	 * 
	 * <pre>Example:
	 * 请求: http://www.windwork.org/demo/index.php?mod=xx
	 * 返回： http://www.windwork.org/
	 * 
	 * 请求: http://www.windwork.org:8080/demo/index.php?mod=xx
	 * 返回： http://www.windwork.org:8080/
	 * </pre>
	 * @return string
	 * @throws Exception 获取主机信息失败的时候抛出异常
	 */
	public function getHostInfo() {
		if ($this->hostInfo === null) {		
			$http = $this->isSecure() ? 'https' : 'http';
			if (($httpHost = $this->getServer('HTTP_HOST')) != null) {
				$this->hostInfo = "{$http}://{$httpHost}/";
			} elseif (($httpHost = $this->getServer('SERVER_NAME')) != null) {
				$this->hostInfo = "{$http}://{$httpHost}";
				if (($port = $this->getServerPort()) != null) {
					$this->hostInfo .= ':' . $port;
				}
				$this->hostInfo .= '/';
			} elseif (isset($_SERVER['argc'])) {
				$this->hostInfo = '';
			} else {
				$this->hostInfo = $http . "://localhost/";
				//throw new Exception('Determine the entry host info failed!!');
			}
		}
		
		return $this->hostInfo;
	}

	/**
	 * 返回当前运行脚本所在的服务器的主机名。
	 * 
	 * 如果脚本运行于虚拟主机中
	 * 该名称是由那个虚拟主机所设置的值决定
	 * @return string
	 */
	public function getServerName() {
		return $this->getServer('SERVER_NAME', '');
	}

	/**
	 * 返回服务端口号
	 *
	 * https链接的默认端口号为443
	 * http链接的默认端口号为80
	 * @return int
	 */
	public function getServerPort() {
		if (!$this->port) {
			$default = $this->isSecure() ? 443 : 80;
			$this->setServerPort($this->getServer('SERVER_PORT', $default));
		}
		return $this->port;
	}

	/**
	 * 设置服务端口号
	 * 
	 * https链接的默认端口号为443
	 * http链接的默认端口号为80
	 * @param int $port 设置的端口号
	 */
	public function setServerPort($port) {
		$this->port = (int) $port;
	}

	/**
	 * 返回浏览当前页面的用户的主机名
	 * 
	 * DNS 反向解析不依赖于用户的 REMOTE_ADDR
	 * 
	 * @return string
	 */
	public function getRemoteHost() {
		return $this->getServer('REMOTE_HOST');
	}

	/**
	 * 返回浏览器发送Referer请求头
	 * 
	 * 可以让服务器了解和追踪发出本次请求的起源URL地址
	 * 
	 * @return string
	 */
	public function getRefererUrl() {
		return $this->getServer('HTTP_REFERER');
	}

	/**
	 * 获得用户机器上连接到 Web 服务器所使用的端口号
	 * 
	 * @return number
	 */
	public function getRemotePort() {
		return $this->getServer('REMOTE_PORT');
	}

	/**
	 * 返回User-Agent头字段用于指定浏览器或者其他客户端程序的类型和名字
	 * 
	 * 如果客户机是一种无线手持终端，就返回一个WML文件；如果发现客户端是一种普通浏览器，
	 * 则返回通常的HTML文件
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return $this->getServer('HTTP_USER_AGENT', '');
	}

	/**
	 * 返回当前请求头中 Accept: 项的内容，
	 * 
	 * Accept头字段用于指出客户端程序能够处理的MIME类型，例如 text/html,image/*
	 * 
	 * @return string
	 */
	public function getAcceptTypes() {
		return $this->getServer('HTTP_ACCEPT', '');
	}

	/**
	 * 返回客户端程序可以能够进行解码的数据编码方式
	 * 
	 * 这里的编码方式通常指某种压缩方式
	 * @return string|''
	 */
	public function getAcceptCharset() {
		return $this->getServer('HTTP_ACCEPT_ENCODING', '');
	}

	/**
	 * 返回客户端程序期望服务器返回哪个国家的语言文档
	 *
	 * Accept-Language: en-us,zh-cn
	 * @return string
	 */
	public function getAcceptLanguage() {
		if (!$this->language) {
			$language = explode(',', $this->getServer('HTTP_ACCEPT_LANGUAGE', ''));
			$this->language = $language[0] ? $language[0] : 'zh-cn';
		}
		
		return $this->language;
	}

	/**
	 * 返回访问IP
	 *
	 * 如果获取请求IP失败,则返回0.0.0.0
	 * @return string
	 */
	public function getClientIp() {
		if ($this->clientIp) {
		    return $this->clientIp;
		}

		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
				foreach ($arr as $ip) {
					$ip = trim($ip);
					if ($ip != "unknown") {
						$this->clientIp = $ip;
						break;
					}
				}
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$this->clientIp = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (isset($_SERVER['REMOTE_ADDR'])) {
				$this->clientIp = $_SERVER['REMOTE_ADDR'];
			} else {
				$this->clientIp = "0.0.0.0";
			}
		} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
			$this->clientIp = getenv("HTTP_X_FORWARDED_FOR");
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$this->clientIp = getenv("HTTP_CLIENT_IP");
		} else {
			$this->clientIp = getenv("REMOTE_ADDR");
		}
		
		preg_match( "/[\\d\\.]{7,15}/", $this->clientIp, $onlineIp);
		$this->clientIp = !empty($onlineIp[0]) ? $onlineIp[0] : '0.0.0.0';
		
		$this->clientIp = preg_replace("/[^0-9\\.]/", '', $this->clientIp);
		
		return $this->clientIp;
	}
	
	/**
	 * 获取控制器识别参数
	 * @return string
	 */
	public function getMCA() {
		return "{$_GET['mod']}.{$_GET['ctl']}.{$_GET['act']}";	
	}
	
	/**
	 * 客户端是否来自微信
	 * @return boolean
	 */
	public function isFromWeixin() {
		if(stripos($this->getUserAgent(), 'MicroMessenger')) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * 转义外部传入变量
	 *
	 * @param array|string $var
	 * @return array|string
	 */
	public static function unquotesGpc($var) {
		if(empty($var)) return $var;
	
		if (is_array($var)) {
			return array_map(array('\core\Request', 'unquotesGpc'), $var);
		}
			
	    if (ini_get('magic_quotes_sybase')) {
			$var = str_replace("''", "'", $var);		
	    } else {
	    	$var = stripslashes($var);
	    }
	    	
		return trim($var);	
	}
	
	/**
	 * 验证检查表单重复提交
	 * 
	 * @return bool false：重复提交，验证不通过；true：验证通过
	 */
	public static function checkRePost() {
		$rePostSessionKey = '^form.post.hash';
		isset($_SESSION[$rePostSessionKey]) || $_SESSION[$rePostSessionKey] = array();
		
		$hash = sprintf('%x', abs(crc32(serialize(array_merge($_GET, $_POST, $_FILES)))));
		$uriHash = sprintf('%x', abs(crc32(\core\App::getInstance()->getRequest()->getRequestUri())));
		
		if(isset($_SESSION[$rePostSessionKey][$uriHash]) && $_SESSION[$rePostSessionKey][$uriHash] == $hash) {
			return false;
		}
		
		$_SESSION[$rePostSessionKey][$uriHash] = $hash;
		
		if(count($_SESSION[$rePostSessionKey]) > 10) {
			array_shift($_SESSION[$rePostSessionKey]);
		}
		
		return true;
	}
}
