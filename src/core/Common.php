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
 * 常用函数 
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.common.html
 * @since       1.0.0
 */
class Common {
	
	/**
	 * 是否启用gz压缩，服务器端支持压缩并且客户端支持解压缩则启用压缩
	 * @return bool
	 */
	public static function isGzEnabled() {
		static $isGzEnabled = null;
		if (null === $isGzEnabled) {
			$isGzEnabled = Config::get('gzcompress')
			  && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false
			  && (
				ini_get("zlib.output_compression") == 1 
				|| in_array('ob_gzhandler', ob_list_handlers())
			  );
		}
		
		return $isGzEnabled;
	}
	
	/**
	 * 默认异常处理
	 *
	 * @param Exception $e 异常对象
	 */
	public static function exceptionHandler($e) {
		$code = $e->getCode();
		$message = $e->getMessage();
		$message = "<b style='color:#F00; font-size:14px; line-height:18px;'>{$message}</b>";
		$file = substr($e->getFile(), strlen(SRC_PATH));
		$file = str_replace('\\', '/', $file);
		$line = $e->getLine();
		$trace = str_replace(str_replace('/', DIRECTORY_SEPARATOR, trim(SRC_PATH, '/')).DIRECTORY_SEPARATOR, '', $e->getTraceAsString());
		$trace = "<pre class=\"error-trace\">{$trace}</pre>\n";
		
		if (in_array($code, array(401, 403, 404))) {
			\core\mvc\Message::setErr($message);
			
			if(Config::get('debug')) {
				\core\mvc\Message::setWarn($trace);
			}
			
			App::getInstance()->dispatch("system.default.error/{$code}");			
			return ;
		}

		if (Config::get('debug')) {
			$message = "<div style=\"color:#666;\">"
			        . "  <b>Exception:</b> ".get_class($e) . "\n<br />"
					. "  <b>Message:</b> {$message}\n<br />"
					. "  <b>File:</b> {$file}\n<br />"
					. "  <b>Line:</b> {$line}</b>"
					. "  {$trace}\n"
	         		. "</div>";
		}
		
		header('Content-Type: text/html; Charset=utf-8');
		print "<div style=\"border: 1px solid #F90; color:#999; padding: 8px 12px; margin:20px 12px; background:#FFFEEE;\">{$message}</div>\n";

		logging('exception', $e->__toString()."\n");
	}
		
	/**
	 * 当前php进程占用的内存（M）, 四舍五入到小数点后4位
	 * 
	 * @return float
	 */
	public static function getMemUsed() {
		if (function_exists('memory_get_usage')) {
			return round(memory_get_usage()/(1024*1024), 4); // by M
		} else {
			return 0;
		}
	}
	
	/**
	 * 把字符串左边给定的字符去掉
	 * 
	 * @param string $string
	 * @param string $needle
	 * @param bool $trimAll 是否把重复匹配的都去掉
	 */
	public static function ltrimStr($string, $needle, $trimAll = false) {
		// 如果字符串左边字符和左边要去掉的字符一致则去掉
		if($needle === substr($string, 0, strlen($needle))){
			$string = substr($string, strlen($needle));
			$trimAll && $string = self::ltrimStr($string, $needle, true);
		}
	
		return $string;
	}
	
	/**
	 * 把字符串右边给定的字符去掉
	 * 
	 * @param string $string
	 * @param string $needle
	 * @param bool $trimAll 是否把重复匹配的都去掉
	 * @return string
	 */
	public static function rtrimStr($string, $needle, $trimAll = false) {
		// 如果字符串右边字符和要去掉的字符一致则去掉
		if($needle === substr($string,  - strlen($needle))){
			$string = substr($string, 0,  - strlen($needle));
			$trimAll && $string = self::rtrimStr($string, $needle, true);
		}
	
		return $string;
	}
	
