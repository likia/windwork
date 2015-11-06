<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

/**
 * 兼容预处理、工具函数，
 * 在这里实现系统用到的可能不存在的函数
 */


/**
 * 将任意维度的数组值进行 htmlspecialchars
 * 
 * @param array|string $data
 * @param array $ignoreKeys 忽略的下标
 * @param string $flag 
 * @return array|string
 */
function htmlspecialcharsDeep($data, $ignoreKeys = array(), $flag = ENT_NOQUOTES){
    if(is_array($data)) {
	 	foreach ($data as $key => $val) {
	 		if (is_array($val) && !in_array($key, $ignoreKeys)) {
	 			$data[$key] = htmlspecialcharsDeep($val, $ignoreKeys);
	 		} else if (!is_array($val)) {
	 			$data[$key]	= htmlspecialchars($val, $flag);
	 		}
	 	} 
    } else {
    	$data = htmlspecialchars_decode($data, $flag);
 		$data = htmlspecialchars($data, $flag);
    }
    
    return $data;
}

/**
 * 密码加密
 * @param string $str 密码明文
 * @param string $salt 每个用户用于验证密码的不同的字符串，生成： sprintf('%x', mt_rand(0x100000, 0xFFFFFF));
 * @return string
 */
function pw($str, $salt) {
	return md5(md5($str). $salt);
}

/**
 * 跨站脚本漏洞过滤，在未使用htmlspecialchars的变量存贮到数据库之前需要进行xss过滤
 * 
 * @param mixed $mixed
 * @return $mixed
 */
function xssFilter($mixed) {
	if(is_array($mixed)) {
		foreach ($mixed as $key => $val) {
			$mixed[$key] = xssFilter($val);
		}
	} elseif(is_scalar($mixed)) {
		$mixed = \core\util\XSS::strip($mixed);
	}
	return $mixed;
}

if(!function_exists('lcfirst')) {
    /**
     * 字符串首字母小写
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}

if (!function_exists('hex2bin')) {
	/**
	 * 十六进制字符串转换成二进制字符串
	 *
	 * @param string $hexData
	 * @return string
	 */
	function hex2bin($hexData) {
		$binData = '';
		for($i=0; $i < strlen($hexData); $i += 2) {
			$binData .= chr(hexdec(substr($hexData, $i, 2)));
		}
		return $binData;
	}
}

if (!function_exists('hash_hmac')) {
	/**
	 * hash_hmac PHP实现
	 *
	 * @param string $algo 使用的哈希计算函数
	 * @param string $data 要哈希的数据
	 * @param string $key  密钥
	 * @param bool $raw_output 是否返回原始二进制数据，为否则返回十六进制小写数据
	 * @return string
	 */
	function hash_hmac($algo, $data, $key, $raw_output = false) {
	    $algo = strtolower($algo);
	    $pack = 'H'.strlen($algo('test'));
	    $opad = str_repeat(chr(0x5C), 64);
	    $ipad = str_repeat(chr(0x36), 64);
	
	    if (strlen($key) > 64) {
	        $key = str_pad(pack($pack, $algo($key)), 64, chr(0x00));
	    } else {
	        $key = str_pad($key, 64, chr(0x00));
	    }
	
	    for ($i = 0; $i < strlen($key) - 1; $i++) {
	        $opad[$i] = $opad[$i] ^ $key[$i];
	        $ipad[$i] = $ipad[$i] ^ $key[$i];
	    }
	
	    $output = $algo($opad.pack($pack, $algo($ipad.$data)));
	
	    return ($raw_output) ? pack($pack, $output) : $output;
	}
}

/**
 * 截取中文字符串
 * 
 * @param string $string
 * @param int $length
 * @param string $add 
 * @return string
 */
function cutstr($string, $length, $add = '...') {
	return \core\Common::substr($string, $length, $add);
}


if(!function_exists('parse_ini_string')){
	/**
	 * 解析ini文件内容
	 * @param string $string
	 * @return array()
	 */
    function parse_ini_string($string) {
        $array = array();

        $lines = explode("\n", $string);
       
        foreach($lines as $line) {
            $statement = preg_match("/^(?!;)(?P<key>[\\w+\\.\\-]+?)\\s*=\\s*(?P<value>.+?)\\s*$/", $line, $match);

            if($statement) {
                $key    = $match['key'];
                $value  = $match['value'];
               
                # Remove quote
                if(preg_match("/^\".*\"$/", $value) || preg_match("/^'.*'$/", $value)) {
                    $value = substr($value, 1, substr($value) - 2);
                }
                
                $array[$key] = $value;
            }
        }
        return $array;
    }
}

