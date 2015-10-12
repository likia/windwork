<?php

namespace core\util;

class Utf8 {

	/**
	 * UTF-8 转GB编码
	 *
	 * @param string $utfstr UTF-8字符串
	 * @return string gbk字符串
	 */
	public static function utf82Gbk($utfstr) {
		if(function_exists('iconv')) {
			return iconv('utf-8','gbk//ignore',$utfstr);
		} elseif(function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($utfstr, 'GBK', 'UTF-8');
		}
		
		if(trim($utfstr)=="") {
			return $utfstr;
		}
		
		static $UC2GBTABLE = '';
		if(empty($UC2GBTABLE)) {
			$filename = SRC_PATH."data/gbk-utf8.dat";
			$fp = fopen($filename, "r");
			while(false !== $l = fgets($fp, 15)) {
				$UC2GBTABLE[hexdec(substr($l, 7, 6))] = hexdec(substr($l, 0, 6));
			}
			
			fclose($fp);
		}
		
		$gbkstr = "";
		$ulen = strlen($utfstr);
		
		for($i = 0; $i < $ulen; $i++) {
			$c = $utfstr[$i];
			$cb = decbin(ord($utfstr[$i]));
			if(strlen($cb)==8) {
				$csize = strpos(decbin(ord($cb)),"0");
				for($j=0;$j < $csize;$j++) {
					$i++; $c .= $utfstr[$i];
				}
				$c = self::utf82U($c);
				if(isset($UC2GBTABLE[$c])) {
					$c = dechex($UC2GBTABLE[$c]+0x8080);
					$gbkstr .= chr(hexdec($c[0].$c[1])).chr(hexdec($c[2].$c[3]));
				} else {
					$gbkstr .= "&#".$c.";";
				}
			}
			else {
				$gbkstr .= $c;
			}
		}
		$gbkstr = trim($gbkstr);
		return $gbkstr;
	}
	
	/**
	 * GB转UTF-8编码
	 *
	 * @param string $gbstr gbk字符串
	 * @return string utf-8字符串
	 */
	public static function gbk2Utf8($gbstr) {		
		if(function_exists('iconv')) {
			return iconv('gbk','utf-8//ignore',$gbstr);
		} elseif (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($gbstr, 'UTF-8', 'GBK');
		}
		
		if(trim($gbstr)=="") {
			return $gbstr;
		}
		
		static $GBK2UTF8_CODETABLE = '';
		
		if(empty($GBK2UTF8_CODETABLE)) {
			$filename = SRC_PATH."core/res/gbk-utf8.dat";
			$fp = fopen($filename, "r");
			while (false !== $l = fgets($fp, 15)) {
				$GBK2UTF8_CODETABLE[hexdec(substr($l, 0, 6))] = substr($l, 7, 6);
			}
			fclose($fp);
		}
		
		$ret = "";
		$utf8 = "";
		while ($gbstr != '') {
			if (ord(substr($gbstr, 0, 1)) > 0x80) {
				$thisW = substr($gbstr, 0, 2);
				$gbstr = substr($gbstr, 2, strlen($gbstr));
				$utf8 = "";
				@$utf8 = self::u2Utf8(hexdec($GBK2UTF8_CODETABLE[hexdec(bin2hex($thisW)) - 0x8080]));
				if($utf8 != "") {
					for ($i = 0;$i < strlen($utf8);$i += 3) {
					    $ret .= chr(substr($utf8, $i, 3));
					}
				}
			} else {
				$ret .= substr($gbstr, 0, 1);
				$gbstr = substr($gbstr, 1, strlen($gbstr));
			}
		}
		
		return $ret;
	}
	
	
	/**
	 * Unicode字符集转utf8
	 * 
	 * @param string $c
	 * @return string
	 */
	public static function u2Utf8($c) {
		for ($i = 0;$i < count($c);$i++) {
			$str = "";
		}
		
		if ($c < 0x80) {
			$str .= $c;
		} elseif ($c < 0x800) {
			$str .= (0xC0 | $c >> 6);
			$str .= (0x80 | $c & 0x3F);
		} elseif ($c < 0x10000) {
			$str .= (0xE0 | $c >> 12);
			$str .= (0x80 | $c >> 6 & 0x3F);
			$str .= (0x80 | $c & 0x3F);
		} elseif ($c < 0x200000) {
			$str .= (0xF0 | $c >> 18);
			$str .= (0x80 | $c >> 12 & 0x3F);
			$str .= (0x80 | $c >> 6 & 0x3F);
			$str .= (0x80 | $c & 0x3F);
		}
		
		return $str;
	}
	
	
	/**
	 * utf8字符集转Unicode
	 * 
	 * @param string $c
	 * @return string
	 */
	public static function utf82U($c) {
		switch(strlen($c)) {
			case 1:
				return ord($c);
			case 2:
				$n = (ord($c[0]) & 0x3f) << 6;
				$n += ord($c[1]) & 0x3f;
				return $n;
			case 3:
				$n = (ord($c[0]) & 0x1f) << 12;
				$n += (ord($c[1]) & 0x3f) << 6;
				$n += ord($c[2]) & 0x3f;
				return $n;
			case 4:
				$n = (ord($c[0]) & 0x0f) << 18;
				$n += (ord($c[1]) & 0x3f) << 12;
				$n += (ord($c[2]) & 0x3f) << 6;
				$n += ord($c[3]) & 0x3f;
				return $n;
		}
	}
	
}