	/**
	 * 把字符串两头给定的字符去掉
	 * 
	 * @param string $string
	 * @param string $needle
	 * @param bool $trimAll 是否把重复匹配的都去掉
	 * @return string
	 */
	public static function trimStr($string, $needle, $trimAll = true) {
		$string = self::rtrimStr($string, $needle, $trimAll);
		$string = self::ltrimStr($string, $needle, $trimAll);
	
		return $string;
	}
	
	/**
	 * 客户端是否是搜索引擎的爬虫 ，如果是已知爬虫，则返回爬虫类型
	 *
	 * @return bool|string
	 */
	public static function isRobot() {
		static $robot = null;
		if(null !== $robot) {
			return $robot;
		}
		
		$spiders  = 'bot|crawl|spider|slurp|sohu-search|lycos|robozilla';
		$browsers = 'MSIE|Netscape|Opera|Konqueror';		
		$agent    = @$_SERVER['HTTP_USER_AGENT'];
	
		if(preg_match("/($browsers)/i", $agent)) {
			$robot = false;
		} elseif(preg_match("/($spiders)/i", $agent)) {
			$robot = 'UnknowSpider';
			
			$botList = array(
				'GoogleBot',
				'mediapartners-google',
				'BaiduSpider',
				'360Spider',
				'msnbot',
				'bingbot',
				'yodaobot',
				'yahoo! slurp',
				'yahoo! slurp china',
				'iaskspider',
				'Sogou web spider',
				'Sogou push spider',
				'YisouSpider'
			);
			
			foreach ($botList as $bot) {
				if(false !== stripos($agent, $bot)) {
					$robot = $bot;
					break;
				}
			}
		} else {
			$robot = false;
		}
		
		return $robot;
	}
		