/**
 * 获取缩略图的URL，一般在模板中使用
 * @param string|ing $path 图片路径或图片附件id
 * @param int $width
 * @param int $height
 * @return string
 */
function thumb($path, $width = 100, $height = 0) {
	return \core\Factory::storage()->getThumbUrl($path, $width, $height);
}

/**
 * 获取会员头像的url，一般在模板中使用
 * 
 * @param int $uid
 * @param string $type big|medium|small|tiny
 * @param bool $reload 浏览时是否重新加载头像
 * @return string
 */
function avatar($uid, $type = 'small', $reload = false) {
	$urlExt = \core\Config::get('url_rewrite_ext');
	$avatar = url("system.uploader.load/avatar/{$type}/{$uid}.jpg");
	
	$urlExt && $urlExt != '.jpg' && $avatar = \core\Common::rtrimStr($avatar, $urlExt);
	
	if($reload) {
		static $rand = null;
		$rand or $rand = sprintf("%X", mt_rand(0x100000, 0xFFFFFF));
		
		$avatar .= "?".$rand;
	}
	
	return $avatar;
}

/**
 * 生成URL，一般在模板中使用
 * 
 * @param string $uri
 * @param bool $fullUrl = false 是否获取完整URL
 * @return string
 */
function url($uri, $fullUrl = false) {
	return \core\mvc\Router::buildUrl($uri, $fullUrl);
}

if (!function_exists('array_column')) {
	/**
	 * 返回数组中指定的一列
	 * @param array $input 需要取出数组列的多维数组（或结果集）
	 * @param mixed $column_key 需要返回值的列，它可以是索引数组的列索引，或者是关联数组的列的键。 也可以是NULL，此时将返回整个数组（配合index_key参数来重置数组键的时候，非常管用）
	 * @param string $index_key 作为返回数组的索引/键的列，它可以是该列的整数索引，或者字符串键值。
	 * @return array
	 */
     function array_column($input, $column_key, $index_key = null) {
         if ($index_key !== null) {
             $keys = array();
             $i = 0;
             
             foreach ($input as $row) {
                 if (array_key_exists($index_key, $row)) {
                     if (is_numeric($row[$index_key]) || is_bool($row[$index_key])) {
                         $i = max($i, (int) $row[$index_key] + 1);
                     }
                     
                     $keys[] = $row[$index_key];
                 } else {
                     $keys[] = $i++;
                 }
             }
         }
         
         if ($column_key !== null) {
             $values = array();
             $i = 0;
             
             foreach ($input as $row) {
                 if (array_key_exists($column_key, $row)) {
                     $values[] = $row[$column_key];
                     $i++;
                 } elseif (isset($keys)) {
                     array_splice($keys, $i, 1);
                 }
             }
         } else {
             $values = array_values($input);
         }
         
         if ($index_key !== null) {
             return array_combine($keys, $values);
         }
         
         return $values;
     }
}
 
 /**
  * 去掉数组或字符串的html标签
  * @param string $arr
  * @param string $allowTags
  * @return string
  */
 function stripTagsDeep($arr, $allowTags = '') {
 	if (is_array($arr)) {
 		foreach ($arr as $key => $val) {
 			$arr[$key] = stripTagsDeep($val, $allowTags);
 		}
 	} else {
 		$arr = strip_tags($arr, $allowTags);
 	}
 	
 	return $arr;
}

/**
 * 智能日期显示（把时间显示为n小时n分钟前/后，或昨天、前天、年-月-日后面连着时分秒）
 * @todo 在\core\util\DateTime类实现
 * @param int $time 
 * @param string $ymd 年月日显示格式
 * @param string $his 时分秒显示格式，空则不显示
 * @return string
 */
function smartDate($time, $ymd = 'Y-m-d', $his = 'H:i:s') {
	$r = '';
	
	if (abs(time() - $time) < 24*3600) {
		$seconds = time() - $time;
		if($seconds > 0) {
			if ($seconds > 3600) {
				$r .= floor($seconds/3600) . "小时";
			}
			$r .= ceil($seconds%3600/60) . "分钟前";
		} else {
			$seconds =  -$seconds;
			if ($seconds > 3600) {
				$r .= floor($seconds/3600) . "小时";
			}
			$r .=  ceil($seconds%3600/60) . "分钟后";
		}
	} else {
		if (date('Y-m-d', strtotime('-1 day')) == date('Y-m-d', $time)) {
			$r = '昨天';
		} elseif (date('Y-m-d', strtotime('-2 day')) == date('Y-m-d', $time)) {
			$r = '前天';
		} elseif (date('Y-m-d', strtotime('+1 day')) == date('Y-m-d', $time)) {
			$r = '明天';
		} elseif (date('Y-m-d', strtotime('+2 day')) == date('Y-m-d', $time)) {
			$r = '后天';
		} else {
			$r = date($ymd, $time);
		}
		
	    $his && $r .= ' ' . date($his);
	}
	
	return $r;
}

 /**
  * 获取推荐位数据
  * @param int $posid
  * @param string $type
  * @param number $cid
  * @param bool $mustPic 是否必须有封面图片
  * @return Ambigous <multitype:, multitype:string , \module\system\model\PositionModel, \core\cache\mixed>
  */
