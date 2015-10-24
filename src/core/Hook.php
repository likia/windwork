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
 * 组件调用机制
 * 
 * 提供一种机制在不需要修改框架代码的情况下来扩展系统。
 * <pre>
 * 1、在config/config.php中启用钩子 hook_enabled => 1
 * 2、在config/hoocks.php设置钩子
 *   配置规则
 *   方式1：钩子类名或钩子类的实例,如：'\\user\\hook\\Acl', new \module\user\hook\Acl()
 *   方式2：钩子类名或钩子类的实例+数组参数,如：array('\\user\\hook\\Acl', array($param1, $param2, ....)), array(new \module\user\hook\Acl(), array($param1, $param2, ....))
 * 3、钩子类必须实现 \core\IHook接口
 * 4、钩子在框架中调用的位置有： 
 *   1)start_new_app 创建App实例前触发的钩子，只执行一次； 注意：这里的钩子在框架初始化前执行，因此不能调用框架的各种功能。
 *   2)start_init_runtime 加载完系统配置后,初始化运行时触发的钩子，目的是增加修改运行时环境选项。只在创建App单例时执行一次，框架仅初始化了request、response、自动加载、默认异常处理，其他库不可用；
 *   3)end_new_app 创建App实例后触发的钩子，只执行一次； 
 *   4)start_new_controller 初始化控制器实例前触发的钩子
 *   5)start_action 执行action前触发的钩子
 *   6)end_action 执行action后触发的钩子
 *   7)start_output 内容输出前触发的钩子，可对输出内容进行处理过滤
 *   8)end_app 程序执行完后触发的钩
 * 5、你也可以在自己开发的模块中加入钩子调用点
 * </pre>
 * 
 * @package     core
 * @author      cmm <cmm@windwork.org>
 * @link        http://www.windwork.org/manual/core.hook.html
 * @since       1.0.0
 */
class Hook {
	/**
	 * 是否启用钩子
	 *
	 * @var	bool
	 */
	public static $enabled = false;
	
	/**
	 * 钩子注册的类列表，从config/hoocks.php加载
	 *
	 * @var	array
	 */
	public static $hooks = array();
	
	/**
	 * 钩子实例
	 *
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * 是否正在执行钩子，防止重复执行
	 * @var bool
	 */
	private static $isInProgress = false; 

	/**
	 * 初始化注册列表
	 *
	 * @return void
	 */
	public static function init() {
		if (false == Config::get('enable_hook')) {
			return;
		}		
		
		// 按运行环境加载钩子
		if (file_exists($envHookCfg = SRC_PATH.'config/'.Config::get('env').'/hooks.php')) {
			$hooks = include $envHookCfg;
		} else if (file_exists($hookCfg = SRC_PATH.'config/hooks.php')) {
			// 加载默认钩子
			$hooks = include $hookCfg;
		}
		
		if (empty($hooks) || !is_array($hooks)) {
			return;
		}
		
		static::$hooks =& $hooks;
		
		static::$enabled = true;
	}

	/**
	 * 获得指定扩展点的全部扩展调用
	 *
	 * @param string $registerKey
	 * @return array
	 */
	public static function getRegistry($registerKey) {
		if (!isset(static::$hooks[$registerKey])) {
			return false;
		}
		
		return static::$hooks[$registerKey];
	}

	/**
	 * 手动注册钩子
	 *
	 * @param string $registerKey 钩子位置
	 * @param string|object|array $injectHook 注入信息 Hook Class|Hook instance|array('Hook Class', array($param1, $param2, ....)), array(Hook instance, array($param1, $param2, ....))
	 * @return void
	 */
	public static function registerHook($registerKey, $injectHook) {
		static::$hooks[$registerKey][] = $injectHook;
	}

	/**
	 * 执行指定钩子点的所有方法
	 * 
	 * @param string $hookId = '' 指定钩子点
	 * @return bool
	 */
	public static function call($hookId = '') {		
		if(!static::$enabled || !isset(static::$hooks[$hookId])) {
			return false;
		}

		benchmark('start_hook:'.$hookId);
		
		// 执行钩子列表中的钩子
		foreach (static::$hooks[$hookId] as $hook) {
			static::callDetail($hook);
		}

		benchmark('end_hook:'.$hookId);
		
		return true;
	}
	
	/**
	 * 执行钩子注入的具体业务
	 * @param mixed $hook  Hook Class|Hook instance|array('Hook Class', array($param1, $param2, ....)), array(Hook instance, array($param1, $param2, ....))
	 * @throws \core\Exception
	 * @return void|boolean
	 */
	protected static function callDetail($hook) {
		$params = array();
		
		if (is_object($hook)) {
			// 设置值为实例
			if (!$hook instanceof \core\IHook) {
			    throw new \core\Exception(get_class($hook) . '钩子必须实现\core\IHook接口');
			}
			
			$hookObj = &$hook;
		} elseif (is_string($hook)) {
			// 设置值为类名
			$hook = '\\' . ltrim($hook, '\\');
			if (isset(static::$instances[$hook])) {
				// 已创建类实例
				$hookObj = static::$instances[$hook];
			} else {
			    // 未创建类实例
				if(!class_exists($hook)) {
					throw new \core\Exception($hook . '钩子类不存在');
				}
				// 类实例
				$hookObj = new $hook();
				
				if (!$hookObj instanceof \core\IHook) {
				    throw new \core\Exception($hook . '钩子必须实现\core\IHook接口');
				}
				
				static::$instances[$hook] = $hookObj;
			}
		} elseif (is_array($hook)) {			
			if (!isset($hook[0])) {
				throw new \core\Exception($hook . '钩子设置错误');
			}
			
			if (is_object($hook[0])) {
				$hookObj = &$hook[0];
				// 设置值为实例
				if (!$hookObj instanceof \core\IHook) {
					throw new \core\Exception(get_class($hookObj) . '钩子必须实现\core\IHook接口');
				}
			} elseif (is_string($hook[0])) {
				// 设置值为类名
				$hookClass = '\\' . ltrim($hook[0], '\\');
				if (isset(static::$instances[$hookClass])) {
					// 已创建类实例
					$hookObj = static::$instances[$hookClass];
				} else {
				    // 未创建类实例
					if(!class_exists($hookClass)) {
						throw new \core\Exception($hookClass . '钩子类不存在');
					}
					
					// 类实例
					$hookObj = new $hookClass();
					
					if (!$hookObj instanceof \core\IHook) {
					    throw new \core\Exception($hookClass . '钩子必须实现\core\IHook接口');
					}
					
					static::$instances[$hookClass] = $hookObj;
				}				
			} else {
				throw new \core\Exception(var_export($hook, 1) . '钩子设置错误');
			}
			
			isset($hook[1]) && $params = $hook[1];
		}
		
		$hookObj->execute($params);
		
		return true;
	}
}