	/**
	 * 客户端手机型号
	 * 
	 * @return string|bool
	 */
	public static function checkMobile() {
		static $mobile = null;
		if($mobile !== null) {
			return $mobile;
		}

		$mobile = false;		
		if(empty($_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}
		
		$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
			
		if((strpos($userAgent, 'pad') && false === strpos($userAgent, 'coolpad')) || strpos($userAgent, 'gt-p1000')) {		
			$mobile = false;
		} else {
			$mobileBrowserList = array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
				'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung', 'palmsource',
				'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
				'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
				'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
				'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
				'benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'coolpad', 'MicroMessenger');
			
			foreach($mobileBrowserList as $browser) {
				if (false !== stripos($userAgent, $browser, true)){
					$mobile = $browser;
					break;
				}
			}			
		}
		
		return $mobile;	
	}
	
	/**
	 * 获得浏览器名称和版本
	 *
	 * @return string
	 */
	public static function userBrowser() {
		static $userBrowser = '';
	    if($userBrowser) {
			return $userBrowser;
		}
					
		$agent    = @$_SERVER['HTTP_USER_AGENT'];
		$browser  = '';
		$version  = '';
	
		if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
			$browser = 'Internet Explorer';
			$version = $regs[1];
		} elseif (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'FireFox';
			$version = $regs[1];
		} elseif (preg_match('/Maxthon/i', $agent, $regs)) {
			$browser = '(Internet Explorer ' .$version. ') Maxthon';
			$version = '';
		} elseif (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
			$browser = 'Opera';
			$version = $regs[1];
		} elseif (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
			$browser = 'OmniWeb';
			$version = $regs[2];
		} elseif (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'Netscape';
			$version = $regs[2];
		} elseif (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'Safari';
			$version = $regs[1];
		} elseif (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
			$browser = '(Internet Explorer ' .$version. ') NetCaptor';
			$version = $regs[1];
		} elseif (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
			$browser = 'Lynx';
			$version = $regs[1];
		}
	
		if (!empty($browser)) {
		   return $userBrowser = addslashes($browser . ' ' . $version);
		} else {
			return $userBrowser = 'Unknow Browser';
		}
	}
	
	
	/**
	 * 获得客户端的操作系统
	 *
	 * @return string
	 */
	public static function userOS() {
		static $os = '';
		if ($os) {
		    return $os;
		}
		
		if (empty($_SERVER['HTTP_USER_AGENT'])) {
			return 'Unknown';
		}
	
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$os    = '';
	
		if (strpos($agent, 'win') !== false) {
			if (strpos($agent, 'nt 5.1') !== false) {
				$os = 'Windows XP';
			} elseif (strpos($agent, 'nt 5.2') !== false) {
				$os = 'Windows 2003';
			} elseif (strpos($agent, 'nt 5.0') !== false) {
				$os = 'Windows 2000';
			} elseif (strpos($agent, 'nt 6.0') !== false) {
				$os = 'Windows Vista';
			} elseif (strpos($agent, 'nt') !== false) {
				$os = 'Windows NT';
			} elseif (strpos($agent, 'win 9x') !== false && strpos($agent, '4.90') !== false) {
				$os = 'Windows ME';
			} elseif (strpos($agent, '98') !== false) {
				$os = 'Windows 98';
			} elseif (strpos($agent, '95') !== false) {
				$os = 'Windows 95';
			} elseif (strpos($agent, '32') !== false) {
				$os = 'Windows 32';
			} elseif (strpos($agent, 'ce') !== false) {
				$os = 'Windows CE';
			}
		} elseif (strpos($agent, 'linux') !== false) {
			$os = 'Linux';
		} elseif (strpos($agent, 'unix') !== false) {
			$os = 'Unix';
		} elseif (strpos($agent, 'sun') !== false && strpos($agent, 'os') !== false) {
			$os = 'SunOS';
		} elseif (strpos($agent, 'ibm') !== false && strpos($agent, 'os') !== false) {
			$os = 'IBM OS/2';
		} elseif (strpos($agent, 'mac') !== false && strpos($agent, 'pc') !== false) {
			$os = 'Macintosh';
		} elseif (strpos($agent, 'powerpc') !== false) {
			$os = 'PowerPC';
		} elseif (strpos($agent, 'aix') !== false) {
			$os = 'AIX';
		} elseif (strpos($agent, 'hpux') !== false) {
			$os = 'HPUX';
		} elseif (strpos($agent, 'netbsd') !== false) {
			$os = 'NetBSD';
		} elseif (strpos($agent, 'bsd') !== false) {
			$os = 'BSD';
		} elseif (strpos($agent, 'osf1') !== false) {
			$os = 'OSF1';
		} elseif (strpos($agent, 'irix') !== false) {
			$os = 'IRIX';
		} elseif (strpos($agent, 'freebsd') !== false) {
			$os = 'FreeBSD';
		} elseif (strpos($agent, 'teleport') !== false) {
			$os = 'teleport';
		} elseif (strpos($agent, 'flashget') !== false) {
			$os = 'flashget';
		} elseif (strpos($agent, 'webzip') !== false) {
			$os = 'webzip';
		} elseif (strpos($agent, 'offline') !== false) {
			$os = 'offline';
		} else {
			$os = 'Unknown';
		}
	
		return $os;
	}
	
	/**
	 * 获得用户操作系统的换行符
	 *
	 */
	public static function userCrlf() {
		static $crlf = null;
		if ($crlf !== null) {
			return $crlf;
		}
		
		if(empty($_SERVER['HTTP_USER_AGENT'])) {
			return $crlf = "\n";
		}
		
		if (stristr($_SERVER['HTTP_USER_AGENT'], 'Win')) {
		    $crlf = "\r\n";
		} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
		    $crlf = "\r"; // for old MAC OS
		} else {
		    $crlf = "\n";
		}
		
