<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\wx\model;

/**
 * 微信自定义菜单编码
 *
 */
class DiyMenuEncoder {
	private static function escape($str) {
		return  "\"" . preg_replace("/([\\b\\t\\n\\r\\f\"\\'])/", "\\\\\\1", $str) . "\"";
	}

	private static function escapeValue($value, $indent) {
		$out = '';
		if (is_object($value) || is_array($value)) {
			$out .= "\n";
			$out .= static::toJson($value, $indent + 1);
		} else if (is_bool($value)) {
			$out .= $value ? 'true' : 'false';
		} else if (is_null($value)) {
			$out .= 'null';
		} else if (is_numeric($value)) {
			$out .= $value;
		} else if (is_string($value)) {
			$out .= static::escape($value);
		} else {
			$out .= $value;
		}

		$out .= ",\n";

		return $out;
	}

	/**
	 * 对微信自定义菜单进行编码
	 *
	 * @param mixed $in
	 * @param number $indent
	 * @return string
	 */
	private static function toJson($in, $indent = 0) {
		$out = '';

		if (is_scalar($in)) {
			return rtrim(static::escapeValue($in, $indent), ",\n");
		}

		foreach ($in as $key => $value) {
			$out .= str_repeat("\t", $indent + 1);
			if(!(is_numeric($key) && is_object($value))) {
				$out .= static::escape((string)$key) . ": ";
			}
			$out .= static::escapeValue($value, $indent);
		}

		$out = rtrim($out, ",\n");

		if(is_array($in)) {
			$out = str_repeat("\t", $indent) . "[\n" . $out;
			$out .= "\n" . str_repeat("\t", $indent) . "]";
		} else {
			$out = str_repeat("\t", $indent) . "{\n" . $out;
			$out .= "\n" . str_repeat("\t", $indent) . "}";
		}

		return $out;

	}

	/**
	 * 编码成微信公众号自定义菜单JSON格式
	 * @param array $arr
	 * @return string
	 */
	public static function encode($arr) {
		$menu = static::format($arr);
		$menu = static::toJson($menu);

		return $menu;
	}

	/**
	 * 将树形结构菜单格式化成符合微信自定义菜单的数组数据
	 * @param array $in
	 * @return Ambigous <multitype:multitype: , StdClass>
	 */
	private static function format($in) {
		$menuButton = array();
		foreach ($in as $item) {
			if (!$item['isTop']) {
				continue;
			}
				
			$button = array(
				'name' => $item['name'],
			);
				
			if ($item['chileArr']) {
				$button['sub_button'] = array();
				foreach ($item['chileArr'] as $subBtnId) {
					$subButton = array();

					if(empty($in[$subBtnId])) {
						continue;
					}
					$subItem = $in[$subBtnId];
					$subButton['name'] = $subItem['name'];
					$subItem['keyword'] = trim($subItem['keyword']);
					if (empty($subItem['url'])) {
						$subButton['type'] = 'click';
						$subButton['key']  = $subItem['keyword'] ? $subItem['keyword'] : $subItem['name'];
					} else {
						$subButton['type'] = 'view';
						$subButton['url']  = $subItem['url'];
					}
						
					$button['sub_button'][] = (object)$subButton;
				}
			} else {
				if (empty($item['url'])) {
					$button['type']     = 'click';
					$subItem['keyword'] = trim($item['keyword']);
					$button['key']      = $item['keyword'] ? $item['keyword'] : $item['name'];
				} else {
					$button['type'] = 'view';
					$button['url']  = $item['url'];
				}
			}

			$menuButton[] = (object)$button;
		}

		return (object)array('button' => $menuButton);
	}
}


