消息约定
=====================
我们使用消息管理器来管理消息。
消息类型分有：正确提示ok、警告消息warn、错误消息err。
在程序执行流程中，消息从模型传递到控制器，然后再从控制器设置到消息管理器，视图显示的时候自动从消息管理器获取信息。
消息最终传递到html视图或json数据响应到客户端。

1、模型传递消息到控制器
-----------------
模型只将错误消息传递到控制器，而没有warn和ok消息。
传递方式为：
模型里面有错误的时候，使用$this->setErr(错误信息)设置错误信息并返回false，在控制器中使用 $modelObj->getErrs() 获取模型错误信息。

**消息传递案例**
```
namespace module\user\controller;

use core\Message;

/**
 * 典型的控制器中消息传递的实例
 * 控制器从模型取得错误信息并通过Message::setErr()传到视图
 * 如果没有错误，则传递成功的提示信息到视图
 */
class AccountController extends \core\mvc\Controller {
    public function loginAction() {
        if($this->request->isPost()) {
            $account  = $this->request->getRequest('account');
            $password = $this->request->getRequest('password');

            // 处理用户登录
            $userObj = new \module\user\model\UserModel();
            // 执行业务逻辑
            if(false !== $userObj->login($account, $password)) {
                Message::setOK('登录成功'); // 将登录成功的信息传递到视图
            } else {
                $errs = $userObj->getErrs(); // 从模型取得错误信息
                Message::setErr($errs); // 将模型返回的错误信息传递到视图以响应请求
            }

            // 如果是ajax请求则返回JSON数据格式
            if($this->request->isAjax()) {
                //输出消息
                $this->showMessage();
                return;
            }
        }
        // 显示视图，视图自动去获取消息
        $this->view->render();
    }
}
```

2、视图中显示消息
--------------------
```
  <!--{if Message::hasMessage()}-->
    <!--{if Message::hasErr()}-->
    <div class="section prompt err">
      <div class="section-hd"><span class="l"></span><span class="r"></span></div>
      <div class="section-bd">
        <div class="box box-error-msg">
            <ol class="">
                <!--{loop Message::getErrs() $k $err}-->
                <li>{$err}</li>
                <!--{/loop}-->
            </ol>
        </div>
      </div>
      <div class="section-ft"><span class="l"></span><span class="r"></span></div>
    </div>
    <!--{/if}-->
    
    <!--{if Message::hasWarn()}-->    
    <div class="section prompt warn">
      <div class="section-hd"><span class="l"></span><span class="r"></span></div>
      <div class="section-bd">
        <div class="box box-warning">
            <ol>
                <!--{loop Message::getWarns() $warn}-->
                <li>{$warn}</li>
                <!--{/loop}-->
            </ol>
        </div>
      </div>
      <div class="section-ft"><span class="l"></span><span class="r"></span></div>
    </div>
    <!--{/if}-->
    
    <!--{if Message::hasOK()}-->
    <div class="section prompt ok">
      <div class="section-hd"><span class="l"></span><span class="r"></span></div>
      <div class="section-bd">
        <div class="box box-success">
            <ol>
                <!--{loop Message::getOKs() $ok}-->
                <li>{$ok}</li>
                <!--{/loop}-->
            </ol>
        </div>
      </div>
      <div class="section-ft"><span class="l"></span><span class="r"></span></div>
    </div>
  <!--{/if}-->
```

3、控制器返回JSON消息
-------------------
控制器调用$this->showMessage();的时候，如果$_GET['ajax'] == true 或 $_GET['ajax_callback'] == true 或客户端使用XmlHttpRequest则自动响应json格式数据。
如果需要传递其它消息，则统一将返回消息设置在message下标中的值。
```
class AccountController extends \core\mvc\Controller {
    public function loginAction() {
        Message::setErr('err 1');
        Message::setErr('err 2', 302); // 设置消息下标为302
        Message::setWarn('warn message');
        Message::setOK('ok message');
        // 如果是ajax请求则返回JSON数据格式
        if($this->request->isAjaxRequest()) {
            //输出消息
            $this->showMessage('other message'); // 参数设置其他消息，类型可以是标量或数组
            return;
        }
    }
}
```
消息最终输出为
```
{"err":{"0":"err 1","302":"err 2"},"ok":["ok message"],"warn":["warn message"],"message":"other message"}
```
err、ok、warn消息值都是字符串数组

4、模型中的消息
-----------------
模型中只需将业务逻辑错误的信息传递给调用者，没有warn和ok信息。
模型中执行业务逻辑的时候，如果产生错误，则设置错误消息，并返回false。控制器发现返回只false的时候，再去主动去获取错误信息。
案例：
```
class AccountController extends \core\mvc\Controller {
    public function loginAction() {
        // 使用模型
        $userObj = new \module\user\model\UserModel();

        // 执行模型业务逻辑
        if(false !== $userObj->doLogin($params)) {
            Message::setOK('登录成功！');
        } else {
            //从模型获取错误信息并传递给消息管理器
            Message::setErr($userObj->getErrs());
        }
        $this->showMessage();
    }
}
```