function rec($posid, $type = '', $cid = 0, $mustPic = false) {
	return \module\system\model\PositionModel::getPositionData($posid, $type, $cid, $mustPic);
}

/**
 * 获取推荐位数据列表
 * @param int $posid 推荐位id
 * @param string $type 推荐位类型，同模块文件夹名,如文章：article；景点：sight；酒店：hotel
 * @param number $cid 分类id，0是全部分类，可以是通过数组获取到包括子分类的内容，如 $cat['descendantIdArr'] 获取当前栏目及所有级别的子栏目的id数组
 * @param number $rows 返回行数，不能多于推荐位设置的行数
 * @param bool $mustPic 是否必须有图片
 * @return array
 */
function recDataList($posid, $type = '', $cid = 0, $rows = 0, $mustPic = false) {
	$list = array();
	$rec = rec($posid, $type, $cid, $mustPic);
	if($rows && $rows < count($rec['data'])) {
	    $res = array_chunk($rec['data'], $rows);
	    $list = $res[0];
	} else {
		$list = $rec['data'];
	}
	
	return $list;
}


/**
 * 根据上传文件的Path获取完整URL
 * @param string $path
 * @return string
 */
function pathToUrl($path) {
	return \core\Factory::storage()->getFullUrl($path);
}

/**
 * 用于判断两个action字符串是否相等
 *
 * @param string $str
 * @param string $expect
 * @return boolean
 */
function isActionEqual($str, $expect) {
	return strtolower($str) == strtolower($expect);
}

/**
 * 对请求URL进行解码
 * @param string $str
 * @return string||array
 */
function paramDecode($arg) {
	if (is_array($arg)) {
		foreach ($arg as $key => $val) {
			$arg[$key] = paramDecode($val);
		}
	} else {
	    $arg = urldecode(urldecode($arg));
	}
	return $arg;
}

/**
 * 对请求URL进行编码
 * @param string $arg
 * @return string
 */
function paramEncode($arg) {
	if (is_array($arg)) {
		foreach ($arg as $key => $val) {
			$arg[$key] = paramEncode($val);
		}
	} else {
	    $arg = urlencode(urlencode(paramDecode($arg)));
	}
	return $arg;
}

/**
 * 写入日志
 * 
 * 可以在config/config.php中启用日志，所有日志按类别保存
 * @param string $level 日志级别 emergency|alert|critical|error|warning|notice|info|debug|exception
 * @param string $message
 */
function logging($level, $message) {
	if (!is_scalar($message)) {
		$message = var_export($message, 1);
	}
	\core\Factory::logger()->log($level, $message);
}

/**
 * 获取防跨站请求伪造验证的令牌
 * @return string
 */
function csrfToken() {
	return \core\util\XSS::csrfToken();
}


/**
 * 运行时间(ms)评估、内存使用量(M)记录
 * @param string $pointKey = 'start' 当前运行标注点名称
 * @param bool $retThisOnly = false 是否只返回当前点结果
 * @return array
 */
function benchmark($pointKey = '', $retThisOnly = false) {
	static $marker = array();

	$pointKey = strtolower($pointKey);

	// 初始化第一个点
	if (empty($marker) || $pointKey == 'start') {
		$marker['start'] = array(
			'time' => microtime(true),
			'mem' => round(memory_get_usage(true)/(1024*1024), 4),// by M
		);
	}

	if ($pointKey && $pointKey != 'start') {
		$marker[$pointKey] = array(
			'time' => microtime(true),
			'mem' => round(memory_get_usage(true)/(1024*1024), 4),// by M
		);

		// 从开始到当前点执行时间
		$marker[$pointKey]['elapsed'] = $marker[$pointKey]['time'] - $marker['start']['time'];
	}

	return $retThisOnly ? $marker[$pointKey] : $marker;
}

