多语言支持
================
Windwork 通过\core\Lang类实现多语言的支持。

1、语言包文件
---------
语言包文件根据不同语言保存在 src/language文件夹，以数组形式保存。
例如这里有一个中文语言包
```
<?php
// src/language/zh_CN/system.php
return array(
	'system_manage' => '系统管理',
	'trash' => '垃圾',
	'draft' => '草稿',
	'published' => '已发布',
);
```

2、添加语言包
--------------
使用\core\Lang::add('语言包文件名，不带.php');静态方法添加语言包，系统会根据当前选择的语言加载不同语言的语言包。如：
```
\core\Lang::add('system'); // 系统将加载 "src/language/zh_CN/system.php" 语言包
```

3、使用多语言
---------------
### 3.1、在php代码中使用
通过\core\Lang::add($langKey)方法获取语言包中的语言选项字符串；
例如：
```
\core\Lang::add('system_manage'); // 返回 “系统管理” 字符串

```

### 3.2、在视图中使用
视同中使用 lang 标签显示语言包中的语言选项字符串。
例如：
```
{lang system_manage}
```
模板解析后变成

```
<?php echo \core\Lang::add('system_manage');?>

```
最终显示“系统管理”
