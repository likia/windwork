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
 * 数据编码，base64的改进版
 * 把base64中用的/+分别换成_-，并且把=去掉，方便在url中使用
 * 
 * 如果直接用base64编码，在url中传递的时候还需要进行url_encode，如果忘记的时候很可能会引起灾难性的问题。
 * 
 * <pre>
 * Base64编码在URL中的应用
 *     Base64编码可用于在HTTP环境下传递较长的标识信息。例如，在Java Persistence系统Hibernate中，
 * 就采用了Base64来将一个较长的唯一标识符（一般为128-bit的UUID）编码为一个字符串，用作HTTP表单
 * 和HTTP GET URL中的参数。在其他应用程序中，也常常需要把二进制数据编码为适合放在URL（包括隐藏
 * 表单域）中的形式。此时，采用Base64编码不仅比较简短，同时也具有不可读性，即所编码的数据不会被
 * 人用肉眼所直接看到。
 *     然而，标准的Base64并不适合直接放在URL里传输，因为URL编码器会把标准Base64中的“/”和“+”
 * 字符变为形如“%XX”的形式，而这些“%”号在存入数据库时还需要再进行转换，因为ANSI SQL中已将“%”
 * 号用作通配符。
 *     为解决此问题，可采用一种用于URL的改进Base64编码，它不在末尾填充&＃39;&＃39;=&＃39;&＃39;号，
 * 并将标准Base64中的“+”和“/”分别改成了“*”和“-”，这样就免去了在URL编解码和数据库存储时所要作
 * 的转换，避免了编码信息长度在此过程中的增加，并统一了数据库、表单等处对象标识符的格式。
 *     另有一种用于正则表达式的改进Base64变种，它将“+”和“/”改成了“!”和“-”，因为“+”,“*”
 * 以及前面在IRCu中用到的“[”和“]”在正则表达式中都可能具有特殊含义。
 *     此外还有一些变种，它们将“+/”改为“_-”或“._”（用作编程语言中的标识符名称）或“.-”
 * （用于XML中的Nmtoken）甚至“_:”（用于XML中的Name）。
 * </pre>
 *  
 * @package     core.util
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class Encoder {
    /**
     * 编码
     * 将$data用base64编码并将其中的"/","+","="分别换成"_","-",""
     *
     * @param string $string
     * @return string
     */
	public static function encode($string) {
		$string = base64_encode($string);
		$string = rtrim($string, '=');
		$string = strtr($string, '+/', '-_');
		
		return $string;
	}
	
	/**
	 * 解码
	 * 将数据中的|_还原成/+在用base64解码
	 *
	 * @param string $string
	 * @return string
	 */
	public static function decode($string) {
		$string = strtr($string, '-_', '+/');
		$string = base64_decode($string);
		
		return $string;
	}
}