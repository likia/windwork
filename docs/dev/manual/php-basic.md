开发基础
==============================
开发人员在使用开发框架前需要了解一些必备基础知识才能更有效的进行开发。


PHP开发者不得不掌握的PHP入门知识
================
开发工具
----------------
* IDE：Zend Studio
* 数据库管理： Navicat for MySQL，也可以用phpmyadmin
* 前端开发： Dreamweaver CS

 安装与配置
----------------
  不建议使用集成安装环境包，自己配置能更好了解php的体系结构。  
  * **内置Web Server：** PHP 5.4.0起提供了一个内置的Web服务器，无需另外安装web服务器，主要用于本地开发使用，不建议线上产品使用。[详情](http://php.net/manual/zh/features.commandline.webserver.php)     
  * **推荐产品环境：** Nginx / PHP(FPM) / MySQL5+  
  * **官方安装文档：** [http://php.net/manual/zh/install.php](http://php.net/manual/zh/install.php)  
  * **安装配置注意：** VC9 版本需要用户系统中安装有 » [Microsoft 2008 C++ Runtime (x86)](http://www.microsoft.com/downloads/details.aspx?FamilyID=9B2DA534-3E03-4391-8A4D-074B9F2BC1BF) 或者 » [Microsoft 2008 C++ Runtime (x64)](http://www.microsoft.com/downloads/details.aspx?FamilyID=BD2A6171-E2D6-4230-B809-9A8D7548C1B6)。 


 运行模型
----------------
* 运行模型，每个请求都是一个独立的PHP进程，两个请求之间会完全隔离，每个请求结束后就释放内容进程运行时占的内存。
* [PHP垃圾回收机制](http://php.net/manual/zh/features.gc.php)  
* 线程安全与非线程安全


必须掌握的语言基础（更多详见官方[语言参考](http://php.net/manual/zh/langref.php)） 
----------------
  * [基本语法](http://php.net/manual/zh/language.basic-syntax.php)
  * [类型](http://php.net/manual/zh/language.types.php)
  * [变量](http://php.net/manual/zh/language.variables.php)
  * [常量](http://php.net/manual/zh/language.constants.php)
  * [表达式](http://php.net/manual/zh/language.expressions.php)
  * [运算符](http://php.net/manual/zh/language.operators.php)
  * [流程控制](http://php.net/manual/zh/language.control-structures.php)
  * [函数](http://php.net/manual/zh/language.functions.php)
  * [类与对象](http://php.net/manual/zh/language.oop5.php)
  * [命名空间](http://php.net/manual/zh/language.namespaces.php)
  * [异常处理](http://php.net/manual/zh/language.exceptions.php)
  * [引用的解释](http://php.net/manual/zh/language.references.php)
  * [预定义变量](http://php.net/manual/zh/reserved.variables.php)
  * 熟悉常用函数库（表单获取、字符串处理、数字、时间日期、Cookie/Session、目录和文件操作、MySQL数据库操作、）
  * PHP单步调试（Zend Debugger + Zend Studio）

安全相关
----------------
  * [PHP安全](http://php.net/manual/zh/security.php)
  * 跨域脚本漏洞 XSS


PHP进阶
----------------
除了了解php的基本语法外，还需要先熟悉如下内容
* PHP编码规范，遵循规范让每个人维护你写的代码都像他们自己写一样，提高协作效率及可维护性。
* 了解php.ini配置，熟悉php各模块的作用
* 了解正则表达式
* 面向对象设计(OOP)、分析(OOD)
* MVC
* [文档注释](http://www.phpdoc.org/docs/latest/index.html)
* [PHPUnit单元测试](https://phpunit.de/manual/current/zh_cn/index.html)  
* 版本管理（git、svn、vss） [git简易指南](http://www.bootcss.com/p/git-guide/)、git官方手册 [英文v2](http://www.git-scm.com/book/en/v2) / [中文v1](http://www.git-scm.com/book/zh/v1)。git客户端集成到
* 了解模板引擎
* 了解PHP性能优化相关知识，如缓存、opcode、分布式、使用性能分析工具做代码（一开始用xdebug+WinCacheGrind，现在用Zend Debugger + Zend Studio）等
* 熟悉JSON、XML数据交换格式


PHP提升
-----------
* 阅读一些设计模式与架构模式的书。
* 学习一些敏捷开发方法（Scrum、XP等）。
* 阅读关于项目管理的书
* 如果有兴趣可以阅读一些产品设计的书，推荐《启示录：打造用户喜爱的产品》
* 了解开发框架。PHP有很多用户量比较多的框架，纯php的CI、CakePHP、Yii、symfony、zf、laravel等、C扩展的有Phalcon、Yaf等。推荐
* 学习MySQL数据库优化，推荐《[[高性能MySQL（第3版）].Baron.Scbwartz等》
* 建议学习前端技术（DIV+CSS、JS、Ajax、jQuery）


>要成为一名专业的php开发工程师，需要学习的东西真不少，如果你只是想混口饭吃，建议换个行业。下定决心了以后坚定的把每块骨头看下来，这些东西看起来很多，沉下心来学了以后发现，一步步就过来了。