		return $crlf;
	}
	
	/**
	 * 把全角字符全部换成半角字符再返回
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toSemiangle($str) {
		$arr = array(
		"０" => "0", "１" => "1", "２" => "2", "３" => "3", "４" => "4", "５" => "5",
		"６" => "6", "７" => "7", "８" => "8", "９" => "9", "Ａ" => "A", "Ｂ" => "B",
		"Ｃ" => "C", "Ｄ" => "D", "Ｅ" => "E", "Ｆ" => "F", "Ｇ" => "G", "Ｈ" => "H",
		"Ｉ" => "I", "Ｊ" => "J", "Ｋ" => "K", "Ｌ" => "L", "Ｍ" => "M", "Ｎ" => "N",
		"Ｏ" => "O", "Ｐ" => "P", "Ｑ" => "Q", "Ｒ" => "R", "Ｓ" => "S", "Ｔ" => "T",
		"Ｕ" => "U", "Ｖ" => "V", "Ｗ" => "W", "Ｘ" => "X", "Ｙ" => "Y", "Ｚ" => "Z",
		"ａ" => "a", "ｂ" => "b", "ｃ" => "c", "ｄ" => "d", "ｅ" => "e", "ｆ" => "f",
		"ｇ" => "g", "ｈ" => "h", "ｉ" => "i", "ｊ" => "j", "ｋ" => "k", "ｌ" => "l",
		"ｍ" => "m", "ｎ" => "n", "ｏ" => "o", "ｐ" => "p", "ｑ" => "q", "ｒ" => "r",
		"ｓ" => "s", "ｔ" => "t", "ｕ" => "u", "ｖ" => "v", "ｗ" => "w", "ｘ" => "x",
		"ｙ" => "y", "ｚ" => "z", "（" => "(", "）" => ")", "［" => "[", "］" => "]",
		"【" => "[", "】" => "]", "〖" => "[", "〗" => "]", "「" => "[", "」" => "]",
		"『" => "[", "』" => "]", "｛" => "{", "｝" => "}", "《" => "<", "》" => ">",
		"％" => "%", "＋" => "+", "—" => "-", "－" => "-", "～" => "-", "：" => ":",
		"。" => ".", "、" => ",", "，" => ".", "、" => ".", "；" => ",", "？" => "?",
		"！" => "!", "…" => "-", "‖" => "|", "　" => " ", "＇" => "`", "｀" => "`",
		"｜" => "|", "〃" => "\"", "＂" => "\"");
		return strtr($str, $arr);
	}
		
	/**
	 * 切割字符串
	 *
	 * @param string $string 将切割的字符窜
	 * @param int $length 返回多长的字符窜
	 * @param bool $add 切割后是否在后面添加字符串(...)
	 * @return string
	 */
	public static function substr($string, $length = 0, $add = '') {
		if ($length && strlen($string) > $length) {
			$str = substr($string, 0, $length);
			$hex = '';
			for ($i = strlen($str) - 1; $i >= 0; $i--) {
				$hex .= ' ' . ord($str[$i]);
				$ch = ord($str[$i]);
				if (($ch & 128) == 0)
					return substr($str, 0, $i) . $add;
				if (($ch & 192) == 192)
					return substr($str, 0, $i) . $add;
			}
			return($str . $hex . $add);
		}
		
		return $string;
	}

	/**
	 * 输出js内容
	 *
	 * @param string $js js程序代码
	 * @return string
	 */
	public static function jsScript($js) {
		return "<script type='text/javascript'>{$js}</script>\n";
	}
	
	/**
	 * 加载url的js内容
	 *
	 * @param string $url
	 * @return string
	 */
	public static function jsLocation($url) {
		$url = urldecode($url);
		$url = str_replace("'", "\\'", $url);
		
		return static::jsScript("window.location.href='{$url}'");
	}
	
	/**
	 * 把内容转换成提供js的document.write()使用的字符串
	 * 
	 * @param string $content
	 */
	public static function jsWrite($content) {		
		$search  = array("\r\n", "\n", "\r", "\"", "<script ");
		$replace = array(' ', ' ', ' ', '\"', '<scr"+"ipt ');
        $content = str_replace($search, $replace, $content);
        
		return "document.write(\"{$content}\");\n";
	}

	/**
	 * 生成json
	 * 
	 * @param string $array
	 */
	public static function showJson($array) {
		//if (!is_array($array)){
		//	return;
		//}
		
		$json = json_encode($array);
		
		if (isset($_GET['iframe_callback'])) {
			$callback = preg_replace("/[^0-9a-z_\\.]/i", '', $_GET['iframe_callback']);
			$callback = preg_replace("/^parent\\./", '', $callback);
			header('Content-Type: text/html; Charset=utf-8');
			$json = "<script type=\"text/javascript\">try{parent.{$callback}({$json});}catch(e){}</script>";
		} else {
			header("Content-type: text/javascript; charset=utf-8");
			if(isset($_GET['ajax_callback']) && $_GET['ajax_callback'] != '?') {
				$callback = preg_replace("/[^0-9a-z_\\.]/i", '', $_GET['ajax_callback']);
				
			    $json = "try{{$callback}({$json});}catch(e){}";
			}
		}
		
		print $json;
	}
	
	/**
	 * 替换html标签
	 *
	 * @param string $string
	 * @param string|array $tag
	 * @param string|array $replace
	 * @param string $feed
	 * @return string
	 */
	public static function htmlTagReplace($string, $tag = 'p', $replace = 'div', $feed = '') {
		$tag = (array)$tag;
		$replace = (array)$replace;
		foreach ($tag as $key => $_tag) {
			$string = preg_replace("/<$_tag(.*?)>(.*?)<\/$_tag>/s", "<{$replace[$key]}\\1>\\2</{$replace[$key]}>", $string);
			$string = preg_replace("/<$_tag(.*?)\/>/s", "<{$replace[$key]}\\1/>", $string);
		}
		
		return $string ;	
	}	
	
	/**
	 * 取得随机整数
	 *
	 * @param int $len
	 * @return int
	 */
	public static function randNum($len = 4) {
		$rand = mt_rand(1, 9);
		
		for($i = 0; $i < $len - 1; $i++) {
			$rand .= mt_rand(0, 9);
		}
		
		return $rand;
	}
	
	/**
	 * 取得无序随机字符串
	 *
	 * @param int $len
	 * @param bool $easySee = false 是否是人眼容易识别的
	 * @return int
	 */
	public static function randStr($len, $easySee = false) {
		$s = '0123456789abcdefghijklmnopqrstuvwxyz';
		$se= 'ab23456789abcdefghzykxmnwpqrstuvwxyz'; // 人眼容易识别的字母数字
		
		$easySee && $s = $se;
		
		$r = '';
		for ($i = 0; $i < $len; $i++) {
			$r .= $s[mt_rand(0, 35)];
		}
		
		return $r;
	}
	
	/**
	 * 生成UUID
	 * 
	 * @param int $type = 36 uuid类型 16|32|36  36:标准的UUID；32：没有横杠的32个16进制字符；16：16个16进制字符；
	 */
	public static function guid($type = 36) {
		$pieces = array(
			crc32(uniqid()),
			mt_rand(0x1000, 0xffff),
			mt_rand(0x1000, 0xffff),
			mt_rand(0x1000, 0xffff),
			mt_rand(0x1000, 0xffff),
			mt_rand(0x1000, 0xffff),
		);
		
		// 返回小写的16进制字符串（大写对密集型恐惧症人有更大的压迫感^_^）
		switch ($type) {
			case 16:
				$format = "%08x%x%x";
				break;
			case 32:
				$format = "%08x%x%x%x%x%x";
				break;
			default:
				$format = "%08x-%x-%x-%x-%x%x";
		}

		$uuid = vsprintf($format, $pieces);
		return $uuid;
	}
	
	/**
	 * 获取GD库版本
	 */
	public static function getGDVersion() {
		if(!function_exists('gd_info')) {
			return false;
		}
	
		$gdInfo = gd_info();
		return $gdInfo['GD Version'];
	}
	
	/**
	 * 文件上传的最大大小
	 */
	public static function getUploadMaxSize() {
		$maxSize = ini_get('upload_max_filesize');
		$maxPostSize = ini_get('post_max_size');
		$cfgSize = Config::get('upload_max_size');
		
		$maxSize = $maxSize > $maxPostSize ? $maxPostSize : $maxSize;
		$maxSize = $maxSize > $cfgSize ? $cfgSize : $maxSize;
		
		return $maxSize;
	}


	/**
	 * 多个标签字符串统一格式
	 * @param string $keywords
	 * @return string
	 */
	public static function keywordsFormat($keywords) {
		$keywords = trim($keywords, ', ');
	
		if($keywords) {
			$keywords = htmlspecialchars(strip_tags($keywords)); // 去掉html和特殊符号
			$keywords = preg_replace("/(\\s|\\-|\\||，|、|　)/", ",", $keywords); // 将空格，全角符号转为半角符号
			$keywords = preg_replace("/,+/", ",", $keywords);
				
			// 去掉重复的标签
			$keywords = explode(',', $keywords);
			$keywords = array_unique($keywords);
			$keywords = implode(',', $keywords);
		}
	
		return $keywords;
	}
	/**
	 * 去掉html标签
	 * @param string $string
	 * @return string
	 */
	public static function stripTags($string) {
		$string = strip_tags($string);
		$string = str_replace('&nbsp;', '', $string);
		$string = trim($string);
		
		return $string;
	}
	
	/**
	 * 给内容中的图片添加lazy load
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function addLazyLoadToContentImage($string) {
		if(preg_match_all("/<img\\s.*?>/is", $string, $match)) {
			$match = $match[0];
			$match = array_unique($match);
			$replace = preg_replace("/(class=.*?)[\\s>\\/]/is", '', $match);
			$replace = preg_replace("/\\ssrc=(.*?)/is", " class=\"lazy\" src=\"static/images/lazy-loading.gif\" data-original=$1", $replace);
		
			$string = str_replace($match, $replace, $string);
		}
		
		return $string;
	}
	
	/**
	 * 给内容中的关键词添加锚点
	 * @param string $content
	 * @param string $url
	 * @param string $keywords
	 * @return mixed
	 */
	public static function addAnchor($content, $url, $keywords) {
		$keywords = static::keywordsFormat($keywords);
		if (!$keywords) {
			return $content;
		}
		
		$anchors = explode(',', $keywords);
		
		$content = preg_replace("/(<a.+?>.*?<\\/a>)/ie", "'<[base['.base64_encode('$1').']]>'", $content);
		
		foreach ($anchors as $anchor) {
			if (!$anchor) {
				continue;
			}
			
			if(false === $pos = strpos($content, $anchor)) {
				continue;
			}
						
			$name = urlencode($anchor);
			$replace = "<a href=\"{$url}#{$name}\" name=\"{$name}\" title=\"{$anchor}\">{$anchor}</a>";
			
			$content = substr($content, 0, $pos) . $replace . substr($content, $pos + strlen($anchor));
		}

		$content = preg_replace("/<\\[base\\[(.*?)\\]\\]>/ie", "str_replace('\\\"', '\"', base64_decode('$1'))", $content);
		
		return $content;
	}
	
	/**
	 * 去掉用于url中的字符串中的特殊字符
	 * @param string $slug
	 * @return string
	 */
	public static function stripSlug($slug) {
		$slug = strip_tags($slug);
		$slug = trim($slug);
		$slug = preg_replace("/(\\s+)/", "-", $slug);
		$slug = str_replace(array("\"","'", "\r", "\n", "\032", "/"), "", $slug);
		$slug = urlencode(urldecode(urldecode($slug)));
		$slug = strtolower($slug);
				
		return $slug;
	}
}