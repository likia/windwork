权限控制列表（ACL）
===================
Windwork使用权限控制列表进行访问权限验证。

身份认证
-----------
通过钩子\module\user\hook\AuthHook执行身份认证。
身份认证业务逻辑在\module\user\model\AclModel::isAccessable()实现，从数据库加载权限控制列表并进行匹配。我们使用白名单的验证方式，只有设置了允许访问的action才能访问，否则不允许访问。

**权限认证规则**
* 模块必须安装并且启用才能访问
* 超级管理员可以访问已安装模块所有功能
* 根据用户组进行权限验证（TODO：根据用户进行权限验证）
* 用户类型分为：member会员；ext扩展用户，可用作编辑，版主，卖家等；admin管理员；
* 用户角色按类型划分
* 管理员、扩展组未审核，验证身份时降级到会员角色进行验证；
* 会员未通过审核，验证身份时降级到游客角色进行验证；

角色类型
---------
系统有5中角色类型：
```
super：超级管理员，
admin：管理员，
ext：扩展用户，
member：会员，
guest：游客
```
其中admin、ext、member角色类型可分别添加不同角色，即添加系统角色时按admin、ext、member三种类型进行分组；并且可初始化角色的权限。

action的允许角色（用户）访问级别分为：
```
4：游客级
3：会员级
2：扩展用户级
1：管理员级
0：超级管理员级
```
访问级别id低的角色继承基本id高的角色的权限，比如管理员有本身的访问权限和超级管理员访问级别外的所有级别的权限，而游客只有本身级别4的访问权限。

超级管理员级具有访问任何功能的权限，安装模块时不需设置相关权限。
安装模块时自动设置admin，ext，member，guest四种角色的访问权限。

模块权限配置
------------
访问权限设置在应用文件夹下的src/module/{$mod}/install/cfg.php中，下标为 ‘acls’ 的数组为权限控制设置，下标为 ‘acts’的则是action功能及其名称的列表。
```
// apps/module/demo/install/cfg.php
return array (
    'acts' => array (
        // \module\demo\controller\AdminController
        'admin' => array (
            'dev'     => '开发者入口',   // AdminController::devAction()
            'index'   => '后台首页',     // AdminController::indexAction()
            'welcome' => '后台欢迎页面', // AdminController::welcomeAction()
        ),
        // 默认功能 \module\demo\controller\DefaultController
        'default' => array (
            'index'   => '默认首页', // DefaultController::indexAction()
            'welcome' => '欢迎页',   // DefaultController::welcomeAction()
            'profile' => 'XX页面'    // DefaultController::profileAction()
        ),
    ),
    'acls' => array (
        // 参数中 0-4 的值设置访问限制的级别
        'admin' => array (
            'dev'     => 0, // AdminController::devAction()只允许超级管理员访问
            'index'   => 1, // AdminController::indexAction()管理员及超级管理员可访问
        ),
        'default' => array (
            'profile' => 3, // DefaultController::profileAction()登录后可访问
            'index'   => 4, // 允许任何人访问
            'welcome' => 4  // 允许任何人访问
        ),
        // 也可以详细设置4个角色类型的权限
        // array(管理员, 扩展用户, 会员, 游客)
        'admin' => array (
            'dev'     => array(0, 0, 0, 0), // AdminController::devAction()只允许超级管理员访问
            'index'   => array(1, 0, 0, 0), // AdminController::indexAction()管理员及超级管理员可访问
            'demo' => array(1, 1, 0, 0)  // AdminController::demoAction()扩展用户可访问
        ),
        'default' => array (
            'profile' => array(1, 1, 1, 0), // DefaultController::profileAction()登录后可访问
            'index'   => array(1, 1, 1, 1), // 允许任何人访问
            'welcome' => '*'  // 允许任何人访问
        ),
    ),
);
```
