Windwork
===============
Windwork是一个开源的轻量级Web框架。
基于PHP5+MYSQL做为技术架构，以组件工厂为核心，MVC OOP模式开发，采用模块化开发方式。
我们的目的是提供一个简洁易用、易维护、易扩展、松耦合、适应于云应用的PHP开发框架。


环境要求
-------------------
* PHP5.3+ (需启用模块：mbstring、mysql或pdo_mysql、gd2，打开allow_url_fopen)
* MySQL 5.0+

设计目标
-----------------
 * 简单易用（易上手、易维护、易扩展）
 * 性能出色
 * 适合高并发平台开发
 * 组件工厂架构模式
 * 松耦合
 * 组件职责单一
 * OOP
 * MVC
 * 模块化
 * 文档简洁易懂


目录结构说明
-----------------
```
{SRC_PATH}
  |- core
  |    |- adapter
  |    |    |- cache
  |    |    |- captcha
  |    |    |- crypt
  |    |    |- db
  |    |    |- image
  |    |    |- logger
  |    |    |- mailer
  |    |    |- mq
  |    |    |- session
  |    |    |- session
  |    |- mvc
  |    |    |- Controller.php
  |    |    |- Exception.php
  |    |    |- Message.php
  |    |    |- Model.php
  |    |    |- Restful.php
  |    |    |- Template.php
  |    |- sms
  |    |- util
  |    |- wrapper
  |    |- wx
  |    |- App.php
  |    |- Common.php
  |    |- Config.php
  |    |- Exception.php
  |    |- Factory.php
  |    |- File.php
  |    |- Hook.php
  |    |- IHook.php
  |    |- Lang.php
  |    |- Object.php
  |    |- Request.php
  |    |- Router.php
  |    |- Storage.php
  |    |- Version.php
  |    |- Wrapper.php
  |- compat.php
  
```

