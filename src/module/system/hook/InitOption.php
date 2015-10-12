<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace module\system\hook;

use core\Factory;

/**
 * 从数据库加载配置并缓存
 * @author cm
 *
 */
class InitOption implements \core\IHook {
	
	public function execute($params = array()) {
		$rewriteRules = array();
		
		// 加载保存在数据表中的配置选项，并进行排序
		if (!$options = Factory::cache()->read('system/options')) {
		    $options = \module\system\model\OptionModel::getOptions();
			$sortArr = array(); // 根据值长度进行排序
			
			foreach (\core\Router::$rules as $ruleKey => $ruleVal) {
				$rewriteRules[strtolower($ruleKey)] = $ruleVal;
				$sortArr[strtolower($ruleKey)] = strlen($ruleVal);
			}
		    
			// 加载URLRewrite规则 ，在系统后台设置
			$urlRule = $options['url_rewrite_rule'];
			$urlRule = trim($urlRule);

			if ($urlRule) {
				$urlRule = str_replace("\r\n", "\n", $urlRule);
				$urlRuleArr = explode("\n", $urlRule);
				
				foreach ($urlRuleArr as $rule) {
					$rule = trim($rule);
					if (!$rule) {
						continue;
					}
					
					$rule = preg_replace("/\\s+/", ' ', $rule);
					list($ruleKey, $ruleVal) = explode(' ', $rule);
					
					$rewriteRules[strtolower($ruleKey)] = $ruleVal;
					$sortArr[strtolower($ruleKey)] = strlen($ruleVal);
				}
			}

			// 对重写规则进行排序
			array_multisort($sortArr, SORT_DESC, $rewriteRules);
			$options['url_rewrite_rule_arr'] = $rewriteRules;
			
		    //Factory::cache()->write('system/options', $options, 24*60*60);
		}
		
		// 后台数据库存在的配置项合并到文件配置的配置项
		\core\Config::setConfigs(array_merge($options, \core\Config::getConfigs()));
		\core\Router::$rules = $options['url_rewrite_rule_arr'];
	}

}

