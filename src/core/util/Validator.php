<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace core\util;

/**
 * 验证类
 * 
 * @package     core.util
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class Validator extends \core\Object {
	/**
	 * 批量验证
	 * @param array $data 
	 * @param array $rules 验证规则  array('待验证数组下标' => array('验证方法1' => '提示信息1', '验证方法2' => '提示信息2'), ...)
	 * @param array &$validErrs
	 */
	public static function Validate($data, $rules, &$validErrs) {
		foreach ($rules as $key => $fieldRule) {
			if(empty($data[$key]) && !array_key_exists('notEmpty', $fieldRule)) {
				continue;
			}
			
			foreach ($fieldRule as $method => $msg) {
				$method = trim($method);
				$isNot  = $method[0] == '!';
				$method = str_replace(array('!', ' '), '', $method);
				
				$callback = "static::".(substr($method, 0, 2) == 'is' ? $method : 'is'.ucfirst($method));
				if((!$isNot && !call_user_func($callback, $data[$key])) || ($isNot && call_user_func($callback, $data[$key]))) {
					$validErrs[] = $msg;
				}
			}
		}
		
		return !((bool)$validErrs);
	}
	
	/**
	 * 参数格式是否email格式
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function isEmail($email) {
		return strpos($email, "@") !== false && strpos($email, ".") !== false &&
		    preg_match("/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}\$/i", $email);
	}

	/**
	 * 参数格式是否是时间的格式 Y-m-d H:i:s
	 *
	 * @param string $time
	 * @return bool
	 */
	public static function isTime($time) {
		return preg_match("/[\\d]{4}-[\\d]{1,2}-[\\d]{1,2}\\s[\\d]{1,2}:[\\d]{1,2}:[\\d]{1,2}/", $time);
	}

	/**
	 * 参数是否为空，不为空则验证通过
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function isNotEmpty($var) {
		return !empty($var);
	}

	/**
	 * 参数是否是只允许字母、数字和下划线的字符串
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function isSafeString($var) {
		return preg_match('/^[0-9a-zA-Z_]*$/', $var);
	}

	/**
	 * 参数类型是否是货币的格式 123.45,保留2位小数
	 *
	 * @param string|float $var
	 * @return bool
	 */
	public static function isMoney($var) {
		return preg_match('/^[0-9]*\.[0-9]{2}$/', $var);
	}

	/**
	 * 参数类型是否为IP
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function isIP($var) {
		return ip2long((string)$var);
	}

	/**
	 * 是否是链接
	 * @param string $str
	 * @return number
	 */
	public static function isUrl($str) {
		return preg_match("/^(http|https|ftp):\\/\\/(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}(\\/\\w)?/i", $str);		
	}

	/**
	 * 参数类型是否为数字型
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function isNumber($var) {
		return is_numeric($var);
	}

	/**
	 * 参数类型是否为年的格式(1000-2999)
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isYear($var) {
		return preg_match('/^[12][0-9]{3}$/', $var);
	}

	/**
	 * 参数类型是否为月格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isMonth($var) {
		return preg_match('/^[0-9]{2}$/', $var) && 0 < $var && 12 > $var;
	}

	/**
	 * 参数类型是否为日期的日格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isDay($var) {
		return preg_match('/^[0-9]{2}$/', $var) && 0 < $var && 31 > $var;
	}

	/**
	 * 参数类型是否为时间的小时格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isHour($var) {
		return preg_match('/^[0-9]{2}$/', $var) && 23 > $var;
	}

	/**
	 * 参数类型是否为时间的分钟格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isMinute($var) {
		return preg_match('/^[0-9]{2}$/', $var) && 60 < $var;
	}

	/**
	 * 参数类型是否为时间的秒钟格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isSecond($var) {
		return self::isMinute($var);
	}

	/**
	 * 参数类型是否为星期范围内的值
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isWeek($var) {
		$weeks = array(1, 2, 3, 4, 5, 6, 7, '一', '二', '三', '四', '五', '六', '天', '日', 'monday', 
				'tuesday', 'wednesday', 'thursday', 'Friday', 'saturday', 'sunday', 'mon', 'tue', 
				'wed', 'thu', 'fri', 'sat', 'sun');
		$var = strtolower($var);
		
		return in_array($var, $weeks);
	}

	/**
	 * 参数类型是否为十六进制字符串
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function isHex($var) {
		return preg_match('/^[0-9A-Fa-f]*$/', $var);
	}

	/**
	 * 身份证号码
	 * 可以验证15和18位的身份证号码
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function isIdCard($var) {
		$province = array("11", "12", "13", "14", "15", "21", "22", "23", "31", "32", "33", "34", 
				"35", "36", "37", "41", "42", "43", "44", "45", "46", "50", "51", "52", "53", 
				"54", "61", "62", "63", "64", "65", "71", "81", "82", "91");
		//前两位的省级代码
		if(! in_array(substr($var, 0, 2), $province)) {
			return false;
		}
		
		if(strlen($var) == 15) {
			if(! preg_match("/^\\d+$/", $var)) return false;
			// 检查年-月-日（年前面加19）
			return checkdate(substr($var, 8, 2), substr($var, 10, 2), 
					"19" . substr($var, 6, 2));
		}
		if(strlen($var) == 18) {
			if(! preg_match("/^\\d+$/", substr($var, 0, 17))) {
				return false; // 前17位是否是数字
			}
			//检查年-月-日
			if(! @checkdate(substr($var, 10, 2), substr($var, 12, 2), 
					substr($var, 6, 4))) {
				return false;
			}
			//加权因子Wi=2^（i-1）(mod 11)计算得出
			$Wi_arr = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
			//校验码对应值
			$VN_arr = array(1, 0, 'x', 9, 8, 7, 6, 5, 4, 3, 2);
			//计算校验码总值(计算前17位的，最后一位为校验码)
			for($i = 0; $i < strlen($var) - 1; $i++) {
				$t += substr($var, $i, 1) * $Wi_arr[$i];
			}
			//得到校验码
			$VN = $VN_arr[($t % 11)];
			//判断最后一位的校验码
			if($VN == substr($var, - 1)) {
				return true;
			} else {
				return false;
			}
		}
		
		return false;
	}

	/**
	 * 验证字符串是否是utf-8
	 *
	 * @param string $text
	 * @return bool
	 */
	public static function isUtf8($text) {
		if(strlen($text) == 0) {
			return true;
		}
		
		return (preg_match('/^./us', $text) == 1);
	}
	
	/**
	 * 检查日期格式是否正确
	 * 
	 * @param string $text 日期，如：2011-01-20
	 * @param string $delemiter 日期分隔符
	 */
	public static function isDate($text, $delemiter = '-') {		
    	return preg_match("/[\\d]{4}\\{$delemiter}[\\d]{1,2}\\{$delemiter}[\\d]{1,2}/", $text);
	}
	
	/**
	 * 是否是手机号
	 * 
	 * @param number $mobile
	 * @return bool
	 */
	public static function isMobile($mobile) {
		return preg_match("/^1[3458]{1}[0-9]{9}$/", $mobile) || preg_match("/^17[6-8]{1}[0-9]{8}$/", $mobile);
	}
		
}
