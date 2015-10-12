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
 * Xml操作，基于SimpleXML
 *
 * @package     core.util
 * @author      cmm <cmm@windwork.org>
 * @since       1.0.0
 */
class XML {
	/**
	 * 数组或对象生成 SimpleXMLElement对象实例
	 *
	 * @param mixed $object variable object to convert
	 * @param string $root root element name
	 * @param object $xml xml object
	 * @param string $unknown element name for numeric keys
	 * @param string $doctype XML doctype
	 * @return \SimpleXMLElement
	 */
	public static function make($object, $root = 'data', $xml = null, $unknown = 'element', $doctype = "<?xml version='1.0' encoding='utf-8'?>\n") {
		if(is_null($xml)) {
			$xml = new \SimpleXMLElement("$doctype<$root/>");
		}

		foreach((array) $object as $k => $v) {
			if(is_numeric($k)) {
				$k = $unknown;
			}

			if(is_scalar($v)) {
				$xml->addChild($k, htmlspecialchars($v, ENT_QUOTES, 'utf-8'));
			} else {
				$v = (array) $v;
				$node = is_numeric($k) ? $xml : $xml->addChild($k);
				self::make($v, $k, $node);
			}
		}

		return $xml;
	}

}
