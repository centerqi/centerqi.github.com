---
layout: post
category : php
tags : [php]
---
{% include JB/setup %}

今天数据库报联接超时，但是不知道为什么连接超时，并且可爱的op说联接超时，不一定是网络问题，有可能是程序问题。
一定要我把代码给他看

###报警的信息如下

    time[ total:1002 ]
    [Db.php:174 Db::getConn()] logid[138240388911983] url[/abc.do]  Error No: 2002 Error message: Connection timed out Db:tmpdb
    [Db.php:26 Db::query()] logid[138240388911983] url[/abc.do] get connect error
     
一看就是连接超时

###在看php的代码
得到如下信息
1、先调用mysqli_init()初始化一个资源

2、设置 MYSQLI_OPT_CONNECT_TIMEOUT 为1秒(也就是连接超时为1秒)

3、再调用 real_connect进行联接。

以上是php的代码，我们再查，网络超时是仅仅只连接还是包括别的一些操作。

<img src="/assets/images/connect.jpg" />

###mysqli的实现

这php扩展层，做了一次映射(找不到别的形容词，也就是把php的宏替换成mysql认识的宏)MYSQL_OPT_CONNECT_TIMEOUT

<img src="/assets/images/opt.png" />

###mysqlclient的实现
<img src="/assets/images/client_8c_a4a3d9fd5bd870a30257fe1a13fdd4f21_cgraph.png" />

###vio_poll_read
最后会调用 vio_poll_read这个函数
这个函数的最终实现如下
<img src="po11111.jpg" />




[mysql 超时设置](http://www.laruence.com/2011/04/27/1995.html 'mysql 超时设置')

[PHP访问MySQL查询超时处理](http://blog.csdn.net/heiyeshuwu/article/details/5869813 'PHP访问MySQL查询超时处理')

[http://www.sourcecodebrowser.com/mysql-5.1/5.1.47/client_8c.html#a4a3d9fd5bd870a30257fe1a13fdd4f21](http://www.sourcecodebrowser.com/mysql-5.1/5.1.47/client_8c.html#a4a3d9fd5bd870a30257fe1a13fdd4f21 'sourcecodebrowser')




