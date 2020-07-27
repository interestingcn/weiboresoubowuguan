# 微博热搜博物馆 2.0

------

![微博热搜博物馆](https://pic4.zhimg.com/v2-6fbff11cb4d1fc143edabcf1e33ede8e_1440w.jpg)

欢迎访问 **微博热搜博物馆** 开源项目仓库。一个开放的微博热搜历史数据检索系统。通过本仓库开源代码可以实现私有化部署。

其主要提供以下核心功能：

> * 根据时间检索热搜内容
> * 根据关键词检索相关内容
> * 检索热搜排名趋势
> * 检索热搜搜索量趋势

相关使用功能介绍请移步  [知乎：分享一个微博热搜历史记录网站 - 微博热搜博物馆]( https://zhuanlan.zhihu.com/p/133192873)  

##  部署说明

### 服务器规格

* CPU ：云服务器不应小于2核心 
* 内存：> 1G
* 存储：> 5GB
* 带宽：> 1Mbps

###  运行环境

* Nginx 
* MySql > 5.5
* PHP  7.2

###  WEB服务器 

* 支持`Pathinfo`方式传递参数
* 启用URL改写功能
* 请将网站根目录设置为 `/项目目录/public `目录下。

URL 改写规则（Nginx）(通用Thinkphp5 URL改写规则)

```
if (!-e $request_filename) { 
        rewrite  ^(.*)$  /index.php?s=$1  last; 
        break;   
														} 
```

###  数据库

在MySql中创建新数据库，导入 `OtherFile/数据库`目录内容。

