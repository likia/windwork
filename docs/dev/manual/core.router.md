路由（Router）
====================
路由是一个把请求URL映射到控制器的action上的工具，确定用户的请求该执行那个功能（或者反过来，某个功能应该用哪个URL来访问到）。
路由从URL中解释并提取出这次请求的参数: 模块(module，简写mod)控制器(controller，简写ctl), 动作(action，简写act)以及其他参数。


约定
--------------
 * 系统使用单入口模式，所有Web动态请求均通过 index.php 访问；
 * 模块文件夹放在src/module文件夹中，每个模块为一个文件夹；
 * 控制器类放在src/module/$mod/controller文件夹中；
 * Router根据URL请求参数把请求映射到对应的控制器，执行业务逻辑。


请求处理流程
------------------
第1步、我们在浏览器中打开URL： http://localhost/windwork/index.php?system.default.index 

第2步、路由器解析请求URL中的参数：模块（module）、控制器（controller）、动作（action）和请求中的其他参数。这三个参数结果分别为
```
$mod = 'system';
$ctl = 'default';
$act = 'index';
```
即请求url可表示为： http://localhost/windwork/index.php?{$mod}.{$ctl}.{$act}

第3步、框架执行\module\\**{$mod}**\controller\\**{$ctl}**Controller::**{$act}**Action()方法，
即执行了 \module\\**system**\controller\\**Default**Controller::**index**Action()



URL映射到控制器action
-----
###1、基本URI
* 请求URI格式为：{$baseUrl}{$mod}.{$ctl}.{$act}
* 如我们要访问 \user\controller\AccountController::loginAction()，链接则为：http://localhost/windwork/index.php?user.account.login
* 当启用URL Rewrite的时候， $baseUrl = ''; 否则$baseUrl = 'index.php?';
* 如果启用了url重写，则请求URL为http://localhost/windwork/user.account.login
* 我们可以给router设置把user.account.login简写为login，URL就可以是 http://localhost/windwork/login


###2、往action方法传递函数参数
请求URI格式为：{$baseUrl}{$mod}.{$ctl}.{$act}/参数1/参数2/…

```
namespace module\user\controller;
/**
 * 传action参数
 */
class AccountController extends \core\mvc\Controller {
    /**
     * 传参案例
     */
    public function doAction($p1 = '', $p2 = ''){
        // index.php?user.account.do/aa
        // $p1 == 'aa'; $p2 == '';

        // index.php?user.account.do/aa/bb
        // $p1 == 'aa'; $p2 == 'bb';

        // 我们还可以传更多的参数
        // index.php?user.account.do/aa/bb/cc/dd....
    }
}
```

###3、传递URL请求变量($_GET)到控制器

请求URI格式为：{$baseUrl}{$mod}.{$ctl}.{$act}/param1/param2/下标1:值1/下标2:值2/…
```
namespace user\controller;

/**
 * 传URL请求变量
 */
class AccountController extends \core\mvc\Controller {
    /**
     * 传变量演示说明
     */
    public function loginAction($param = ''){
        // index.php?user.account.login/name:demo/password:123456
        $param == '';
        $a = $this->request->getRequest('name');   // $a == 'demo'
        $b = $this->request->getRequest('password'); // $b == '123456'

        // index.php?user.account.login/vip/name:demo2/password:abc
        $param == 'vip';
        $a = $this->request->getRequest('name');   // $a == 'demo2'
        $b = $this->request->getRequest('password'); // $b == 'abc'
    }
}
```

分析URL获取参数
-----------
```
$router = new \core\Router();
$url = 'index.php?user.account.login/dopost/name:demo/password:123456';
// 分析URL
$router->parseUrl($url);
// 分析后结果将被保存到params属性，结果为
$router->params == array(
    ['mod'] => 'user',
    ['ctl'] => 'account',
    ['act'] => 'login',
    ['...'] => array (
        [0] => 'dopost',
    ),
    ['name'] => 'demo',
    ['password'] => '123456',
);
```

生成URL
---------------
用\core\Router::buildUrl()生成url，Router会自动把URl转成简写url，如果启用url重写，它也会自动生成重写的url。   


```
/**
 * 生成URL
 * @param string|\core\Router $url
 * @param bool $absolute
 * @return string
 */
public static function buildUrl($url, $absolute = false)
```

也可以用url()函数来生成URL。
```
/**
 * 生成URL
 * 
 * @param string $uri
 * @param bool $fullUrl = 1
 * @return string
 */
function url($uri, $fullUrl = 0) {
	return \core\Router::buildUrl($uri, $fullUrl);
}
```

URL简化
--------------
可以设置简化URL让URL更简洁及更方便搜索引擎收录。
我们通过在系统管理后台进行设置简化URL规则， “登录管理后台 》设置 》链接设置 》 url简写规则” 设置URL简讯规则，每行一个规则，简写词开头，匹配规则在后面，以空格隔开。缩写词不能重复。
可使用的规则例如：
```
规则
demo1  mod.ctl.act/aboutUs/do:1/go:2
demo2  mod.ctl.act/aboutUs
demo3  mod.ctl.act

简化URL对应URL
index.php?demo1  =>  index.php?mod.ctl.act/aboutUs/do:1/go:2
index.php?demo2  =>  index.php?mod.ctl.act/aboutUs
index.php?demo3  =>  index.php?mod.ctl.act
index.php?demo1/a:b/c:d  => index.php?mod.ctl.act/aboutUs/do:1/go:2/a:b/c:d

```

注意
--------
 如果是在命令行下使用，请确定正确设置如下选项：
```
 'host_info'  => '', // 网站网址，如：http://www.yoursite.com:8080
 'base_path'  => '', // 网站根目录，如 /ctx/
 'base_url'   => '', // index.php? || empty string
```

URL重写
---------------
通过URL Rewrite来移除 URL 中的 index.php，使URL看起来更简洁，也便于搜索引擎收录。
通过web服务器的URL重写，把类似http://yoursite.com/mod.ctl.act的链接重写到http://yoursite.com/index.php?mod.ctl.act，从而达到简化URL的效果。
```
#nginx URL重写设置
if (!-e $request_filename) {
	rewrite ^/(.+)$ /index.php?$1 last;
}
```

```
# Apache URL重写设置
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    RewriteRule (.*) index.php [L]
</IfModule>
```

IIS7+安装URL重写模块以后可支持URL重写，IIS7以下的版本需要另外下载安装URL重写控件到IIS。
```
#IIS 7+ URL 重写设置
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <defaultDocument>
            <files>
                <add value="index.php" />
            </files>
        </defaultDocument>
		<security>
		    <requestFiltering>
                <requestLimits maxAllowedContentLength="256000000" />
			</requestFiltering>
		</security>
        <urlCompression doStaticCompression="true" />
        <rewrite>
            <rules>
                <rule name="RewriteUserFriendlyURL1" stopProcessing="true">
                    <match url="^(.*?)$" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php?{R:1}" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

约定：
-----------
 * 默认的URLRewrite仅对index.php入口有效  
