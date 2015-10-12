模板视图
=================
视图从业务逻辑分离，便于前端与程序的分工协作。前端或设计师只需要简单的模板标签语法即可进行模板开发。
视图层将模型化的数据渲染为某种表现形式。负责用它得到的信息生成应用程序需要的任何表现界面。

视图层并非只限于 HTML 或文本格式的数据表现形式，它可以根据需要生成多种多样的格式， 比如视频、音乐、文档或其它任何你能想到的格式。

我们使用模板视图作为视图层。

1、使用模板
------------------
**模板变量赋值：**
```
$this->view->assign(‘变量’, “值”); // 变量名为字符串，值为任意数据类型。
```

**显示模板：**
默认模板文件存放于 src/template/default 文件夹中，
```
$this->view->render($tpl = ‘模板文件’);  
$this->view->render(); // 默认 $tpl = "{$mod}/{$ctl}.{$act}.html"
```
**使用模板案例**
```
class AccountController extends \core\mvc\Controller {
    public function loginAction() {
        // 模板变量赋值
        $this->view->assign('time', time());

        // 显示模板 src/template/default/user/account.login.html
        $this->view->render();

        // 显示模板 src/template/default/user/account.login.m.html
        $this->view->render('user/account.login.m.html');
    }
}
```

2、模板引擎语法
--------------
```
# foreach
  {loop $arr $var}
  {/loop}

  {loop $arr $k $v}
  {/loop}

# if
  {if $a}

  {elseif $b}

  {else}

  {/if}

# for
  {for $a = 0; $a < $x; $a++}

  {/for}

# 变量输出
  {$var}
  {$arr[key]}
  {$arr[key1][key2]}

# 常量输出
  {CONST_XX} // 模板中的常量约定全部为大写

# 调用类、对象、函数并输出返回值
  {fncName()}
  {$obj->method()}
  {\mymod\model\XXModel::fnc()}

# URL
  1)用url标签 {url $mod.$ctl.$act/param1/param2/paramk1:paramv1/...}
  2)用url函数 {url("$mod.$ctl.$act/param1/param2/paramk1:paramv1/...")}

缩略图：
  {thumb $uploadid, $width, $height} // 使用标签
  {thumb($uploadid, $width, $height)}  // 使用函数
  {thumb($uploadid, $width, 0)}  // 高自动
  {thumb($uploadid, 0, $height)}  // 宽自动

用户头像地址：
  {avatar($uid, $type)}  // type： big|medium|small|tiny

语言变量：
  {lang xxx}

执行代码段：
1）{#任意PHP代码段...#}
2）<?php php代码 ?>

模板标签注释
<!--{模板标签}--> 
模板解析时将去掉模板标签两边的html注释变成 {模板标签}
```
