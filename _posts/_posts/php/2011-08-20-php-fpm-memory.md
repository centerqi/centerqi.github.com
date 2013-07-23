---
layout: post
category : php
tags : [php]
---
{% include JB/setup %}

穷人家的孩子只能省吃俭用，最近买了一个vps，内存只有256mb，感谢linux还支持这么小的内存。

上面布了lnmp，因为php是用php-fpm模式，并且是5.3，发现内存基本上是被php-fpm所用，并且用的太多了点，250m左右。

其实主要是调整php/etc/php-fpm.conf文件。

关于php-fpm与内存相关的几个参数

        pm = static|dynamic

        pm.max_children

        pm.start_servers

        pm.min_spare_servers

        pm.max_spare_servers

        pm.max_requests

用过apache的人其实一看就知道这几个参数的意思，如果没用过，其实看英文也知道他能做什么。

刚开始pm默认是dynamic，并且 pm.max_children为50，并且pm.start_servers为25左右、因为每一个cgi会用20mb左右的内存，这就悲剧了，系统没有挂掉已经很不错了。

碰到问题一直没有很好去解决，把max_children和start_servers、min_spare-servers改动过好几次，都是在pm=dynamic这个前提下。

后来发现这样内存很难控制，所以就把pm=static了，设置max_children为6个，内存就在120mb左右，系统可用还用120mb左右。

并且比较稳定。

 

看来php-fpm这些配置是给内存1g以上用户用的，这一点也太难为我们这些穷人家的孩子了。



添加几个case。

[http://serverfault.com/questions/285242/ever-increasing-mem-usage-by-php-fpm](http://serverfault.com/questions/285242/ever-increasing-mem-usage-by-php-fpm)

[http://www.webmaster-forums.net/server-management/php-5-php-fpm-memory-leak](http://www.webmaster-forums.net/server-management/php-5-php-fpm-memory-leak)

[http://www.streamreader.org/serverfault/questions/242610/phpfpm-high-memory-usage](http://www.streamreader.org/serverfault/questions/242610/phpfpm-high-memory-usage)

[http://serverfault.com/questions/173821/nginx-php-5-3-3-with-php-fpm-memory-usage](http://serverfault.com/questions/173821/nginx-php-5-3-3-with-php-fpm-memory-usage)

[http://serverfault.com/questions/242610/php-fpm-high-memory-usage](http://serverfault.com/questions/242610/php-fpm-high-memory-usage)

[http://comdeng.com/blog/article/10007.html](http://comdeng.com/blog/article/10007.html)

[http://hi.baidu.com/tuozhuai/blog/item/c218e3efa84ace3dadafd561.html](http://hi.baidu.com/tuozhuai/blog/item/c218e3efa84ace3dadafd561.html)
