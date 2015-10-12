钩子（Hook）
========================
提供一种机制在不需要修改框架代码的情况下来扩展核心框架，改变或增加框架的核心运行功能。

启用钩子
------------------
在 src/config/config.php 中启用钩子
```
hook_enabled => 1
```
设置钩子
------------------
在src/config/hoocks.php设置钩子

钩子业务逻辑实现
------------------
钩子类保存在 "src/module/{$mod}/hook/" 文件夹；
钩子必须实现core\IHook类，钩子管理器将执行钩子实现类中的execute方法。
```
<?php
namespance module\user\hook;
class AclHook implements \core\IHook {
    public function execute($params = array()) {
        // to do sth.
    }
}
```

配置规则
----------------
 * **方式1：**钩子类名或钩子类的实例。如：
```
    'pre_action' => array(
        '\\module\\user\\hook\\AclHook',  // 钩子类名（推荐）
         new \module\user\hook\AclHook2(), // 钩子类的实例
    ),
```

 * **方式2：**钩子类名或钩子类的实例+数组参数。如：
```
    'pre_action' => array(
        // 钩子类名 + 参数（推荐）
        array('\\module\\user\\hook\\AclHook', array('param 1', 'param 2', ....)), 
        // 钩子类的实例 + 参数
        array(new \module\user\hook\AclHook2(), array('param 1', 'param 2', ....)), 
    ),
```

挂钩点
------------------
挂钩点是系统在框架中触发的位置。

 * **start_new_app**   
   创建App实例前触发的钩子，只执行一次； 
   注意：这里的钩子在框架初始化前执行，因此不能调用框架的各种组件功能。

 * **start_init_runtime**   
   加载完系统配置后,初始化运行时触发的钩子，目的是增加修改运行时环境选项。只在创建App单例时执行一次，框架仅初始化了request、response、自动加载、默认异常处理，其他库不可用；

 * **end_new_app**   
   创建App实例后触发的钩子，只执行一次； 

 * **start_new_controller**   
   初始化控制器实例前触发的钩子

 * **end_new_controller**   
   初始化控制器实例后触发的钩子

 * **start_action**   
   action执行前触发的钩子

 * **end_action**   
   action执行后触发的钩子

 * **start_output**   
   内容输出前触发的钩子，可对输出内容进行处理过滤

 * **end_app**   
   程序执行完后触发的钩

自定义挂钩点
--------------
你也可以在自己开发的模块控制器中加入挂钩点
1、先在你的业务代码中加入挂钩点
```
\core\Hook::call('my_hook_call_id'); // my_hook_call_id 为挂载点id
```

2、然后在配置问价中加入钩子调用类
```
  'my_hook_call_id' => {
       '\\module\\mymod\\hook\\MyHook',  // 钩子类名
       new \module\mymod\hook\MyHook2(), // 钩子类的实例
  }
```