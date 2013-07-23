---
layout: post
category : data 
tags : [data]
---
{% include JB/setup %}


scribe 真是一个很奇怪的东西，特别是依赖了一个我非常讨厌的 boost库, 怎么样安装文章很多，我的是redhat系统，所有的东西都是自己编译的  

总结如下

1、thrift最好选择 0.7版本，不然问题会很多  

2、boost最好选择 1.4.7以下的本版本，如果选择了 1.4.7，麻烦肯定就来了  

3、scribe 直接从 github上面 clone就可以了，保证是最新的  

5、如果安装 boost的时候，指定了 prefix ，一定要在 /etc/ld.so.conf中把 boost的lib目录添加到里面  

##config参数 

    ./configure --prefix=/usr/local/webserver/scribe --with-boost=/usr/local/webserver/boost45/ --with-thriftpath=/usr/local/webserver/thrift-0.7.0/ --with-fb303path=/usr/local/webserver/fb303-0.7/ CPPFLAGS="-DHAVE_INTTYPES_H -DHAVE_NETINET_IN_H -I/usr/local/webserver/libevent-1.4.13/include -I/usr/local/webserver/boost45/include" LDFLAGS="-L/usr/local/webserver/libevent-1.4.13/lib -L/usr/local/webserver/boost45/lib"

##真的开源吗
从github上面来看，很久没人update 了，并且facebook这文档写的很少，并且一些安装方式还是错误的  


[项目主页](https://github.com/facebook/scribe)
