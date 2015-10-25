Windwork
===============
Windwork是一个开源的轻量级Web框架。
基于PHP5+MYSQL做为技术架构，以组件工厂为核心，MVC OOP模式开发，采用模块化开发方式。
我们的目的是提供一个简洁易用、易维护、易扩展、松耦合、适应于云应用的PHP开发框架。


环境要求
-------------------
* PHP5.3+ (需启用模块：mysql或pdo_mysql、gd2，打开allow_url_fopen，建议启用mbstring或iconv)
* MySQL 5.0+
* 建议启用URLRewrite

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

文档目录
--------
* [编码规范](general.coding-standard.html)  
* [目录结构](general.folder-structure.html)  
* [MVC](core.mvc.html) 
    * [路由（router）](core.mvc.router.html)   
    * [控制器](core.mvc.controller.html)   
    * [模型](core.mvc.model.html)   
    * [视图](core.mvc.template.html)   
    * [消息传递](core.mvc.message.html)
    * [Restful](core.mvc.restful.html)
* [权限控制](general.acl.html)
* [应用容器](core.app.html) 
    * [自动加载类](core.app.html#autoload) 
* [Common](core.common.html)   
* [配置信息](core.config.html)     
* [文件处理](core.file.html)   
* [钩子（Hook）](core.hook.html)  
* [多语言](core.lang.html)   
* [Object基础类](core.object.html)    
* [客户端请求](core.request.html)    
* [服务器端响应](core.response.html)    
* [附件存贮](core.storage.html)  
* [组件工厂](core.factory.html)   
    * [缓存](core.factory.adapter.cache.html)   
    * [验证码](core.factory.adapter.captcha.html)   
    * [加密解密](core.factory.adapter.crypt.html)   
    * [数据库](core.factory.adapter.db.html)   
    * [图像处理](core.factory.adapter.image.html)   
    * [日志](core.factory.adapter.logger.html)   
    * [邮件发送](core.factory.adapter.mailer.html) 
    * [消息队列](core.factory.adapter.mq.html)   
    * [Session](core.factory.adapter.session.html)   
    * [短信发送](core.factory.adapter.sms.html)        
* [工具类](core.util.html)   
    * [请求客户端](core.util.client.html)     
    * [编码解码器](core.util.encoder.html)     
    * [表单生成](core.util.form.html)   
    * [分页类](core.util.pagination.html)     
    * [树](core.util.tree.html)     
    * [zip压缩](core.util.zip.html)    
    * [zip解压](core.util.unzip.html)       
    * [数据验证](core.util.validator.html)     
    * [XML处理](core.util.xml.html)     
    * [XSS](core.util.xss.html)    

* [开发指南](guide.html)  


Windwork框架开发基础
---------------
使用Windwork框架必须有PHP基础以及一些面向对象、MVC等知识。   
[PHP开发基础学习指南](php-basic.html)